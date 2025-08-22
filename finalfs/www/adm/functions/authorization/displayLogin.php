<?php

	function displayLogin()
	{
		if (!empty($_GET["call"]))
		{
			$getCall=$_GET["call"];
		}
		else
		{
			$getCall="";
		}
		echo <<<HERE
					<form id="normal" class="general" action="./authorization.php" method="post">
						<input class="call" name="call" type="hidden" value="{$getCall}" />
						<table border="0" cellspacing="5" cellpadding="0">
							<tbody>
								<tr>
									<td>Användare:</td>
									<td><input class="text" style="background:#FFFFFF;border-radius:1rem;width:auto;white-space:nowrap;font:12px Segoe UI,Roboto,Helvetica Neue,Arial,sans-serif;" name="user" type="text" /></td>
								</tr>
								<tr>
									<td>Lösenord:</td>
									<td><input class="text" style="background:#FFFFFF;border-radius:1rem;width:auto;white-space:nowrap;font:12px Segoe UI,Roboto,Helvetica Neue,Arial,sans-serif;" name="passwd" type="password" /></td>
								</tr>
								<tr>
									<td></td>
									<td>
										<input id="loginbtn" style="cursor:pointer;background:#eee;border-radius:1rem;border:#eee;width:auto;text-align:center;white-space:nowrap;padding: 0.5rem 0.75rem;font:14px Segoe UI,Roboto,Helvetica Neue,Arial,sans-serif;" title="Submit" alt="Logga in" name="submitButton" type="submit" value="Logga in" class="submit" />
										<input title="Reset" style="cursor:pointer;background:#eee;border-radius:1rem;border:#eee;width:auto;text-align:center;white-space:nowrap;padding: 0.5rem 0.75rem;font:14px Segoe UI,Roboto,Helvetica Neue,Arial,sans-serif;" alt="Rensa" name="reset" type="reset" value="Rensa" />
									</td>
								</tr>
							</tbody>
						</table>
					</form>
		HERE;
		fastcgi_finish_request();
		exit(0);
	}

?>
