<?php
	include_once("./functions/adldap/autoload.php");

	function initUser(&$dbh)
	{
		if (isset($_SESSION['user']))
		{
			return false;
		}
		else
		{
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
				session_write_close();
			}
			return true;
		}
	}
?>
