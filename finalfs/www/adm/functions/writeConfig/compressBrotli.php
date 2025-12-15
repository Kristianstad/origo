<?php

/**
 * Compress data with Brotli (level 11, text mode) if the extension is available.
 * Returns compressed string or null if Brotli is not available.
 */
function compressBrotli(string $data): ?string
{
    if (!function_exists('brotli_compress')) {
        return null; // Brotli extension not loaded – caller must handle this
    }
    $compressed = brotli_compress($data, 11, BROTLI_TEXT);
    return $compressed !== false ? $compressed : null;
}
