<?php
// Tell browsers to not cache response
header("Cache-Control: must-revalidate, max-age=0, s-maxage=0, no-cache, no-store");

// Expose specific functions
require_once("./functions/includeDirectory.php");

// Expose all functions in given folders
includeDirectory("./functions/common");

require "./constants/configSchema.php";

$dbh = dbh();
$layers   = all_from_table($dbh, $configSchema, 'layers');
$sources  = all_from_table($dbh, $configSchema, 'sources');
$services = all_from_table($dbh, $configSchema, 'services');

$updated = 0;
$errors  = [];

foreach ($layers as $layer) {
    if (empty($layer['tables'])) {
        $layerId   = $layer['layer_id'];
        $layerName = explode('#', $layerId)[0];
        $sourceId  = $layer['source'];

        $source = array_column_search($sourceId, pkColumnOfTable('sources'), $sources);
        if (empty($source)) {
            continue;
        }

        $service = array_column_search($source['service'], 'service_id', $services);
        if (empty($service)) {
            continue;
        }

        $serviceType = $service['type'];

        if (strtolower($serviceType) === 'qgis') {
            $qgsFile = '/services/' . $source['service'] . '/' . explode('#', $sourceId)[0] . '.qgs';
            $qgsXml  = @simplexml_load_file($qgsFile);

            if ($qgsXml === false) {
                $errors[] = "Kunde inte läsa QGS-fil: {$qgsFile}";
                continue;
            }

            $tables = tablesFromQgsXml($qgsXml, $layerName);

            if (!empty($tables)) {
                $tablesStr = '{' . implode(',', $tables) . '}';

                $sql = "UPDATE {$configSchema}.layers SET tables = $1 WHERE layer_id = $2";
                $result = pg_query_params($dbh, $sql, [$tablesStr, $layerId]);

                if (!$result) {
                    $errors[] = "SQL-fel för layer_id {$layerId}: " . pg_last_error($dbh);
                } else {
                    $updated++;
                }
            }
        }
    }
}

pg_close($dbh);

// === HTML-svar ===
$errorHtml = '';
if (!empty($errors)) {
    $errorHtml = '<ul><li>' . implode('</li><li>', array_map('htmlspecialchars', $errors)) . '</li></ul>';
}

echo <<<HTML
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Uppdatera layer tables</title>
</head>
<body>
<h1>Klart</h1>
<p>Uppdaterade lager: {$updated}</p>
{$errorHtml}
</body>
</html>
HTML;
