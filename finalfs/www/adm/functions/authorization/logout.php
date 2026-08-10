<?php

	function logout()
	{
		require('./constants/cookieConfig.php');
		$cookieName = $cookieConfig['cookieName'];
		$cookiestr = '';
		$setcookieOptions =
		[
			'expires'  => time()-3600,
			'path'     => $cookieConfig['cookiePath'],
			'domain'   => $cookieConfig['cookieDomain'],
			'secure'   => $cookieConfig['cookieSecure'],
			'httponly' => $cookieConfig['cookieHttpOnly'],
			'samesite' => $cookieConfig['cookieSameSite']
		];
		setcookie(
			$cookieName, 
			$cookiestr, 
			$setcookieOptions
		);
		session_start([
			'read_and_close'  => false,
			'cookie_domain'   => $cookieConfig['cookieDomain'],
			'cookie_path'     => $cookieConfig['cookiePath'],
			'cookie_secure'   => $cookieConfig['cookieSecure'],
			'cookie_httponly' => $cookieConfig['cookieHttpOnly'],
			'cookie_samesite' => $cookieConfig['cookieSameSite']
		]);
		$_SESSION['user'] = false;
		session_write_close();
		echo '<b>Du är nu utloggad!</b><br>';
		displayLogin();
		fastcgi_finish_request();
		exit(0);
	}
