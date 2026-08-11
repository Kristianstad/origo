<?php
function logout()
{
    require './constants/cookieConfig.php';

    $cookieName = $cookieConfig['cookieName'];

    // Se till att sessionen är skrivbar
    ensureSessionWritable();

    // === 1. Förstör sessionen ordentligt ===
    $_SESSION = [];

    // Ta bort PHP-session-cookien
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', [
            'expires'  => time() - 42000,
            'path'     => $params['path'],
            'domain'   => $params['domain'],
            'secure'   => $params['secure'],
            'httponly' => $params['httponly'],
            'samesite' => $params['samesite'] ?? 'Lax',
        ]);
    }

    session_destroy();

    // === 2. Ta bort autentiseringscookien + refresh-cookien ===
    $options = getCookieOptions(time() - 3600);
    setcookie($cookieName, '', $options);
    setcookie($cookieName . '_last_refresh', '', $options);

    // === 3. Visa meddelande ===
    echo '<b>Du är nu utloggad!</b><br>';
    displayLogin();
}
