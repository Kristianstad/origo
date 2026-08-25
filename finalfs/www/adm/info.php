<?php
/*
info.php
 ├─ includeDirectory("./functions/common")
 ├─ includeDirectory("./functions/info")   → laddar printParents.php, printUniqueLogins.php
 ├─ dbh()                                          [common]
 ├─ toSwedish($childType)                          [common] → svensk översättning av typnamnet
 ├─ all_from_table($dbh, $configSchema, ...)        [common] → hämtar alla rader av given typ
 ├─ array_column_search(...)                        [common] → hittar EN rad baserat på kolumnvärde
 ├─ pkColumnOfTable(...)                             [common] → tar reda på primärnyckelkolumn för en tabell
 ├─ (om $childType == 'source' och QGIS) läser .qgs-fil direkt från disk
 ├─ (om $childType == 'aduser') printUniqueLogins(...)  [info] → inloggningsstatistik
 ├─ findAllParents($dbh, $child)                     [common] → hittar alla objekt som refererar till detta
 └─ printParents($allParents)                        [info] → skriver ut länkad lista av föräldrar
     └─ använder internt: assoc_array_values, toSwedish  [common]
*/

// Tell browsers to not cache response
header("Cache-Control: must-revalidate, max-age=0, s-maxage=0, no-cache, no-store");

// Expose specific functions
require_once("./functions/includeDirectory.php");

// Expose all functions in given folders
includeDirectory("./functions/common");
includeDirectory("./functions/info");

require("./constants/configSchema.php");

$dbh = dbh();

$childType   = $_GET['type'] ?? '';
$childId     = $_GET['id'] ?? '';
$childTypeSv = toSwedish($childType);

// === Början av sidan ===
echo <<<HTML
<!DOCTYPE html>
<html>
<head>
	<style>
HTML;

require("./styles/info.css");

echo <<<HTML
	</style>
	<script>
		window.onload = function() {
			if (window.parent !== window) { // Make sure we are in an iframe
				window.parent.postMessage({ action: 'resize' }, window.location.origin);
			}
		};
	</script>
</head>
<body>
HTML;

// === Innehåll ===
if (!empty($childId)) {
	echo "<div>";
	echo "<h2>$childId</h2> ($childTypeSv)</br>";

	$allOfChildType = all_from_table($dbh, $configSchema, $childType . 's');
	$child = array($childType => $childId);
	$childFull = array_column_search($childId, pkColumnOfTable($childType . 's'), $allOfChildType);

	if (!empty($childFull['name'])) {
		echo "<b>Namn: </b>" . $childFull['name'] . "</br>";
	}
	if (!empty($childFull['alias'])) {
		echo "<b>Alias: </b>" . $childFull['alias'] . "</br>";
	}
	if (!empty($childFull['info'])) {
		echo $childFull['info'] . "</br>";
	}

	if ($childType == 'source') {
		$services = all_from_table($dbh, $configSchema, 'services');
		$serviceType = array_column_search($childFull['service'], pkColumnOfTable('services'), $services)['type'];
		if (strtolower($serviceType) == 'qgis') {
			$qgsXml = simplexml_load_file('/services/' . $childFull['service'] . '/' . explode('#', $childId)[0] . '.qgs');
			if (!empty($qgsXml)) {
				echo "<b>Qgis-version: </b>" . $qgsXml['version'] . "<br>";
				echo "<b>Senast uppdaterad: </b>" . $qgsXml['saveDateTime'] . ", " . $qgsXml['saveUserFull'] . "<br>";
			}
		}
	}

	if ($childType == 'aduser') {
		printUniqueLogins(array_column($allOfChildType, 'lastlogin'));
	}

	$allParents = findAllParents($dbh, $child);
	if (!empty(array_values($allParents))) {
		echo "<h3 style='margin-top:0.5em'>Används av</h3></br>";
		printParents($allParents);
	}

	echo '</div>';

	if (!empty($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'info.php') !== false) {
		echo '&nbsp;<button onclick="history.back()">Tillbaks</button>';
	}

	echo "&nbsp;<form action='manage.php' method='post' target='_blank' style='display:inline'><button type='submit' name='" . $childType . "Id' value='" . $childId . "'>Administrera</button></form>";
	echo "&nbsp;<button type=\"button\" onclick=\"window.parent.postMessage({ action: 'close' }, window.location.origin);\">Stäng</button>";
}

pg_close($dbh);

// === Slut på sidan ===
echo <<<HTML
</body>
</html>
HTML;
