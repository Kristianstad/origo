<!DOCTYPE html>
<?php
	header("Cache-Control: must-revalidate, max-age=0, s-maxage=0, no-cache, no-store");

	require_once("./functions/dbh.php");
	require_once("./functions/array_column_search.php");
	require_once("./functions/all_from_table.php");
	require_once("./functions/pkColumnOfTable.php");
	//require_once("./functions/manage/tablesFromQgs.php");

	function tablesFromQgs($qgsFile=null, $layerName=null, $tables=array(), $subtree=null)
	{
		if (isset($subtree))
		{
			$xml = $subtree;
		}
		elseif (isset($qgsFile))
		{
			$xml = simplexml_load_file($qgsFile);
		}
		if (isset($xml))
		{
			foreach ( $xml->{'layer-tree-layer'} as $layer)
    		{
    			if ((!isset($layerName) && $layer['providerKey'] == "postgres") || (isset($layerName) && $layer['name'] == $layerName))
    			{
    				$source=explode(' ', $layer['source']);
    				$sourceParamArray=array();
    				foreach ($source as $sourceParam)
    				{
    					$tmp=explode('=', $sourceParam);
    					$sourceParamArray[$tmp[0]]=$tmp[1];
    				}
    				if (!empty($sourceParamArray['dbname']) && !empty($sourceParamArray['table']) && strpos($sourceParamArray['table'], '(') === false)
    				{
    					$table=trim($sourceParamArray['dbname'],"'").'.'.str_replace('"', "", $sourceParamArray['table']);
    					$tables[]=$table;
    					if (isset($layerName))
    					{
    						return array_unique($tables);
    					}
    				}
    			}
    		}
    		foreach ($xml->{'layer-tree-group'} as $group)
    		{
    			if (isset($layerName) && $group['name'] == $layerName)
    			{
    				$tables=tablesFromQgs($null, null, $tables, $group);
    				return array_unique($tables);
    			}
    			else
    			{
    				$tables=tablesFromQgs($null, $layerName, $tables, $group);
    				if (isset($layerName) && !empty($tables))
    				{
    					return array_unique($tables);
    				}
    			}
    		}
    	}
    	return array_unique($tables);
	}

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
				$tables=tablesFromQgs($qgsFile, $layerName);
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
