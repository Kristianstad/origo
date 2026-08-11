<?php
/**
 * Hjälpfunktion för att rensa auth-session. Används av initUserLdap.
 */
function clearAuthSession(): void
{
    ensureSessionWritable();

    $_SESSION['user'] = false;
    $_SESSION['login_time_stamp'] = time();

    session_write_close();
}
