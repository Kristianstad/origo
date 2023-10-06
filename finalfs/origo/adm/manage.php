<!DOCTYPE html>
<?php
	header("Cache-Control: must-revalidate, max-age=0, s-maxage=0, no-cache, no-store");
	require_once("./functions/dbh.php");
	require_once("./functions/array_column_search.php");
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
	$idPosts=idPosts($post);
	$categoryPosts=categoryPosts($post);
	$focusTable=focusTable($idPosts);
	$dbh=dbh();
	$configTables=configTables($dbh);
	$keywordCategorized=viewKeywordCategorized($view);
	$categoryConfigs=array_intersect_key($configTables, array_flip($keywordCategorized));
	foreach ($categoryConfigs as $table=>$config)
	{
		$catParam=pkColumnOfTable($table);
		eval("\${$table}Categories=categories(\$config, \$catParam);");
	}
	$postButton=postButton($post);
	if (isset($postButton))
	{
		$type=substr($postButton, 0, -6);
		$typeTable=typeTable($type);
		$typeTablePkColumn=pkColumnOfTable($typeTable);
		$command=$post[$postButton];
		$sql="";
		if ($command == 'create' && !empty($post[$type.'IdNew']))
		{
			$id=$post[$type.'IdNew'];
			if (!in_array($id, array_column($configTables[$typeTable], $typeTablePkColumn)))
			{
				require("./constants/configSchema.php");
				$sql="INSERT INTO $configSchema.$typeTable($typeTablePkColumn) VALUES ('".$id."')";
				unset($configSchema);
			}
		}
		elseif ($command == 'delete' && !empty($post[$type.'IdDel']))
		{
			$id=$post[$type.'IdDel'];
			if (in_array($id, array_column($configTables[$typeTable], $typeTablePkColumn)))
			{
				require("./constants/configSchema.php");
				$sql="DELETE FROM $configSchema.$typeTable WHERE $typeTablePkColumn = '".$id."'";
				unset($configSchema);
			}
		}
		elseif (isset($post[$type.'Id']))
		{
			$id=$post[$type.'Id'];
			if ($command == 'update')
			{
				$config=array_column_search($id, $typeTablePkColumn, $configTables[$typeTable]);
				if ($type == 'layer' || $type == 'source')
				{
					if ($type == 'layer')
					{
						$layerName=explode('#', $id)[0];
						$sourceConfig=array_column_search($config['source'], 'source_id', $configTables['sources']);
					}
					else
					{
						$layerName=null;
						$sourceConfig=$config;
					}
					$serviceType=array_column_search($sourceConfig['service'], 'service_id', $configTables['services'])['type'];
					if (strtolower($serviceType) == 'qgis')
					{
						$qgsXml = simplexml_load_file('/services/'.$sourceConfig['service'].'/'.explode('#', $sourceConfig['source_id'])[0].'.qgs');
						if (!empty($qgsXml))
						{
							if ($type == 'source')
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
					unset($layerName, $sourceConfig, $serviceType);
				}
				$sql=sqlForUpdate(makeFullTarget($type, $config), updatePosts($post));
				unset($config);
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
						$parentOperationColumnKey=$type.'s';
						$parentOperationColumnValue=array_column_search($parentPkColumnValue, $parentPkColumnKey, $configTables[$parentKey.'s'])[$parentOperationColumnKey];
						$operationParent=array($parentKey.'s'=>array($parentPkColumnKey=>$parentPkColumnValue, $parentOperationColumnKey=>$parentOperationColumnValue));
						$sql=sqlForOperation($operation, makeBasicTarget($type, $id), $operationParent);
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
			if ($command != 'operation' && in_array($typeTable, $keywordCategorized))
			{
				eval("\${$typeTable}Categories=categories(\$configTables[\$typeTable], \$typeTablePkColumn);");
			}
		}
		unset($id, $type, $typeTable, $typeTablePkColumn, $command, $sql);
	}
	$inheritPosts=$idPosts;
	if (isset($post['groupIds']))
	{
		$inheritPosts['groupIds']=$post['groupIds'];
	}
	foreach ($categoryPosts as $postName=>$category)
	{
		$inheritPosts[$postName]=$category;
	}
?>
<html>
<head>
	<meta charset="utf-8"/>
	<title>Administrationsverktyg för Origo</title>
	<link rel="shortcut icon" href="../img/png/logo.png">
	<script>
		let topFrame="";
		<?php
			includeDirectory("./js-functions/manage");
			$updateSelects="";
			foreach ($keywordCategorized as $categorized)
			{
				eval("\$categories=\${$categorized}Categories;");
				$categories2=array();
				foreach ($categories as $category => $member)
				{
					$category=str_replace(array('-', '+', '/'), '', str_replace(' ', '_', $category));
					$categories2[$category]=$member;
					unset($category, $member);
				}
				echo "var {$categorized}Categories = ".json_encode(array_keys($categories2)).";\n";
				$updateSelects=$updateSelects."updateSelect('{$categorized}Categories', {$categorized}Categories);";
				foreach ($categories2 as $category => $member)
				{
					$member=array_merge(array(""), $member);
					echo "var {$categorized}$category = ".json_encode($member).";\n";
					unset($category, $member);
				}
				unset($categorized, $categories, $categories2);
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
	<form id="helpForm" action="help.php" target="topFrame">
		<input class="topInput" onclick="toggleTopFrame('help');" type="submit" value="Hjälp" />
	</form>
	<?php printViewSwitcher($view); ?>
	<iframe id="topFrame" name="topFrame" style="display:none" onload="javascript:(function(o){o.style.height=o.contentWindow.document.body.parentElement.scrollHeight+'px';}(this));"></iframe>
	<iframe id="hiddenFrame" name="hiddenFrame" style="display:none"></iframe>
	<form id="multiselectForm" action="multiselect.php" method="get" target="topFrame"></form>
	<?php printHeadForms($view, $configTables, $focusTable, $inheritPosts); ?>
	<script>
		<?php
			echo $updateSelects;
			unset($updateSelects);
			foreach ($categoryPosts as $postName=>$category)
			{
				$table=substr($postName, 0, -8);
				$type=tableType($table);
				$id=$idPosts[$type.'Id'];
				echo <<<HERE
					document.getElementById("{$table}Categories").value="{$category}";
					updateSelect("{$type}Select", {$table}{$category});
					document.getElementById("{$type}Select").value="{$id}";
				HERE;
				unset($table, $type, $id);
			}
			unset($categoryPosts, $postName, $category);
		?>
	</script>
<?php
/*
 ************************
 *  DYNAMISKT INNEHÅLL  *
 ************************
*/
	$helps=array_column($configTables["helps"], "help_id");

	// Om karta vald
	if (isset($post['mapId']))
	{
		$map=array('map'=>array_column_search($post['mapId'], 'map_id', $configTables['maps']));
		if (!empty(current($map)))
		{
			$selectables=array('footers'=>array_column($configTables['footers'], 'footer_id'), 'tilegrids'=>array_column($configTables['tilegrids'], 'tilegrid_id'));
			printMapForm($map, $selectables, $inheritPosts, typeHelps("map", $helps));
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
			printDatabaseForm($database, $inheritPosts, typeHelps("database", $helps));
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
			printSchemaForm($schema, $selectables, $inheritPosts, typeHelps("schema", $helps));
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
			printGroupForm($group, array('maps'=>$configTables['maps'], 'groups'=>$configTables['groups']), $inheritPosts, typeHelps("group", $helps));
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
		$childFullTarget=makeTargetFull(makeBasicTarget(substr(key($idPosts), 0, -2), current($idPosts)), $configTables);
		$childType=targetType($childFullTarget);
		$typeHelps=typeHelps($childType, $helps);

		//  Om lager vald
		if ($childType == 'layer')
		{
			$selectables=array(
				'contacts'=>array_combine(array_column($configTables['contacts'], 'contact_id'), array_column($configTables['contacts'], 'name')),
				'origins'=>array_combine(array_column($configTables['origins'], 'origin_id'), array_column($configTables['origins'], 'name')),
				'updates'=>array_column($configTables['updates'], 'update_id')
			);
			if (!empty(targetConfigParam($childFullTarget, 'source')))
			{
				$layerSource=array_column_search(targetConfigParam($childFullTarget, 'source'), 'source_id', $configTables['sources']);
				if (!empty($layerSource) && isset($layerSource['service']))
				{
					setTargetConfigParam($childFullTarget, 'service', $layerSource['service']);
				}
				unset($layerSource);
			}
			printLayerForm($childFullTarget, $selectables, array("maps"=>$configTables["maps"], "groups"=>$configTables["groups"]), array_column($configTables["sources"], "source_id"), $inheritPosts, $typeHelps);
			unset($selectables);
		}
		
		//  Om källa vald
		elseif ($childType == 'source')
		{
			$selectables=array('services'=>array_column($configTables['services'], 'service_id'), 'tilegrids'=>array_column($configTables['tilegrids'], 'tilegrid_id'), 'contacts'=>array_combine(array_column($configTables['contacts'], 'contact_id'), array_column($configTables['contacts'], 'name')));
			printSourceForm($childFullTarget, $selectables, $inheritPosts, $typeHelps);
			unset($selectables);
		}
		
		// Om tabell vald
		elseif ($childType == 'table')
		{
			$selectables=array(
				'contacts'=>array_combine(array_column($configTables['contacts'], 'contact_id'), array_column($configTables['contacts'], 'name')),
				'origins'=>array_combine(array_column($configTables['origins'], 'origin_id'), array_column($configTables['origins'], 'name')),
				'updates'=>array_column($configTables['updates'], 'update_id')
			);
			printTableForm($childFullTarget, $selectables, $inheritPosts, $typeHelps);
			unset($selectables);
		}
		
		//  Om kontroll vald
		elseif ($childType == 'control')
		{
			printControlForm($childFullTarget, array("maps"=>$configTables["maps"]), $inheritPosts, $typeHelps);
		}
		
		// Om något annat valts
		else
		{
			eval('print'.ucfirst($childType).'Form($childFullTarget, $inheritPosts, $typeHelps);');
		}
		unset($childFullTarget, $childType, $typeHelps);
	}
	unset($helps, $idPosts);

?>
</body>
</html>
