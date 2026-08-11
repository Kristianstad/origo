<?php
/**
 * Ser till att sessionen är skrivbar.
 * Fungerar både när sessionen aldrig startats och när den startats med read_and_close.
 */
function ensureSessionWritable(): void
{
    // Om sessionen är stängd (vanligt efter read_and_close) → öppna den igen
    if (session_status() === PHP_SESSION_NONE) {
        // Använd samma cookie-inställningar som resten av applikationen
        require './constants/cookieConfig.php';

        session_start([
            'cookie_domain'   => $cookieConfig['cookieDomain'],
            'cookie_path'     => $cookieConfig['cookiePath'],
            'cookie_secure'   => $cookieConfig['cookieSecure'],
            'cookie_httponly' => $cookieConfig['cookieHttpOnly'],
            'cookie_samesite' => $cookieConfig['cookieSameSite'],
        ]);
    }
}
