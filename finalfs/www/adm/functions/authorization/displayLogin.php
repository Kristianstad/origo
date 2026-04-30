<?php

	function displayLogin()
	{
		// Hämta return_to från GET eller POST
		$return_to = $_GET['return_to'] ?? $_POST['return_to'] ?? '';

		// Validera URL för säkerhet
		if (!empty($return_to) && !filter_var($return_to, FILTER_VALIDATE_URL)) {
			$return_to = '';
		}
		
		$getCall = $_GET["call"] ?? "";
		require('./constants/proxyRoot.php');
		$formAction = $proxyRoot . $_SERVER["PHP_SELF"];

		$content = <<<HERE
					<form id="normal" class="general" action="{$formAction}" method="post">
						<input class="call" name="call" type="hidden" value="{$getCall}" />
						<input type="hidden" name="return_to" value="{$return_to}" />
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

		displayWithHtml($content);
	}
