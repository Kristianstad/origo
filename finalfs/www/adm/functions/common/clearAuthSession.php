<?php
/**
 * Hjälpfunktion för att rensa auth-session. Används av initUserLdap.
 */
function clearAuthSession(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['user'] = false;
    $_SESSION['login_time_stamp'] = time();
    session_write_close();
}
