<?php

/**
 * Shared fetcher – returns raw content or false on failure
 * Changes working directory to parent folder for relative include() paths
 */
function fetchResourceContent(string $resource): string|false
{
    $isUrl = preg_match('#^https?://#i', $resource) === 1;

    // Remember original cwd – we will restore it later
    $originalCwd = getcwd();

    // ------------------------------------------------------------------
    // 1. Local file → temporarily go up one level so relative paths work
    // ------------------------------------------------------------------
    if (!$isUrl) {
        @chdir('../');
    }

    // ------------------------------------------------------------------
    // 2. Resolve path (now relative to the parent folder)
    // ------------------------------------------------------------------
    $realPath = realpath($resource);

    // ------------------------------------------------------------------
    // 3. Always restore original working directory – even on failure
    // ------------------------------------------------------------------
    if ($originalCwd !== false) {
        @chdir($originalCwd);
    }

    // ------------------------------------------------------------------
    // 4. Security + existence check
    // ------------------------------------------------------------------
    if ($isUrl) {
        // Remote URL – no realpath check needed
        $pathToFetch = $resource;
    } else {
        if ($realPath === false || !is_file($realPath) || !is_readable($realPath)) {
            return false;
        }
        $pathToFetch = $realPath;
    }

    // ------------------------------------------------------------------
    // 5. Fetch content
    // ------------------------------------------------------------------
    $context = stream_context_create([
        'http'  => [
            'timeout'        => 10,
            'user_agent'     => 'PHP Resource Inliner',
            'follow_location'=> true,
            'max_redirects'  => 5,
        ],
        'https' => ['timeout' => 10],
    ]);

    $raw = @file_get_contents($pathToFetch, false, $context);
    return ($raw !== false && $raw !== '') ? $raw : false;
}
