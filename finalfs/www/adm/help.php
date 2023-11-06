<!DOCTYPE html>
<html>
<head>
	<style>
		<?php require("./styles/help.css"); ?>
	</style>
</head>
<body>
	<?php
		if (isset($_GET['id']))
		{
			require_once("./functions/dbh.php");
			require_once("./functions/array_column_search.php");
			require_once("./functions/all_from_table.php");
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
				<a href="https://origo-map.github.io/origo-documentation/latest/#origo-map" target="_blank">Origo-dokumentation</a>
			HERE;
		}
	?>
</body>
</html>
