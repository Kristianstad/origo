<?php
	// Tell browsers to not cache response
	header("Cache-Control: must-revalidate, max-age=0, s-maxage=0, no-cache, no-store");
	
	// Expose specific functions
	require_once("./functions/includeDirectory.php");
	
	// Expose all functions in given folders
	includeDirectory("./functions/common");
	includeDirectory("./functions/authorization");

	require('./constants/cookieConfig.php');
	session_start([
		'read_and_close'  => true,
		'cookie_domain'   => $cookieConfig['cookieDomain'],
		'cookie_path'     => $cookieConfig['cookiePath'],
		'cookie_secure'   => $cookieConfig['cookieSecure'],
		'cookie_httponly' => $cookieConfig['cookieHttpOnly'],
		'cookie_samesite' => $cookieConfig['cookieSameSite']
	]);	

	require('./constants/authMethod.php');
	if ($authMethod == 'ldap')
	{
		$dbh=dbh();
		initUserLdap($dbh);
	}

	if (isset($_GET['logout']))
	{
		logout();
	}
	elseif (isset($_GET['displaylogout']) || !isset($_GET['SERVICE']) && !empty($_SESSION['user']))
	{
		displayLogout();
	}
	elseif ($_SERVER["REQUEST_METHOD"] == "POST")
	{
		if ($authMethod != 'ldap')
		{
			$dbh=dbh();
		}
		login($dbh);
	}
	else
	{
		displayLogin();
	}
	if (isset($dbh))
	{
		pg_close($dbh);
	}
	exit(0);
