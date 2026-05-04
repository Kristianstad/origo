<?php
	include_once("./functions/adldap/autoload.php");

	function initUser(&$dbh)
	{
		if (isset($_SESSION['user']) && isset($_SESSION['login_time_stamp']) && time()-$_SESSION["login_time_stamp"] <36000)
		{
			return false;
		}
		else
		{
			// === SMART COOKIE FÖRLÄNGNING ===
			if (isset($_SESSION['user']['id']) && isset($_COOKIE['origo_user_id'])) {
				
				$cookieName = 'origo_user_id';
				$lifetime   = 60*60*24*30;           // 30 dagar (ändra efter behov)

				// Förläng endast om cookien är mer än halva livslängden gammal
				// (dvs. om den har mindre än 15 dagar kvar)
				$refreshThreshold = $lifetime / 2;   

				// Kolla om cookien har en expires-tid via header (approximativt)
				if (!isset($_COOKIE['origo_user_id_last_refresh']) || 
					(time() - ($_COOKIE['origo_user_id_last_refresh'] ?? 0)) > $refreshThreshold) {
					
					setcookie(
						$cookieName,
						$_COOKIE[$cookieName],
						[
							'expires'  => time() + $lifetime,
							'path'     => '/',
							'domain'   => '',
							'secure'   => true,
							'httponly' => true,
							'samesite' => 'Strict'
						]
					);

					// Sätt en extra cookie så vi vet när vi senast förlängde
					setcookie(
						'origo_user_id_last_refresh',
						time(),
						[
							'expires'  => time() + $lifetime,
							'path'     => '/',
							'domain'   => '',
							'secure'   => true,
							'httponly' => true,
							'samesite' => 'Strict'
						]
					);
				}
			}
			
			if (isset($_COOKIE['origo_user_id']) || isset($_POST['origo_user_id']))
			{
				if (isset($_COOKIE['origo_user_id']))
				{
					$origoUserId=$_COOKIE['origo_user_id'];
				}
				else
				{
					$origoUserId=$_POST['origo_user_id'];
				}
				require("./constants/cookieKey.php");
				//$decoded=base64_decode($_COOKIE['origo_user_id']);
				list($encrypted_data, $iv) = explode('::', base64_decode($origoUserId), 2);
				//$user=trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $cookieKey, $decoded, MCRYPT_MODE_ECB));
				$user = openssl_decrypt($encrypted_data, 'aes-256-cbc', $cookieKey, 0, $iv);
				require("./constants/adldapConfig.php");
				$ad = new Adldap\Adldap();
				$ad->addProvider($adldapConfig);
				$provider = $ad->connect();
				$search = $provider->search();
				$userInfo = $provider->search()->users()->findBy('cn', $user);
				if (empty($userInfo))
				{
					$userInfo = $provider->search()->users()->findBy('samaccountname', $user);
				}
				$name=$userInfo->getDisplayName();
				$email=$userInfo->getEmail();
				$company=$userInfo->getCompany();
				$department=$userInfo->getDepartment();
				require("./constants/adGroupFilter.php");
				$adgroups=array_diff(array_map('strtolower', array_values($userInfo->getGroupNames($recursive = true))), $adGroupFilter);
				session_start();
				$_SESSION["user"]['id']=$user;
				$_SESSION["user"]["mail"]= $email;
				$_SESSION['user']["groups"] = $adgroups;
				$_SESSION["login_time_stamp"] = time();
				session_write_close();
				require("./constants/configSchema.php");
				$adusers=all_from_table($dbh, $configSchema, 'adusers');
				if (isIdUniqueInTable($user, 'aduser_id', $adusers))
				{
					$sql=insertIdSql($user, 'adusers').';';
				}
				else
				{
					$sql='';
				}
				$adgroupsStr='{'.implode(',', $adgroups).'}';
				$sql=$sql."UPDATE $configSchema.adusers SET name = '$name', email = '$email', company = '$company', department = '$department', adgroups = '$adgroupsStr', lastlogin = now() WHERE aduser_id = '$user';";
				$result=pg_query($dbh, $sql);
				if (!$result)
				{
					die("Error in SQL query: " . pg_last_error());
				}
				unset($result);
			}
			else
			{
				session_start();
				$_SESSION["user"]=false;
				$_SESSION["login_time_stamp"] = time();
				session_write_close();
			}
			return true;
		}
	}
