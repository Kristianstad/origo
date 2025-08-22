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
	
	session_start(array('read_and_close' => true));
	initUser();
	if (isset($_SESSION['user']) && $_SESSION['user'] !== false)
	{
		ignore_user_abort(true); 
		$dbh=dbh();
		
		$username=$_SESSION['user']['id'];
		$pgNewsArray=pgNewsArray();
		$userNews=userNews($username, $pgNewsArray);
		if (!empty($_GET['newId']))
		{
			$newId=$_GET['newId'];
			$selectedNew= selectNew($userNews, $newId);
		}
		$action=$_GET['action'];
		if ($action == 'list')
		{
			printNewsList($userNews);
		}
		elseif ($action == 'load' && !empty($selectedNew))
		{
			$return=explode(',', $_GET['return']);
			printNews($username, $selectedNew, $return);
		}
		elseif (($action == 'delete' || $action == 'read') && !empty($selectedNew) && !in_array($username, $selectedNew[$action.'s']))
		{
			readDelete($username, $selectedNew, $action);
		}
		elseif ($action == 'subjects')
		{
			printNewsSubjects($username, $userNews);
		}
		elseif ($action == 'unread')
		{
			testUnread($username, $userNews);
		}
		pg_close($dbh);
		ignore_user_abort(false); 
	}
	else
	{
		echo '<b style="color:#000000">Ej inloggad!</b>';
	}
?>
</body>
</html>
