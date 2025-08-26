<?php

	function displayLogout()
	{
		$user = $_SESSION["user"]['id'];
		require('./constants/proxyRoot.php');
		$formAction=$proxyRoot.$_SERVER["PHP_SELF"];
		if (basename($formAction) == 'authorization-loader.php')
		{
			$src=dirname($formAction).'/news-loader.php';
		}
		else
		{
			$src='./news.php';
		}
		echo <<<HERE
					<script>
						sessionStorage.user_id="{$user}";
					</script>
					<b style="color:#000000">{$_SESSION['user']['id']} är inloggad! </b>
					<button id="loginbtn" style="cursor:pointer;background:#eee;border-radius:1rem;border:#eee;width:auto;text-align:center;white-space:nowrap;padding: 0.5rem 0.75rem;font:14px Segoe UI,Roboto,Helvetica Neue,Arial,sans-serif;" type="button" onclick="sessionStorage.removeItem('user_id'); document.location.assign('{$formAction}?logout');">Logga ut</button>
					</br><iframe src="{$src}?action=subjects" style="border:none;width:100%;margin-top:5px;margin-bottom:10px"></iframe>
		HERE;
		fastcgi_finish_request();
		exit(0);
	}

?>
