<?php

function isSafeReturnTo(string $url): bool {
    $host = parse_url($url, PHP_URL_HOST);
    if (empty($host)) {
        return false;
    }
    return preg_match('/(?:^|\.)kristianstad\.se$/i', $host) === 1;
}
