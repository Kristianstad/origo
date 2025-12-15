<?php

/**
 * Publish map files with uncompressed and compressed variants,
 * and create public symlinks in the web root directory.
 *
 * @param string $filepathWithoutSuffix Base path without extension (e.g. /storage/maps/123)
 * @param string $html                 HTML content
 * @param string $json                 Pretty-printed JSON content
 * @param string $mapId                The public identifier (e.g. "123")
 */
function publishMapFiles(
    string $filepathWithoutSuffix,
    string $html,
    string $json,
    string $mapId
): void {
    require("./constants/webRoot.php"); // Defines $webRoot

    $htmlFile = $filepathWithoutSuffix . '.html';
    $jsonFile = $filepathWithoutSuffix . '.json';

    // 1. Save uncompressed files
    saveFile($htmlFile, $html);
    saveFile($jsonFile, $json);

    // 2. Create Brotli versions (if supported)
    $htmlBr = compressBrotli($html);
    if ($htmlBr !== null) {
        saveFile($htmlFile . '.br', $htmlBr);
    }

    $jsonBr = compressBrotli($json);
    if ($jsonBr !== null) {
        saveFile($jsonFile . '.br', $jsonBr);
    }

    // 3. Create gzip versions
    $htmlGz = compressGzip($html);
    if ($htmlGz !== null) {
        saveFile($htmlFile . '.gz', $htmlGz);
    }

    $jsonGz = compressGzip($json);
    if ($jsonGz !== null) {
        saveFile($jsonFile . '.gz', $jsonGz);
    }

    // 4. Create public symlinks
    $base = rtrim($webRoot, '/') . '/';

    createSymlinkIfNotExists($htmlFile, $base . $mapId . '.html');
    createSymlinkIfNotExists($jsonFile, $base . $mapId . '.json');

    if ($htmlBr !== null) {
        createSymlinkIfNotExists($htmlFile . '.br', $base . $mapId . '.html.br');
    }
    if ($jsonBr !== null) {
        createSymlinkIfNotExists($jsonFile . '.br', $base . $mapId . '.json.br');
    }

    if ($htmlGz !== null) {
        createSymlinkIfNotExists($htmlFile . '.gz', $base . $mapId . '.html.gz');
    }
    if ($jsonGz !== null) {
        createSymlinkIfNotExists($jsonFile . '.gz', $base . $mapId . '.json.gz');
    }
}
