<?php

/**
 * Create or replace a symlink, including over an existing directory.
 */
function createSymlinkIfNotExists(string $target, string $link): bool
{
    if ((file_exists($link) || is_link($link)) && !removePath($link)) {
        return false;
    }

    return symlink($target, $link);
}
