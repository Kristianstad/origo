<?php

/**
 * Compress data with gzip (level 9).
 * Returns compressed string or null on failure.
 */
function compressGzip(string $data): ?string
{
    $compressed = gzencode($data, 9);
    return $compressed !== false ? $compressed : null;
}
