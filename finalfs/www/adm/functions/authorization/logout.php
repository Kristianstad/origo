<?php

	function logout()
	{
		setcookie('origo_user_id', '', time()-3600, '/', '', 0, 1);
		session_start();
		$_SESSION['user'] = false;
		session_write_close();
		echo '<b>Du är nu utloggad!</b><br>';
		displayLogin();
		fastcgi_finish_request();
		exit(0);
	}

?>
