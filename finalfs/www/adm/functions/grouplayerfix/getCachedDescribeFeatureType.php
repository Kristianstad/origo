<?php

// Hjälpfunktion: Hämta cachad eller färsk describeFeatureType JSON för ett enskilt lager
function getCachedDescribeFeatureType($qgisUrl, $typeName) {
    $cacheKey = 'qgis_describe_' . md5($qgisUrl . '|' . $typeName . '|json');  // GROK: Unik per lager + format
    $defaultTtl = 600;

    // Dynamisk TTL från aktuell request
    $ttl = $defaultTtl;
    if (isset($_GET['ttl']) && is_numeric($_GET['ttl']) && $_GET['ttl'] > 0) {
        $ttl = (int)$_GET['ttl'];
    }

    // Försök APCu
    if (function_exists('apcu_fetch')) {
        $cached = apcu_fetch($cacheKey);
        if ($cached !== false) {
            return $cached;
        }
    }

    // Fil-fallback
    $cacheFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $cacheKey . '.json.cache';
    if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $ttl) {
        return file_get_contents($cacheFile);
    }

    // Miss → hämta från QGIS
    $descParams = [
        'request' => 'describeFeatureType',
        'outputFormat' => 'application/json',
        'service' => 'WFS',
        'typeName' => $typeName,
    ];

    $response = forwardToQgisServer($qgisUrl, $descParams);

    $httpCode = $response['headers']['http_code'] ?? 0;
    if ($httpCode >= 200 && $httpCode < 300) {
        $jsonContent = $response['body'];

        if (function_exists('apcu_store')) {
            apcu_store($cacheKey, $jsonContent, $ttl);
        }
        file_put_contents($cacheFile, $jsonContent, LOCK_EX);

        return $jsonContent;
    }

    // Fel → cachar inte
    return $response['body'];
}
