<?php

function displayLogout()
{
    // Säkerställ att användaren faktiskt är inloggad
    if (empty($_SESSION['user']['id'])) {
        displayLogin();
        return;
    }

    $user = htmlspecialchars($_SESSION['user']['id'], ENT_QUOTES, 'UTF-8');

    require './constants/proxyRoot.php';
    require './constants/authMethod.php';

    $formAction = $proxyRoot . $_SERVER['PHP_SELF'];

    if (basename($formAction) === 'authorization-loader.php') {
        $src = dirname($formAction) . '/news-loader.php';
    } else {
        $src = './news.php';
    }

    // Bygg utloggningsknappen endast om authMethod är ldap
    $logoutButton = '';
    if ($authMethod === 'ldap') {
        $logoutButton = <<<HTML
<button id="loginbtn"
        style="cursor:pointer;background:#eee;border-radius:1rem;border:#eee;width:auto;text-align:center;white-space:nowrap;padding:0.5rem 0.75rem;font:14px Segoe UI,Roboto,Helvetica Neue,Arial,sans-serif;"
        type="button"
        onclick="sessionStorage.removeItem('user_id'); document.location.assign('{$formAction}?logout');">
    Logga ut
</button>
HTML;
    }

    $content = <<<HTML
<script>
sessionStorage.user_id = "{$user}";
</script>
<b style="color:#000000">{$user} är inloggad!</b>
{$logoutButton}
<br>
<iframe src="{$src}?action=subjects"
        style="border:none;width:100%;margin-top:5px;margin-bottom:10px"></iframe>
HTML;

    displayWithHtml($content);
}
