<!DOCTYPE html>
<?php
	// Tell browsers to not cache response
	header("Cache-Control: must-revalidate, max-age=0, s-maxage=0, no-cache, no-store");

	// Expose specific functions
	require_once("./functions/includeDirectory.php");

	// Expose all functions in given folders
	includeDirectory("./functions/common");

	require("./constants/configSchema.php");
	$dbh=dbh();
	$layers=all_from_table($dbh, $configSchema, 'layers');
	$sources=all_from_table($dbh, $configSchema, 'sources');
	$services=all_from_table($dbh, $configSchema, 'services');
	foreach ($layers as $layer)
	{
		if (empty($layer['tables']))
		{
			$layerId=$layer['layer_id'];
			$layerName=explode('#', $layerId)[0];
			$sourceId=$layer['source'];
			$source=array_column_search($sourceId, pkColumnOfTable('sources'), $sources);
			$serviceType=array_column_search($source['service'], 'service_id', $services)['type'];
			if (strtolower($serviceType) == 'qgis')
			{
				$qgsFile='/services/'.$source['service'].'/'.explode('#', $sourceId)[0].'.qgs';
				$qgsXml=simplexml_load_file($qgsFile);
				$tables=tablesFromQgsXml($qgsXml, $layerName);
				if (!empty($tables))
				{
					$tables='{'.implode(',', $tables).'}';
					$result=pg_query($dbh, "UPDATE $configSchema.layers SET tables = '$tables' WHERE layer_id = '$layerId'");
					if (!$result)
					{
						die("Error in SQL query: " . pg_last_error());
					}
				}
			}
		}
	}

?>
