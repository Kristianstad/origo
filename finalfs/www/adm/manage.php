<!DOCTYPE html>
<?php
	// Tell browsers to not cache response
	header("Cache-Control: must-revalidate, max-age=0, s-maxage=0, no-cache, no-store");

	// Expose specific functions
	require_once("./functions/dbh.php");
	require_once("./functions/array_column_search.php");
	require_once("./functions/pkColumnOfTable.php");
	require_once("./functions/configTables.php");
	require_once("./functions/pgArrayToPhp.php");
	require_once("./functions/includeDirectory.php");

	// Expose all functions in given folder
	includeDirectory("./functions/manage");

	// Expose posted data as $post (array)
	$post=array_filter($_POST, function($value) {return (!empty($value) || $value === "0");});

	// Expose view query parameter as $view (string)
	$view=null;
	if (isset($_GET['view']))
	{
		$view=$_GET['view'];
	}

	// Unset unused global variables
	unset($_POST, $_GET);

	// Create $groupIdsArray (array) from $post and update related, missing $post values
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

	// Expose all $post values where the key ends with 'Id' (excluding 'fromMapId', 'toMapId', 'fromGroupId', 'toGroupId') as $idPosts (array)
	$idPosts=idPosts($post);

	// Expose posted configuration field widths as $widthPosts (array)
	$widthPosts=widthPosts($post);
				
	// Expose posted configuration field heights as $heightPosts (array)
	$heightPosts=heightPosts($post);

	// Expose all $post values where the key ends with 'Category' as $categoryPosts (array)
	$categoryPosts=categoryPosts($post);

	// Determine which table has focus and expose the id as $focusTable (string)
	$focusTable=focusTable($idPosts);

	// Expose postgresql database handle as $dbh (handle)
	$dbh=dbh();

	// Read configuration tables from database and expose as $configTables (array)
	$configTables=configTables($dbh);

	// Ids of tables (in current view) that should be categorized by keywords, exposed as $keywordCategorized (array)
	$keywordCategorized=viewKeywordCategorized($view);

	// Configs (from tables) that should be categorized by keywords, exposed as $categoryConfigs (array)
	$categoryConfigs=array_intersect_key($configTables, array_flip($keywordCategorized));

	// Extract the categories (keywords) for all $categoryConfigs and expose as $<table>Categories (array)
	foreach ($categoryConfigs as $table=>$config)
	{
		$catParam=pkColumnOfTable($table);
		eval("\${$table}Categories=categories(\$config, \$catParam);");
	}

	// If a post form was submitted by clicking a button, then the id of the button is exposed as $postButton (string)
	$postButton=postButton($post);

	// If a form has been submitted, then make changes to the configuration database accordingly
	if (isset($postButton))
	{
		// Extract type from the $postButton and expose as $type (string)
		$type=substr($postButton, 0, -6);

		// Expose the table name of $type as $typeTableName (string)
		$typeTableName=typeTableName($type);

		// Expose the primary key column of $typeTableName as $typeTablePkColumn (string)
		$typeTablePkColumn=pkColumnOfTable($typeTableName);
		
		// Expose the table for $type as $typeTable (array)
		$typeTable=$configTables[$typeTableName];
		
		// Expose the command associated with the $postButton as $command
		$command=$post[$postButton];

		// $sql (string) will contain an sql-query to be executed on the database
		$sql="";

		// If $command is 'copy' and an id was posted, then $sql will contain an sql-query to insert a new row into the database table named by $typeTableName.
		if ($command == 'copy' && isset($post[$type.'Id']))
		{
			if (isset($post['update'.ucfirst($typeTablePkColumn)]))
			{
				$copyId=$post['update'.ucfirst($typeTablePkColumn)];
			}
			else
			{
				$copyId=$post[$type.'Id'];
			}
			while (!isIdUniqueInTable($copyId, $typeTablePkColumn, $typeTable))
			{
				$copyId=$copyId.'-kopia';
			}
			$sql=insertIdSql($copyId, $typeTableName).'; ';
		}

		// If $command is 'create' and a new unique id was posted, then $sql will contain an sql-query to insert a new row into the database table named by $typeTableName
		if ($command == 'create' && !empty($post[$type.'IdNew']))
		{
			$id=$post[$type.'IdNew'];
			if (isIdUniqueInTable($id, $typeTablePkColumn, $typeTable))
			{
				$sql=insertIdSql($id, $typeTableName);
			}
		}
		
		// Else, if $command is 'delete' and the specified id exists, $sql will contain an sql-query to delete a row in the database table named by $typeTableName
		elseif ($command == 'delete' && !empty($post[$type.'IdDel']))
		{
			$id=$post[$type.'IdDel'];
			if (!isIdUniqueInTable($id, $typeTablePkColumn, $typeTable))
			{
				$sql=deleteIdSql($id, $typeTableName);
			}
		}

		// Else, if given input is valid, $sql will contain an sql-query to update a row in the database
		elseif (isset($post[$type.'Id']))
		{

			// The id of the given item (of type $type) is read from $post and is exposed as $id (string)
			$id=$post[$type.'Id'];
			
			// If $command is 'update' or 'copy', then $sql will contain an sql-query to update a row in the database table named by $typeTableName
			if ($command == 'update' || $command == 'copy')
			{
				
				// If $type is 'layer' or 'group', make the posted abstract field html compatible
				if (($type == 'layer' || $type == 'group') && isset($post['updateAbstract']))
				{
					$post['updateAbstract'] = str_replace(["\r\n", "\r", "\n"], "<br>", $post['updateAbstract']);
				}
				
				// Expose posted configuration fields as $updatePosts (array)
				$updatePosts=updatePosts($post);
				
				// Makes sure posted configuration fields are valid before continueing database update, or else aborts and gives an alert 
				validateUpdate($updatePosts, $configTables, $updateValid);
				if ($updateValid)
				{
					$config=array_column_search($id, $typeTablePkColumn, $typeTable);
					
					// If $type is 'layer' or 'source' and is originating from Qgis Server, then $post is updated with information gathered from corresponding Qgis project file
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

					// If $command is 'copy' then use the new $copyId in the update operation
					if ($command == 'copy')
					{
						$config[$typeTablePkColumn]=$copyId;
						$updatePosts['update'.ucfirst($typeTablePkColumn)]=$copyId;
						unset($copyId);
					}

					// $sql is appended with an sql-query to update a row in the database with data from $updatePosts
					$sql=$sql.sqlForUpdate(makeFullTarget($type, $config), $updatePosts);
					unset($config);
				}
				unset($updatePosts, $updateFailed);
			}
			
			// If $command is 'operation', then $sql will contain an sql-query to update a specific field in the configuration database to add or remove a given $id of $type from a given map or group
			elseif ($command == 'operation')
			{
				
				// If a parent has been given (whos config is to be edited) then expose its type, which needs to be either 'map' or 'group', as $parentKey (string)
				if (!empty($post['toMapId']) || !empty($post['fromMapId']))
				{
					$parentKey='map';
				}
				elseif (!empty($post['toGroupId']) || !empty($post['fromGroupId']))
				{
					$parentKey='group';
				}

				// If a parent has been given: 
				// Determine if the operation is 'add' or 'remove' and expose the result as $operation
				// Read the id of the parent from $post and expose as $parentPkColumnValue (string)
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
					
					// If $operation is set, then $sql will contain an sql-query that adds or removes given $id of type $type from given parent $parentPkColumnValue
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

		// if $sql has been set, then perform database operations and re-read data
		if (!empty($sql))
		{
			$result=pg_query($dbh, $sql);
			if (!$result)
			{
				die("Error in SQL query: " . pg_last_error());
			}
			unset($result);
			$configTables=configTables($dbh);
			if ($command != 'operation' && in_array($typeTableName, $keywordCategorized))
			{
				eval("\${$typeTableName}Categories=categories(\$configTables[\$typeTableName], \$typeTablePkColumn);");
			}
		}
		unset($id, $type, $typeTableName, $typeTablePkColumn, $typeTable, $command, $sql);
	}

	// Some common information needs to be passed on every time a form is posted, this info is aggregated in $inheritPosts (array)
	// $inheritPosts is set to include $idPosts, $widthPosts, $heightPosts, $post['groupIds'] and $categoryPosts
	$inheritPosts=array_merge($idPosts, $widthPosts, $heightPosts);

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

			// Include all js-code from the given directory
			includeDirectory("./js-functions/manage");
			
			// Prepare js-code to run later that sets the contents of the item selection boxes based on selected keyword category, store it in $updateSelects (string)
			// Create and expose a js-variable for each keyword category, each containing a list of items that has the specified keyword among their keywords
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
		<?php

			// Include all css-stylesheets from the given directory
			require("./styles/manage.css");
		?>
	</style>
</head>
<body onresize="Array.from(document.getElementsByClassName('resizeimg')).forEach(function(element) { element.onerror(); });">

	<!-- Print the top buttons, including radio buttons to change view -->
	<form action="read_json.php">
		<button title="Importera konfiguration från JSON-fil" class="topButton" type="submit">Importera JSON</button>
	</form>
	<form id="helpForm" action="help.php" target="topFrame">
		<button title="Visa/dölj hjälptext" class="topButton" onclick="toggleTopFrame('help');" type="submit">Hjälp</button>
	</form>
	<?php printViewSwitcher($view); ?>

	<!-- Initialize iframes "topFrame" and "hiddenFrame", start hidden -->
	<iframe id="topFrame" name="topFrame" style="display:none" onload="javascript:(function(o){o.style.height=o.contentWindow.document.body.parentElement.scrollHeight+'px';}(this));"></iframe>
	<iframe id="hiddenFrame" name="hiddenFrame" style="display:none"></iframe>

	<!-- Initialize form "multiselectForm" and set its target to topFrame -->
	<form id="multiselectForm" action="multiselect.php" method="get" target="topFrame"></form>
	
	<!-- Print a row of selection forms. What columns shown depends on the selected view -->
	<?php printHeadForms($view, $configTables, $focusTable, $inheritPosts); ?>
	
	<script>
		<?php

			// Run the prepared js-code stored in $updateSelects
			echo $updateSelects;
			unset($updateSelects);
			
			// Add js-code that populates the keyword category selection boxes
			foreach ($categoryPosts as $postName=>$category)
			{
				$table=substr($postName, 0, -8);
				$type=tableType($table);
				if (isset($idPosts[$type.'Id']))
				{
					$id=$idPosts[$type.'Id'];
					echo <<<HERE
						document.getElementById("{$table}Categories").value="{$category}";
						updateSelect("{$type}Select", {$table}{$category});
						document.getElementById("{$type}Select").value="{$id}";
					HERE;
					unset($id);
				}
				unset($table, $type);
			}
			unset($categoryPosts, $postName, $category);
		?>
	</script>
<?php
/*
 *********************************************
 *  DYNAMIC CONTENTS BASED ON SELECTED ITEM  *
 *********************************************
*/
	// Expose field help ids (identifying fields with existing help text) as $helps (array)
	$helps=array_column($configTables["helps"], "help_id");

	// If a map is selected
	if (isset($post['mapId']))
	{
		// Expose selected map target as $map (array)
		$map=array('map'=>array_column_search($post['mapId'], 'map_id', $configTables['maps']));
		if (!empty(current($map)))
		{
			// Map selectable items (footers, tilegrids) are exposed as $selectables (array)
			$selectables=array(
				'footers'=>array_column($configTables['footers'], 'footer_id'), 
				'tilegrids'=>array_column($configTables['tilegrids'], 'tilegrid_id')
			);
			
			// Print the form for the selected map
			printMapForm($map, $selectables, $inheritPosts, typeHelps("map", $helps));
			
			// Print child select dialogs for layers, groups and controls if any exists for selected map
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
	
	// If a database is selected
	elseif (isset($post['databaseId']))
	{
		// Expose selected database target as $database (array)
		$database=array('database'=>array_column_search($post['databaseId'], 'database_id', $configTables['databases']));
		if (!empty(current($database)))
		{
			// Print the form for the selected database
			printDatabaseForm($database, $inheritPosts, typeHelps("database", $helps));

			// Expose all schema ids of selected database as $databaseSchemas (array)
			$databaseSchemas =preg_grep("/^".$post['databaseId']."[.]/", array_column($configTables['schemas'], 'schema_id'));

			// Add $databaseSchemas to $database
			$database['database']['schemas']='{'.implode(',', $databaseSchemas).'}';

			// Print child select dialog for schemas if any exists for selected database
			echo '<table><tr>';
			$thClass='thFirst';
			printChildSelect($database, 'schemas', $thClass, 'Schema', $inheritPosts);
			echo '</tr></table><hr>';
			unset($databaseSchemas, $thClass);
		}
		unset($database, $idPosts['databaseId']);
	}
	
	// If a schema is selected
	if (isset($post['schemaId']))
	{
		// Expose selected schema target as $schema (array)
		$schema=array('schema'=>array_column_search($post['schemaId'], 'schema_id', $configTables['schemas']));
		if (!empty(current($schema)))
		{
			// Schema selectable items (contacts, origins, updates) are exposed as $selectables (array)
			$selectables=array(
				'contacts'=>array_combine(array_column($configTables['contacts'], 'contact_id'), array_column($configTables['contacts'], 'name')),
				'origins'=>array_combine(array_column($configTables['origins'], 'origin_id'), array_column($configTables['origins'], 'name')),
				'updates'=>array_column($configTables['updates'], 'update_id')
			);

			// Print the form for the selected schema
			printSchemaForm($schema, $selectables, $inheritPosts, typeHelps("schema", $helps));

			// Expose all table ids of selected schema as $schemaTables (array)
			$schemaTables =preg_grep("/^".$post['schemaId']."[.]/", array_column($configTables['tables'], 'table_id'));

			// Add $schemaTables to $schema
			$schema['schema']['tables']='{'.implode(',', $schemaTables).'}';
			
			// Print child select dialog for tables if any exists for selected schema
			echo '<table><tr>';
			$thClass='thFirst';
			printChildSelect($schema, 'tables', $thClass, 'Tabell', $inheritPosts);
			echo '</tr></table><hr>';
			unset($schemaTables, $thClass);
		}
		unset($schema, $idPosts['schemaId']);
	}

	//  (If a group is selected)
	
	// Expose a copy of $groupIdsArray as $tmpGroupIds (array)
	$tmpGroupIds=$groupIdsArray;
	
	// Expose the parent group id of selected group as $parent (string)
	$parent=array_shift($tmpGroupIds);
	
	// Expose the count of total parent group levels as $totGroupLevels (integer)
	$totGroupLevels=count($groupIdsArray);
	$groupLevel=1;
	
	// Loop through the tree of groups where selected group belongs
	foreach ($groupIdsArray as $groupId)
	{
		// Expose current loop group target as $group (array)
		$group=array('group'=>array_column_search($groupId, 'group_id', $configTables['groups']));

		$inheritPosts['groupId']=$groupId;
		if (!empty(current($group)))
		{
			// If there is multiple parents to the selected group (ie parents of parents), use the loop to append them to $parent
			if (count($tmpGroupIds) > 0)
			{
				$parent="$parent,".array_shift($tmpGroupIds);
			}

			// Print the form for the current loop group
			printGroupForm($group, array('maps'=>$configTables['maps'], 'groups'=>$configTables['groups']), $inheritPosts, typeHelps("group", $helps));
			
			// Print child select dialogs for layers and/or groups if any exists for the current loop group
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

	// If any <item> is selected
	if (!empty($idPosts))
	{
		// Expose selected <item> target as $childFullTarget (array)
		$childFullTarget=makeTargetFull(makeBasicTarget(substr(key($idPosts), 0, -2), current($idPosts)), $configTables);

		// Expose the type of the selected <item> as $childType (string)
		$childType=targetType($childFullTarget);

		// Expose the help ids available for $childType as $typeHelps (array)
		$typeHelps=typeHelps($childType, $helps);

		//  If a layer is selected
		if ($childType == 'layer')
		{
			// If the selected layer has a source set, then append the 'service_id' of that source to $childFullTarget and also append the service's 'restricted' as 'service_restricted'.
			$layerSourceId=targetConfigParam($childFullTarget, 'source');
			if (!empty($layerSourceId))
			{
				$layerSource=array_column_search($layerSourceId, 'source_id', $configTables['sources']);
				$layerServiceId=$layerSource['service'];
				if (!empty($layerServiceId))
				{
					setTargetConfigParam($childFullTarget, 'service_id', $layerServiceId);
					$layerService=array_column_search($layerServiceId, 'service_id', $configTables['services']);
					setTargetConfigParam($childFullTarget, 'service_restricted', $layerService['restricted']);
					$layerServiceFormats=pgArrayToPhp($layerService['formats']);
					unset($layerService);
				}
				unset($layerSource, $layerServiceId);
			}
			unset($layerSourceId);

			// Layer selectable items (contacts, origins, formats, updates) are exposed as $selectables (array)
			$selectables=array(
				'contacts'=>array_combine(array_column($configTables['contacts'], 'contact_id'), array_column($configTables['contacts'], 'name')),
				'origins'=>array_combine(array_column($configTables['origins'], 'origin_id'), array_column($configTables['origins'], 'name')),
				'formats'=>$layerServiceFormats,
				'sources'=>array_column($configTables["sources"], "source_id"),
				'updates'=>array_column($configTables['updates'], 'update_id')
			);

			// Print the form for the selected layer
			printLayerForm($childFullTarget, $selectables, array("maps"=>$configTables["maps"], "groups"=>$configTables["groups"]), $inheritPosts, $typeHelps);
			unset($selectables);
		}
		
		//  Else, if a source is selected
		elseif ($childType == 'source')
		{
			// If the selected source has a service set, then append the type of that service to $childFullTarget as 'service_type'.
			$sourceServiceId=targetConfigParam($childFullTarget, 'service');
			if (!empty($sourceServiceId))
			{
				$sourceService=array_column_search($sourceServiceId, 'service_id', $configTables['services']);
				setTargetConfigParam($childFullTarget, 'service_type', $sourceService['type']);
				unset($sourceService);
			}
			unset($sourceServiceId);
			
			// Source selectable items (services, tilegrids, contacts) are exposed as $selectables (array)
			$selectables=array(
				'services'=>array_column($configTables['services'], 'service_id'), 
				'tilegrids'=>array_column($configTables['tilegrids'], 'tilegrid_id'), 
				'contacts'=>array_combine(array_column($configTables['contacts'], 'contact_id'), array_column($configTables['contacts'], 'name'))
			);

			// Print the form for the selected source
			printSourceForm($childFullTarget, $selectables, $inheritPosts, $typeHelps);
			unset($selectables);
		}
		
		// Else, if a table is selected
		elseif ($childType == 'table')
		{
			// Table selectable items (contacts, origins, updates) are exposed as $selectables (array)
			$selectables=array(
				'contacts'=>array_combine(array_column($configTables['contacts'], 'contact_id'), array_column($configTables['contacts'], 'name')),
				'origins'=>array_combine(array_column($configTables['origins'], 'origin_id'), array_column($configTables['origins'], 'name')),
				'updates'=>array_column($configTables['updates'], 'update_id')
			);
			
			// The id of the database where the table is stored is exposed as $databaseId (string)
			$databaseId=substr($childFullTarget['table']['table_id'], 0, strpos($childFullTarget['table']['table_id'], '.'));
			
			// The connection string for $databaseId is exposed as $connectionString (string)
			$connectionString=array_column_search($databaseId, 'database_id', $configTables['databases'])['connectionstring'];
			
			// Print the form for the selected table
			printTableForm($childFullTarget, $connectionString, $selectables, $inheritPosts, $typeHelps);
			unset($selectables, $databaseId, $connectionString);
		}
		
		//  Else, if a control is selected
		elseif ($childType == 'control')
		{
			// Print the form for the selected control
			printControlForm($childFullTarget, array("maps"=>$configTables["maps"]), $inheritPosts, $typeHelps);
		}
		
		// Else, if <item> of other type is selected
		else
		{
			// Print the form for the selected <item> based on its type
			eval('print'.ucfirst($childType).'Form($childFullTarget, $inheritPosts, $typeHelps);');
		}
		unset($childFullTarget, $childType, $typeHelps);
	}
	unset($helps, $idPosts);

?>
</body>
</html>
