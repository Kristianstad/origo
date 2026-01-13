<!DOCTYPE html>
<html>
<head>
	<style>
		<?php require("./styles/help.css"); ?>
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
	<?php
		// Tell browsers to not cache response
		header("Cache-Control: must-revalidate, max-age=0, s-maxage=0, no-cache, no-store");
		
		if (isset($_GET['id']))
		{
			// Expose specific functions
			require_once("./functions/includeDirectory.php");
			
			// Expose all functions in given folders
			includeDirectory("./functions/common");
			
			require("./constants/configSchema.php");
			$dbh=dbh();
			$helps=all_from_table($dbh, $configSchema, 'helps');
			$help=array_column_search($_GET['id'], 'help_id', $helps);
			if (isset($help['abstract']))
			{
				echo $help['abstract'];
			}
		}
		else
		{
			echo <<<HERE
				<a href="https://origo-map.github.io/origo-documentation/latest/#origo-map" target="_blank">Origo-dokumentation</a><br>
				<a href="https://jsonchecker.com/" target="_blank">JSON Checker</a>
			HERE;
		}
		echo "<br style=\"clear:both\"><button type=\"button\" onclick=\"window.parent.postMessage({ action: 'close' }, window.location.origin);\">Stäng</button>";
	?>
</body>
</html>
