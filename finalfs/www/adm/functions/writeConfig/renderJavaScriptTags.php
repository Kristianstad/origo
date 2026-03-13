<?php

/**
 * Render JavaScript tags
 * 
 * Stöd för:
 *   include(assets/file.js)         → minifiera
 *   include_minify(assets/file.js)  → minifiera
 *   include_nominify(assets/file.js)→ skippa minifiering (för redan minifierade bundles)
 */
use MatthiasMullie\Minify;

function renderJavaScriptTags(array $items): string
{
    $output = [];

    foreach ($items as $original) {
        $item = trim($original);
        if ($item === '') {
            continue;
        }

        // Försök matcha de tre varianterna
        if (preg_match('#^(include|include_minify|include_nominify)\s*\(\s*(.+?)\s*\)$#i', $item, $m)) {
            $command  = strtolower($m[1]);
            $resource = $m[2];

            $content = fetchResourceContent($resource);

            if ($content === false) {
                require "./constants/proxyRoot.php";
                $escaped = htmlspecialchars($resource, ENT_QUOTES, 'UTF-8');
                $url = $proxyRoot . $_SERVER["REQUEST_URI"] . (strpos($_SERVER["REQUEST_URI"], '?') !== false ? '&' : '?') . 'badJson=y';
                echo '<script>alert("Filen ' . $escaped . ' kunde inte läsas! Ingen konfiguration skriven.");';
                echo '</script>';
                exit;
            }

            // Bestäm om vi ska minifiera eller inte
            $shouldMinify = ($command !== 'include_nominify');

            if ($shouldMinify) {
                $jsMinifier = new Minify\JS($content);
                $processedContent = $jsMinifier->minify();
            } else {
                $processedContent = $content;
            }

            // Säkerställ att </script> inte bryter HTML
            $processedContent = str_replace('</script>', '<\/script>', $processedContent);
            $processedContent = str_replace('<!--', '<\!--', $processedContent);

            $output[] = "\t\t<script>\n\t\t\t{$processedContent}\n\t\t</script>";
            continue;
        }

        // Vanlig <script src="...">
        $src = htmlspecialchars($item, ENT_QUOTES, 'UTF-8');
        $output[] = "\t\t<script src=\"{$src}\"></script>";
    }

    return "\n" . implode(PHP_EOL, $output);
}
