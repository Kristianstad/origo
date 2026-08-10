<?php
/**
 * Enkel whitelist-kontroll mot open redirect.
 * Anpassa efter dina domäner/paths.
 */
function isSafeReturnUrl(string $url): bool
{
    $parsed = parse_url($url);
    if ($parsed === false) {
        return false;
    }

    // Tillåt endast relativa sökvägar eller samma host
    if (!isset($parsed['host'])) {
        // Relativ URL – okej
        return true;
    }

    $allowedHosts = [
        $_SERVER['HTTP_HOST'] ?? '',
        // lägg till fler tillåtna hosts här om behövs
    ];

    return in_array($parsed['host'], $allowedHosts, true);
}
