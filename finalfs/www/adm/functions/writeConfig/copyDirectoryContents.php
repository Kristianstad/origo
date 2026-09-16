<?php

function copyDirectoryContents(string $source, string $target): bool
{
    if (!is_dir($source)) {
        return false;
    }

    if (is_link($target) || is_file($target)) {
        if (!unlink($target)) {
            return false;
        }
    }

    if (!is_dir($target) && !mkdir($target, 0770, true) && !is_dir($target)) {
        return false;
    }

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($source, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($iterator as $item) {
        $relativePath = substr($item->getPathname(), strlen($source) + 1);
        $targetPath = $target . DIRECTORY_SEPARATOR . $relativePath;

        if ($item->isLink()) {
            if (is_link($targetPath) || is_file($targetPath)) {
                if (!unlink($targetPath)) {
                    return false;
                }
            } elseif (file_exists($targetPath)) {
                return false;
            }

            if (!symlink(readlink($item->getPathname()), $targetPath)) {
                return false;
            }
            continue;
        }

        if ($item->isDir()) {
            if (!is_dir($targetPath) && !mkdir($targetPath, 0770, true) && !is_dir($targetPath)) {
                return false;
            }
            continue;
        }

        $targetDirectory = dirname($targetPath);
        if (!is_dir($targetDirectory) && !mkdir($targetDirectory, 0770, true) && !is_dir($targetDirectory)) {
            return false;
        }

        if ((is_link($targetPath) || is_file($targetPath)) && !unlink($targetPath)) {
            return false;
        }
        if (file_exists($targetPath) || !copy($item->getPathname(), $targetPath)) {
            return false;
        }
    }

    return true;
}
