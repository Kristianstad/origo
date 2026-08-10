<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<style>
		<?php require("./styles/news.css"); ?>
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
	includeDirectory("./functions/news");
	
	require './constants/cookieConfig.php';
	require './constants/authMethod.php';

	session_start([
		'cookie_domain'   => $cookieConfig['cookieDomain'],
		'cookie_path'     => $cookieConfig['cookiePath'],
		'cookie_secure'   => $cookieConfig['cookieSecure'],
		'cookie_httponly' => $cookieConfig['cookieHttpOnly'],
		'cookie_samesite' => $cookieConfig['cookieSameSite'],
	// 'read_and_close' => true   ← kan behållas om du vill, men testa först utan
	]);

	$dbh = null;
	if ($authMethod === 'ldap')
	{
		$dbh=dbh();
		initUserLdap($dbh);
	}
	if (isset($_SESSION['user']) && $_SESSION['user'] !== false)
	{
		ignore_user_abort(true); 		
		$username=$_SESSION['user']['id'];
		if ($authMethod !== 'ldap')
		{
			$dbh=dbh();
		}
		$pgNewsArray=pgNewsArray($dbh);
		$userNews=userNews($username, $pgNewsArray);
		if (!empty($_GET['newId']))
		{
			$newId=$_GET['newId'];
			$selectedNew= selectNew($userNews, $newId);
		}
		$action=$_GET['action'];
		if ($action === 'list')
		{
			printNewsList($userNews);
		}
		elseif ($action === 'load' && !empty($selectedNew))
		{
			$return=explode(',', $_GET['return']);
			printNews($username, $selectedNew, $return);
		}
		elseif (($action === 'delete' || $action === 'read') && !empty($selectedNew) && !in_array($username, $selectedNew[$action.'s']))
		{
			readDelete($username, $selectedNew, $action);
		}
		elseif ($action === 'subjects')
		{
			printNewsSubjects($username, $userNews);
		}
		elseif ($action === 'unread')
		{
			testUnread($username, $userNews);
		}
		ignore_user_abort(false); 
	}
	else
	{
		echo '<b style="color:#000000">Ej inloggad!</b>';
	}
	if (isset($dbh) && $dbh)
	{
		pg_close($dbh);
	}
?>
</body>
</html>
