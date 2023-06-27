<!DOCTYPE html>
<?php
	header("Cache-Control: must-revalidate, max-age=0, s-maxage=0, no-cache, no-store");
	require_once("./functions/dbh.php");
	require_once("./functions/array_column_search.php");
	require_once("./functions/layerCategories.php");
	require_once("./functions/pkColumnOfTable.php");
	require_once("./functions/configTables.php");
	require_once("./functions/includeDirectory.php");
	includeDirectory("./functions/manage");
	$post=array_filter($_POST, function($value) {return (!empty($value) || $value === "0");});
	$view=null;
	if (isset($_GET['view']))
	{
		$view=$_GET['view'];
	}
	unset($_POST, $_GET);
	if (isset($post['groupIds']))
	{
		$groupIdsArray=explode(',', $post['groupIds']);
		if (!isset($post['groupId']))
		{
			$post['groupId']=$groupIdsArray[0];
		}
	}
	elseif (isset($post['groupId']))
	{
		$post['groupIds']=$post['groupId'];
		$groupIdsArray=array($post['groupId']);
	}
	else
	{
		$groupIdsArray=array();
	}
	//var_dump($post);
	$idPosts=array_filter($post, function($key) {return (substr($key, -2) == 'Id');}, ARRAY_FILTER_USE_KEY);
	unset($idPosts['fromMapId'], $idPosts['toMapId'], $idPosts['fromGroupId'], $idPosts['toGroupId']);
	$focusTable=focusTable($idPosts);
	$dbh=dbh();
	$configTables=configTables($dbh);
	$layerCategories=layerCategories($configTables['layers']);
	$pressedButton=array_keys(array_filter($post, function($key) {return (substr($key, -6) == 'Button');}, ARRAY_FILTER_USE_KEY));
	if (isset($pressedButton[0]))
	{
		$pressedButton=$pressedButton[0];
	}
	else
	{
		unset($pressedButton);
	}
	if (isset($pressedButton))
	{
		$targetType=substr($pressedButton, 0, -6);
		$targetTable=$targetType.'s';
		$targetPkColumn=pkColumnOfTable($targetTable);
		$command=$post[$pressedButton];
		$sql="";
		if ($command == 'create' && !empty($post[$targetType.'IdNew']))
		{
			$targetId=$post[$targetType.'IdNew'];
			if (!in_array($targetId, array_column($configTables[$targetTable], $targetPkColumn)))
			{
				require("./constants/configSchema.php");
				$sql="INSERT INTO $configSchema.$targetTable($targetPkColumn) VALUES ('".$targetId."')";
				unset($configSchema);
			}
		}
		elseif ($command == 'delete' && !empty($post[$targetType.'IdDel']))
		{
			$targetId=$post[$targetType.'IdDel'];
			if (in_array($targetId, array_column($configTables[$targetTable], $targetPkColumn)))
			{
				require("./constants/configSchema.php");
				$sql="DELETE FROM $configSchema.$targetTable WHERE $targetPkColumn = '".$targetId."'";
				unset($configSchema);
			}
		}
		elseif (isset($post[$targetType.'Id']))
		{
			$targetId=$post[$targetType.'Id'];
			if ($command == 'update')
			{
				$target=array_column_search($targetId, $targetPkColumn, $configTables[$targetTable]);
				if ($targetType == 'layer' || $targetType == 'source')
				{
					if ($targetType == 'layer')
					{
						$layerName=explode('#', $targetId)[0];
						$source=array_column_search($target['source'], 'source_id', $configTables['sources']);
					}
					else
					{
						$layerName=null;
						$source=$target;
					}
					$serviceType=array_column_search($source['service'], 'service_id', $configTables['services'])['type'];
					if (strtolower($serviceType) == 'qgis')
					{
						$qgsXml = simplexml_load_file('/services/'.$source['service'].'/'.explode('#', $source['source_id'])[0].'.qgs');
						if (!empty($qgsXml))
						{
							if ($targetType == 'source')
							{
								if (!empty($fromQgs=substr($qgsXml['saveDateTime'], 0 ,10)))
								{
									$post['updateUpdated']=$fromQgs;
								}
								if (!empty($fromQgs=strstr($qgsXml['version'], '-', true)))
								{
									$post['updateSoftversion']=$fromQgs;
								}
							}
							if (!empty($fromQgs=tablesFromQgsXml($qgsXml, $layerName)))
							{
								$post['updateTables']=implode(',', $fromQgs);
							}
						}
						unset($qgsXml, $fromQgs);
					}
					unset($layerName, $source, $serviceType);
				}
				$updatePosts=array_filter($post, function($key) {return (substr($key, 0, 6) == 'update');}, ARRAY_FILTER_USE_KEY);
				$sql=sqlForUpdate(array($targetType => $target), $updatePosts);
				unset($target, $updatePosts);
			}
			elseif ($command == 'operation')
			{
				if (!empty($post['toMapId']) || !empty($post['fromMapId']))
				{
					$parentKey='map';
				}
				elseif (!empty($post['toGroupId']) || !empty($post['fromGroupId']))
				{
					$parentKey='group';
				}
				if (isset($parentKey))
				{
					if (!empty($post['toMapId']) || !empty($post['toGroupId']))
					{
						$operation='add';
						$parentPkColumnValue=$post['to'.ucfirst($parentKey).'Id'];
					}
					elseif (!empty($post['fromMapId']) || !empty($post['fromGroupId']))
					{
						$operation='remove';
						$parentPkColumnValue=$post['from'.ucfirst($parentKey).'Id'];
					}
					if (isset($operation))
					{
						$parentPkColumnKey=pkColumnOfTable($parentKey.'s');
						$parentOperationColumnKey=$targetType.'s';
						$parentOperationColumnValue=array_column_search($parentPkColumnValue, $parentPkColumnKey, $configTables[$parentKey.'s'])[$parentOperationColumnKey];
						$operationParent=array($parentKey.'s'=>array($parentPkColumnKey=>$parentPkColumnValue, $parentOperationColumnKey=>$parentOperationColumnValue));
						$sql=sqlForOperation($operation, array($targetType => $targetId), $operationParent);
						unset($operation, $parentPkColumnValue, $parentPkColumnKey, $parentOperationColumnKey, $parentOperationColumnValue, $operationParent);
					}
					unset($parentKey);
				}
			}
		}
		if (!empty($sql))
		{
			$result=pg_query($dbh, $sql);
			if (!$result)
			{
				die("Error in SQL query: " . pg_last_error());
			}
			unset($result);
			$configTables=configTables($dbh);
			if ($command != 'operation' && $targetType == 'layer')
			{
				$layerCategories=layerCategories($configTables['layers']);
			}
		}
		unset($targetId, $targetType, $targetTable, $targetPkColumn, $command, $sql);
	}
	$inheritPosts=$idPosts;
	if (isset($post['groupIds']))
	{
		$inheritPosts['groupIds']=$post['groupIds'];
	}
	if (isset($post['layerCategory']))
	{
		$inheritPosts['layerCategory']=$post['layerCategory'];
	}
?>
<html>
<head>
	<meta charset="utf-8"/>
	<title>Administrationsverktyg för Origo</title>
	<link rel="shortcut icon" href="../img/png/logo.png">
	<script>
		var topFrame="";
		<?php
			includeDirectory("./js-functions/manage");
			echo "var categories = ".json_encode(array_keys($layerCategories)).";\n";
			foreach ($layerCategories as $category => $catLayers)
			{
				$catLayers=array_merge(array(""), $catLayers);
				echo "var $category = ".json_encode($catLayers).";\n";
				unset($category, $catLayers);
			}
		?>
	</script>
	<style>
		<?php require("./styles/manage.css"); ?>
	</style>
</head>
<body onresize="Array.from(document.getElementsByClassName('resizeimg')).forEach(function(element) { element.onerror(); });">
	<form action="read_json.php">
		<input class="topInput" type="submit" value="Importera JSON" />
	</form>
	<form action="help.php" target="topFrame">
		<input class="topInput" onclick="toggleTopFrame('help');" type="submit" value="Hjälp" />
	</form>
	<?php printViewSwitcher($view); ?>
	<iframe id="topFrame" name="topFrame" style="display:none" onload="javascript:(function(o){o.style.height=o.contentWindow.document.body.parentElement.scrollHeight+'px';}(this));"></iframe>
	<iframe id="hiddenFrame" name="hiddenFrame" style="display:none"></iframe>
	<?php printHeadForms($view, $configTables, $focusTable, $inheritPosts); ?>
	<script>
		updateSelect("layerCategories", categories);
		<?php
			if (isset($post['layerCategory']))
			{
				echo <<<HERE
					document.getElementById("layerCategories").value="{$post['layerCategory']}";
					updateSelect("layerSelect", {$post['layerCategory']});
					document.getElementById("layerSelect").value="{$post['layerId']}";
				HERE;
			}
		?>
	</script>
<?php
/*
 ************************
 *  DYNAMISKT INNEHÅLL  *
 ************************
*/
	// Om karta vald
	if (isset($post['mapId']))
	{
		$map=array('map'=>array_column_search($post['mapId'], 'map_id', $configTables['maps']));
		if (!empty(current($map)))
		{
			$selectables=array('footers'=>array_column($configTables['footers'], 'footer_id'), 'tilegrids'=>array_column($configTables['tilegrids'], 'tilegrid_id'));
			printMapForm($map, $selectables, $inheritPosts);
			echo '<table><tr>';
			$thClass='thFirst';
			if (!empty($groupIdsArray))
			{
				$inheritPosts['groupId']=$groupIdsArray[0];
			}
			if (isset($inheritPosts['layerId']))
			{
				printChildSelect($map, 'layers', $thClass, 'Lager', $inheritPosts);
				printChildSelect($map, 'groups', $thClass, 'Grupp', $inheritPosts);
				printChildSelect($map, 'controls', $thClass, 'Kontroll', $inheritPosts);
			}
			elseif (isset($inheritPosts['controlId']))
			{
				printChildSelect($map, 'controls', $thClass, 'Kontroll', $inheritPosts);
				printChildSelect($map, 'groups', $thClass, 'Grupp', $inheritPosts);
				printChildSelect($map, 'layers', $thClass, 'Lager', $inheritPosts);
			}
			else
			{
				printChildSelect($map, 'groups', $thClass, 'Grupp', $inheritPosts);
				printChildSelect($map, 'layers', $thClass, 'Lager', $inheritPosts);
				printChildSelect($map, 'controls', $thClass, 'Kontroll', $inheritPosts);
			}
			echo '</tr></table><hr>';
			unset($selectables, $thClass);
		}
		unset($map, $idPosts['mapId']);
	}
	
	// Om databas vald
	elseif (isset($post['databaseId']))
	{
		$database=array('database'=>array_column_search($post['databaseId'], 'database_id', $configTables['databases']));
		if (!empty(current($database)))
		{
			printDatabaseForm($database, $inheritPosts);
			$databaseSchemas =preg_grep("/^".$post['databaseId']."[.]/", array_column($configTables['schemas'], 'schema_id'));
			$database['database']['schemas']='{'.implode(',', $databaseSchemas).'}';
			echo '<table><tr>';
			$thClass='thFirst';
			printChildSelect($database, 'schemas', $thClass, 'Schema', $inheritPosts);
			echo '</tr></table><hr>';
			unset($databaseSchemas, $thClass);
		}
		unset($database, $idPosts['databaseId']);
	}
	
	// Om schema vald
	if (isset($post['schemaId']))
	{
		$schema=array('schema'=>array_column_search($post['schemaId'], 'schema_id', $configTables['schemas']));
		if (!empty(current($schema)))
		{
			$selectables=array(
				'contacts'=>array_combine(array_column($configTables['contacts'], 'contact_id'), array_column($configTables['contacts'], 'name')),
				'origins'=>array_combine(array_column($configTables['origins'], 'origin_id'), array_column($configTables['origins'], 'name')),
				'updates'=>array_column($configTables['updates'], 'update_id')
			);
			printSchemaForm($schema, $selectables, $inheritPosts);
			$schemaTables =preg_grep("/^".$post['schemaId']."[.]/", array_column($configTables['tables'], 'table_id'));
			$schema['schema']['tables']='{'.implode(',', $schemaTables).'}';
			echo '<table><tr>';
			$thClass='thFirst';
			printChildSelect($schema, 'tables', $thClass, 'Tabell', $inheritPosts);
			echo '</tr></table><hr>';
			unset($schemaTables, $thClass);
		}
		unset($schema, $idPosts['schemaId']);
	}

	//  Om grupp vald
	$tmpGroupIds=$groupIdsArray;
	$parent=array_shift($tmpGroupIds);
	$totGroupLevels=count($groupIdsArray);
	$groupLevel=1;
	foreach ($groupIdsArray as $groupId)
	{
		$group=array('group'=>array_column_search($groupId, 'group_id', $configTables['groups']));
		$inheritPosts['groupId']=$groupId;
		if (!empty(current($group)))
		{
			if (count($tmpGroupIds) > 0)
			{
				$parent="$parent,".array_shift($tmpGroupIds);
			}
			printGroupForm($group, array('maps'=>$configTables['maps'], 'groups'=>$configTables['groups']), $inheritPosts);
			echo '<table><tr>';
			$thClass='thFirst';
			if ($groupLevel == $totGroupLevels && isset($inheritPosts['layerId']))
			{
				printChildSelect($group, 'layers', $thClass, 'Lager', $inheritPosts, $groupLevel);
				printChildSelect($group, 'groups', $thClass, 'Grupp', $inheritPosts, $groupLevel, $parent);
			}
			else
			{
				printChildSelect($group, 'groups', $thClass, 'Grupp', $inheritPosts, $groupLevel, $parent);
				printChildSelect($group, 'layers', $thClass, 'Lager', $inheritPosts, $groupLevel);
			}
			echo '</tr></table><hr>';
			$groupLevel++;
		}
		unset($group);
	}
	unset($tmpGroupIds, $parent, $groupLevel, $groupId, $thClass, $idPosts['groupId'], $totGroupLevels);

	if (!empty($idPosts))
	{
		$target=substr(key($idPosts), 0, -2);
		$table=$target.'s';
		$child=array($target=>array_column_search(current($idPosts), pkColumnOfTable($table), $configTables[$table]));
		$printFormFunction='print'.ucfirst($target).'Form';
		
		//  Om lager vald
		if ($target == 'layer')
		{
			$selectables=array(
				'contacts'=>array_combine(array_column($configTables['contacts'], 'contact_id'), array_column($configTables['contacts'], 'name')),
				'origins'=>array_combine(array_column($configTables['origins'], 'origin_id'), array_column($configTables['origins'], 'name')),
				'updates'=>array_column($configTables['updates'], 'update_id')
			);
			if (isset($child['layer']['source']))
			{
				$layerSource=array_column_search($child['layer']['source'], 'source_id', $configTables['sources']);
				if (!empty($layerSource) && isset($layerSource['service']))
				{
					$child['layer']['service']=$layerSource['service'];
				}
				unset($layerSource);
			}
			eval($printFormFunction.'($child, $selectables, array("maps"=>$configTables["maps"], "groups"=>$configTables["groups"]), array_column($configTables["sources"], "source_id"), $inheritPosts);');
			unset($selectables);
		}
		
		//  Om källa vald
		elseif ($target == 'source')
		{
			$selectables=array('services'=>array_column($configTables['services'], 'service_id'), 'tilegrids'=>array_column($configTables['tilegrids'], 'tilegrid_id'), 'contacts'=>array_combine(array_column($configTables['contacts'], 'contact_id'), array_column($configTables['contacts'], 'name')));
			eval($printFormFunction.'($child, $selectables, $inheritPosts);');
			unset($selectables);
		}
		
		// Om tabell vald
		elseif ($target == 'table')
		{
			$selectables=array(
				'contacts'=>array_combine(array_column($configTables['contacts'], 'contact_id'), array_column($configTables['contacts'], 'name')),
				'origins'=>array_combine(array_column($configTables['origins'], 'origin_id'), array_column($configTables['origins'], 'name')),
				'updates'=>array_column($configTables['updates'], 'update_id')
			);
			eval($printFormFunction.'($child, $selectables, $inheritPosts);');
			unset($selectables);
		}
		
		//  Om kontroll vald
		elseif ($target == 'control')
		{
			eval($printFormFunction.'($child, array("maps"=>$configTables["maps"]), $inheritPosts);');
		}
		
		// Om något annat valts
		else
		{
			eval($printFormFunction.'($child, $inheritPosts);');
		}
		unset($target, $table, $child, $printFormFunction);
	}
	unset($idPosts);

?>
</body>
</html>
