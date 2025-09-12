<?php
	include_once("./functions/adldap/autoload.php");

	function login(&$dbh)
	{
		require("./constants/adldapConfig.php");
		require("./constants/adDomain.php");
		$ad = new Adldap\Adldap();
		$ad->addProvider($adldapConfig);
		$provider = $ad->connect();
		$user = strtolower($_POST['user']);
		$passwd = $_POST['passwd'];
		$authUser=false;
		if (!empty($user) && !empty($passwd))
		{
			$authUser = $provider->auth()->attempt("$user@$adDomain", "$passwd");
		}
		if ($authUser)
		{
			//Key
			require("./constants/cookieKey.php");
			//To Encrypt:
			//$cookiestr = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $cookieKey, $user, MCRYPT_MODE_ECB));
			$iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
			$encrypted = openssl_encrypt($user, 'aes-256-cbc', $cookieKey, 0, $iv);
			$cookiestr = base64_encode($encrypted . '::' . $iv);
			setcookie('origo_user_id', $cookiestr, time()+60*60*24*3650, '/', '', 0, 1);
			$_COOKIE['origo_user_id']=$cookiestr;
			unset($_SESSION["user"]);
			initUser($dbh);
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
							sessionStorage.user_id="{$_SESSION["user"]['id']}";
						</script>
						<b style="color:#023f88">Du är nu inloggad!</b>
						<button id="loginbtn" style="cursor:pointer;background:#eee;border-radius:1rem;border:#eee;width:auto;text-align:center;white-space:nowrap;padding: 0.5rem 0.75rem;font:14px Segoe UI,Roboto,Helvetica Neue,Arial,sans-serif;" type="button" onclick="sessionStorage.removeItem('user_id'); document.location.assign('{$formAction}?logout');">Logga ut</button>
						</br><iframe src="{$src}?action=subjects" style="border:none;width:100%;height:115px;margin-top:5px;margin-bottom:10px"></iframe>
			HERE;
		}
		else
		{
			echo '<b style="color:#ff0000">Felaktig inloggning!</b><br>';
			displayLogin();
		}
		fastcgi_finish_request();
		exit(0);
	}

?>
