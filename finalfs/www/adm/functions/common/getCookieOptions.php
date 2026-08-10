<?php
/**
 * Gemensam cookie-options-generator
 */
function getCookieOptions(int $expires): array
{
    require './constants/cookieConfig.php';

    return [
        'expires'  => $expires,
        'path'     => $cookieConfig['cookiePath'],
        'domain'   => $cookieConfig['cookieDomain'],
        'secure'   => $cookieConfig['cookieSecure'],
        'httponly' => $cookieConfig['cookieHttpOnly'],
        'samesite' => $cookieConfig['cookieSameSite'],
    ];
}
