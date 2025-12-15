<?php

/**
 * Create a symlink only if the link does not already exist as a file or symlink.
 * Returns true if symlink was created or already exists, false on error.
 */
function createSymlinkIfNotExists(string $target, string $link): bool
{
    // GROK: här är varför detta behövs – undviker race condition och onödiga fel
    if (file_exists($link) || is_link($link)) {
        return true; // Already present in the desired state
    }

    return symlink($target, $link);
}
