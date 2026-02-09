<?php

// Hjälpfunktion: Hämta cachat eller färskt GetProjectSettings XML
function getCachedProjectSettings($qgisUrl) {
    $cacheKey = 'qgis_project_settings_' . md5($qgisUrl);  // GROK: Unik nyckel per server-URL
    $defaultTtl = 600;  // sekunder (10 min) om ttl saknas i query

    // Hämta TTL från nuvarande request (dynamisk per anrop)
    $ttl = $defaultTtl;
    if (isset($_GET['ttl']) && is_numeric($_GET['ttl']) && $_GET['ttl'] > 0) {
        $ttl = (int)$_GET['ttl'];
    }

    // Försök APCu först (snabbast)
    if (function_exists('apcu_fetch')) {
        $cached = apcu_fetch($cacheKey);
        if ($cached !== false) {
            // error_log("Cache hit (APCu) för GetProjectSettings");
            return $cached;
        }
    }

    // Fallback: Filbaserad cache
    $cacheFile = sys_get_temp_dir() . '/' . $cacheKey . '.xml.cache';
    if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $ttl) {
        $content = file_get_contents($cacheFile);
        // error_log("Cache hit (fil) för GetProjectSettings");
        return $content;
    }

    // Cache miss → hämta från QGIS
    $projectParams = [
        'SERVICE' => 'WMS',
        'REQUEST' => 'GetProjectSettings',
    ];
    $response = forwardToQgisServer($qgisUrl, $projectParams);

    if ($response['headers']['http_code'] ?? 0 >= 200 && ($response['headers']['http_code'] ?? 0) < 300) {
        $xmlContent = $response['body'];

        // Spara i APCu om tillgängligt
        if (function_exists('apcu_store')) {
            apcu_store($cacheKey, $xmlContent, $ttl);
        }

        // Spara i fil som fallback
        file_put_contents($cacheFile, $xmlContent);

        // error_log("Cache miss - hämtade och cachade GetProjectSettings (TTL: $ttl s)");
        return $xmlContent;
    }

    // Vid fel från QGIS → returnera rått (ingen cache)
    // error_log("Cache miss - QGIS-fel, cachar inte");
    return $response['body'];
}
