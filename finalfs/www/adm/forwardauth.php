<?php
/**
 * Traefik ForwardAuth med Azure AD (Entra ID) + gruppkrav
 * 
 * ?required_group=Grupp1,Grupp2,Grupp3  → Användaren måste vara medlem i minst EN av grupperna
 */

header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

require_once("./functions/includeDirectory.php");
//includeDirectory("./functions/common");
includeDirectory("./functions/forwardauth");

require('./constants/cookieConfig.php');

session_start([
    'read_and_close'  => false,
    'cookie_domain'   => $cookieConfig['cookieDomain'],
    'cookie_path'     => $cookieConfig['cookiePath'],
    'cookie_secure'   => $cookieConfig['cookieSecure'],
    'cookie_httponly' => $cookieConfig['cookieHttpOnly'],
    'cookie_samesite' => $cookieConfig['cookieSameSite']
]);

$required_groups = $_GET['required_group'] ?? '';

if (!empty($_SESSION['user']['id']) && isset($_SESSION['user']['expires_at'])) {
	if ($_SESSION['user']['expires_at'] > time()) {
	
		// Gruppkontroll (OR-logik)
		if (!empty($required_groups)) {
			$required = array_map('strtolower', array_filter(array_map('trim', explode(',', $required_groups))));
			$user_groups = array_map('strtolower', $_SESSION['user']['groups'] ?? []);

			if (!array_intersect($required, $user_groups)) {
				session_write_close();
				http_response_code(403);
				echo "Åtkomst nekad. Krävs någon av följande grupper: " . htmlspecialchars($required_groups);
				exit(0);
			}
		}
		
		require('./constants/forwardauthSessionConfig.php');
		
		// === SLIDING EXPIRATION ===
		$newExpires = time() + $forwardauthSessionConfig['slideExtension'];
		if ($newExpires > $_SESSION['user']['expires_at']) {
			$_SESSION['user']['expires_at'] = min($newExpires, time() + $forwardauthSessionConfig['absoluteMax']);
		}
		
		session_write_close();

		// Header för Varnish-cache
		$ttl = $_SESSION['user']['expires_at'] - time();
		header('X-Auth-TTL: ' . $ttl);
		
		// Valfria headers att skicka
		//header('X-Forwarded-User: ' . $_SESSION['user']['id']);
		//header('X-Forwarded-Email: ' . ($_SESSION['user']['mail'] ?? ''));
		//header('X-Forwarded-Name: ' . ($_SESSION['user']['name'] ?? ''));

		http_response_code(200);
		exit(0);
	} else {
		// Session har gått ut → rensa den
		unset($_SESSION['user']);
	}
}

if (!empty($_GET['return_to']) && isSafeReturnTo($_GET['return_to'])) {
    $_SESSION['return_to'] = $_GET['return_to'];
} else {
    if (!empty($_GET['return_to'])) {
        error_log("Otillåten return_to: " . $_GET['return_to']);
    }
	$scheme = $_SERVER['HTTP_X_ORIGINAL_PROTO'] ?? $_SERVER['HTTP_X_FORWARDED_PROTO'] ?? 'https';
	$host   = $_SERVER['HTTP_X_ORIGINAL_HOST']  ?? $_SERVER['HTTP_X_FORWARDED_HOST']  ?? $_SERVER['HTTP_HOST'];
	$uri    = $_SERVER['HTTP_X_ORIGINAL_URI']   ?? $_SERVER['HTTP_X_FORWARDED_URI']   ?? $_SERVER['REQUEST_URI'];
    $_SESSION['return_to'] = $scheme . '://' . $host . $uri;
}

$method = $_SERVER['HTTP_X_ORIGINAL_METHOD'] ?? $_SERVER['HTTP_X_FORWARDED_METHOD'] ?? 'GET';
$_SESSION['return_to_was_post'] = ($method === 'POST');

if (empty($_SESSION['oauth2state'])) {
    $authUrl = getAzureAuthUrl(); // sätter $_SESSION['oauth2state']
    $_SESSION['authUrl'] = $authUrl;
} else {
    // Auth-flöde redan igång – återanvänd samma URL och state
    $authUrl = $_SESSION['authUrl'];
}

session_write_close();
header('Location: ' . $authUrl);
http_response_code(302);
exit(0);
