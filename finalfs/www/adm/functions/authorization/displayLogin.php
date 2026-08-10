<?php

function displayLogin()
{
    $return_to = $_GET['return_to'] ?? $_POST['return_to'] ?? '';
    
    // Använd samma säkra kontroll som i login()
    if ($return_to !== '' && !isSafeReturnUrl($return_to)) {
        $return_to = '';
    }

    $getCall = $_GET['call'] ?? '';

    // Escape för HTML-attribut
    $return_to_safe = htmlspecialchars($return_to, ENT_QUOTES, 'UTF-8');
    $getCall_safe   = htmlspecialchars($getCall, ENT_QUOTES, 'UTF-8');

    require './constants/proxyRoot.php';
    $formAction = $proxyRoot . $_SERVER['PHP_SELF'];

    $content = <<<HTML
<form id="normal" class="general" action="{$formAction}" method="post">
    <input class="call" name="call" type="hidden" value="{$getCall_safe}" />
    <input type="hidden" name="return_to" value="{$return_to_safe}" />
    <table border="0" cellspacing="5" cellpadding="0">
        <tbody>
            <tr>
                <td>Användare:</td>
                <td>
                    <input class="text" style="background:#FFFFFF;border-radius:1rem;width:auto;white-space:nowrap;font:12px Segoe UI,Roboto,Helvetica Neue,Arial,sans-serif;" 
                           name="user" type="text" autocomplete="username" />
                </td>
            </tr>
            <tr>
                <td>Lösenord:</td>
                <td>
                    <input class="text" style="background:#FFFFFF;border-radius:1rem;width:auto;white-space:nowrap;font:12px Segoe UI,Roboto,Helvetica Neue,Arial,sans-serif;" 
                           name="passwd" type="password" autocomplete="current-password" />
                </td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <input id="loginbtn" 
                           style="cursor:pointer;background:#eee;border-radius:1rem;border:#eee;width:auto;text-align:center;white-space:nowrap;padding:0.5rem 0.75rem;font:14px Segoe UI,Roboto,Helvetica Neue,Arial,sans-serif;" 
                           title="Submit" alt="Logga in" name="submitButton" type="submit" value="Logga in" class="submit" />
                    <input title="Reset" 
                           style="cursor:pointer;background:#eee;border-radius:1rem;border:#eee;width:auto;text-align:center;white-space:nowrap;padding:0.5rem 0.75rem;font:14px Segoe UI,Roboto,Helvetica Neue,Arial,sans-serif;" 
                           alt="Rensa" name="reset" type="reset" value="Rensa" />
                </td>
            </tr>
        </tbody>
    </table>
</form>
HTML;

    displayWithHtml($content);
}
