<?php
/**
 * Hjälpfunktion för att rensa auth-session. Används av initUserLdap.
 * $writable = true  → försök spara ändringen
 * $writable = false → bara rensa i minnet för aktuell request
 */
function clearAuthSession(bool $writable = true): void
{
    $_SESSION['user'] = false;
    $_SESSION['login_time_stamp'] = time();

    if ($writable && session_status() === PHP_SESSION_ACTIVE) {
        session_write_close();
    }
}
