<?php

function removePath(string $path): bool
{
    if (is_link($path) || is_file($path)) {
        return unlink($path);
    }

    if (!is_dir($path)) {
        return true;
    }

    $iterator = new DirectoryIterator($path);
    foreach ($iterator as $item) {
        if ($item->isDot()) {
            continue;
        }

        $itemPath = $item->getPathname();
        if (!removePath($itemPath)) {
            return false;
        }
    }

    return rmdir($path);
}
