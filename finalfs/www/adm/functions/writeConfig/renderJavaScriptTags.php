<?php

/**
 * Render JavaScript tags (uppdaterad – använder shared fetcher)
 */
use MatthiasMullie\Minify;

function renderJavaScriptTags(array $items): string
{
    $output = [];

    foreach ($items as $original) {
        $item = trim($original);
        if ($item === '') continue;

        if (preg_match('#^include\s*\(\s*(.+?)\s*\)$#i', $item, $m)) {
            $resource = $m[1];
            $content  = fetchResourceContent($resource);

            if ($content === false) {
                require_once "./constants/proxyRoot.php";
                $escaped = htmlspecialchars($resource, ENT_QUOTES, 'UTF-8');
                $url = $proxyRoot . $_SERVER["REQUEST_URI"] . (strpos($_SERVER["REQUEST_URI"], '?') !== false ? '&' : '?') . 'badJson=y';
                echo '<script>alert("Filen ' . $escaped . ' kunde inte läsas! Ingen konfiguration skriven.");';
                echo '</script>';
                exit;
            }
			
			$jsMinifier = new Minify\JS($content);
			$minifiedContent=$jsMinifier->minify();
            $output[] = "\t\t<script>\n\t\t\t{$minifiedContent}\n\t\t</script>";
            continue;
        }

        $src = htmlspecialchars($item, ENT_QUOTES, 'UTF-8');
        $output[] = "\t\t<script src=\"{$src}\"></script>";
    }

    return "\n".implode(PHP_EOL, $output);
}
