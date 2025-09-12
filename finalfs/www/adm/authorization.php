<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<style>
		<?php require("./styles/authorization.css"); ?>
	</style>
</head>
<body>
<?php
	// Tell browsers to not cache response
	header("Cache-Control: must-revalidate, max-age=0, s-maxage=0, no-cache, no-store");
	
	// Expose specific functions
	require_once("./functions/includeDirectory.php");
	
	// Expose all functions in given folders
	includeDirectory("./functions/common");
	includeDirectory("./functions/authorization");
	
	session_start(array('read_and_close' => true));
	$dbh=dbh();
	initUser($dbh);

	//ini_set('output_buffering', 'off');

	if (isset($_GET['logout']))
	{
		logout();
		exit(0);
	}
	elseif (isset($_GET['displaylogout']) || !isset($_GET['SERVICE']) && !empty($_SESSION['user']))
	{
		displayLogout();
		exit(0);
	}
	elseif ( $_SERVER["REQUEST_METHOD"] == "POST" )
	{
		login($dbh);
		exit(0);
	}
	else
	{
		displayLogin();
		exit(0);
	}
?>
</body>
</html>
