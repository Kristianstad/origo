<?php
/**
 * Traefik ForwardAuth med AD-grupp-stöd
 * 
 * Används som:
 *   forwardAuth:
 *     address: "http://din-origo-container:80/forwardauth.php?required_group=MinGrupp"
 * 
 * ?required_group=Grupp1,Grupp2  → OR-logik (användaren behöver bara vara medlem i en av dem)
 * Gruppjämförelsen är case-insensitive tack vare strtolower i initUser.php
 */

	// Tell browsers to not cache response
	header("Cache-Control: must-revalidate, max-age=0, s-maxage=0, no-cache, no-store");
	
	require_once("./functions/includeDirectory.php");
	includeDirectory("./functions/common");
	
	session_start();
	unset($_SESSION['user']);
	session_write_close();
	$dbh = dbh();
	initUser($dbh);

	// Extra debug för att se om cookie når fram
	/*if (isset($_COOKIE['origo_user_id']))
	{
		error_log("ForwardAuth: Cookie origo_user_id finns: " . substr($_COOKIE['origo_user_id'], 0, 20) . "...");
	}
	else
	{
		error_log("ForwardAuth: INGEN origo_user_id cookie!");
	}*/

	// Hämta eventuell krävd AD-grupp från Traefik-middleware
	$required_group = $_GET['required_group'] ?? '';

	if (!empty($_SESSION['user']['id']))
	{
		//error_log("ForwardAuth: origo_user_id: " . $_SESSION['user']['id']);
		
		// Grupp-kontroll (använder exakt samma data som initUser.php redan har lagt i sessionen)
		if (!empty($required_group))
		{
			// Gör required-grupper lowercase för att matcha initUser.php
			$requireds    = array_map('strtolower', array_filter(array_map('trim', explode(',', $required_group))));
			$user_groups  = $_SESSION['user']['groups'] ?? [];

			if (!array_intersect($requireds, $user_groups))
			{
				// Användaren är inloggad men saknar rätt grupp → 403
				header('Content-Type: text/plain; charset=utf-8');
				http_response_code(403);
				echo "Åtkomst nekad.\nDu är inte medlem i någon av de kravda AD-grupperna: " 
				     . htmlspecialchars(implode(', ', $requireds));
				exit(0);
			}
		}

		// Allt OK → godkänn begäran
		header('X-Forwarded-User: ' . $_SESSION['user']['id']);
		// Du kan lägga till fler headers här om du vill:
		// header('X-Forwarded-Email: ' . ($_SESSION['user']['mail'] ?? ''));
		// header('X-Forwarded-Groups: ' . implode(',', $_SESSION['user']['groups']));

		http_response_code(200);
		exit(0);
	}
	else
	{
		// Ej inloggad → redirect till inloggningssidan
		$scheme = $_SERVER['HTTP_X_FORWARDED_PROTO'] ?? 'http';
		$host   = $_SERVER['HTTP_X_FORWARDED_HOST']   ?? $_SERVER['HTTP_HOST'];
		$uri    = $_SERVER['HTTP_X_FORWARDED_URI']    ?? $_SERVER['REQUEST_URI'];
		
		$original_url = $scheme . '://' . $host . $uri;
		
		//require('./constants/proxyRoot.php');
		//$login_url    = $proxyRoot . 'authorization.php';
		//$redirect_url = $login_url . '?return_to=' . urlencode($original_url);
		//$current_file_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		$current_file_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : $scheme) . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		$parsed = parse_url($current_file_url);
		$base = rtrim($parsed['scheme'] . '://' . $parsed['host'] . dirname($parsed['path']), '/') . '/';
		$redirect_url = $base . 'authorization-loader.php' . '?return_to=' . urlencode($original_url);
		header('Location: ' . $redirect_url);
		http_response_code(302);
		exit(0);
	}
