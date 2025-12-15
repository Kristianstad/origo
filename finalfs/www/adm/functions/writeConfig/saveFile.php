<?php

/**
 * Defensively save content to a file.
 * Returns true on success, false on failure.
 */
function saveFile(string $path, string $content): bool
{
    $result = file_put_contents($path, $content);
    return $result !== false;
}
