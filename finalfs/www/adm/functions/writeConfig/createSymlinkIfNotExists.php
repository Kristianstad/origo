<?php

/**
 * Create or replace a symlink. Returns false when an existing directory blocks the link.
 */
function createSymlinkIfNotExists(string $target, string $link): bool
{
    if (is_link($link) || is_file($link)) {
        if (!unlink($link)) {
            return false;
        }
    } elseif (file_exists($link)) {
        return false;
    }

    return symlink($target, $link);
}
