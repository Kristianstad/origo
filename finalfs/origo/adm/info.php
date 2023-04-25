<!DOCTYPE html>
<html style="width:100%;height:100%">
<head>
	<style>
		<?php require("./styles/info.css"); ?>
	</style>
</head>
<body>
<?php
	header("Cache-Control: must-revalidate, max-age=0, s-maxage=0, no-cache, no-store");

	require_once("./functions/dbh.php");
	require_once("./functions/pgArrayToPhp.php");
	require_once("./functions/array_column_search.php");
	require_once("./functions/all_from_table.php");
	require_once("./functions/findParents.php");
	require_once("./functions/pkColumnOfTable.php");
	require_once("./functions/toSwedish.php");
	require_once("./functions/includeDirectory.php");
	includeDirectory("./functions/info");
	require("./constants/configSchema.php");
	$dbh=dbh();

	$childType=$_GET['type'];
	$childId=$_GET['id'];
	$childTypeSv=toSwedish($childType);
	if (!empty($childId))
	{
		echo "<div style='float:left'>";
		echo "<h2>$childId</h2> ($childTypeSv)</br>";
		$allOfChildType=all_from_table($dbh, $configSchema, $childType.'s');
		$child=array($childType=>$childId);
		$name=array_column_search($childId, pkColumnOfTable($childType.'s'), $allOfChildType)['name'];
		if (!empty($name))
		{
			echo "<b>Namn: </b>$name</br>";
		}
		$alias=array_column_search($childId, pkColumnOfTable($childType.'s'), $allOfChildType)['alias'];
		if (!empty($alias))
		{
			echo "<b>Alias: </b>$alias</br>";
		}
		$info=array_column_search($childId, pkColumnOfTable($childType.'s'), $allOfChildType)['info'];
		if (!empty($info))
		{
			echo "$info</br>";
		}
		if ($childType != 'map')
		{
 			echo "<h3>Används av</h3></br>";
 			if ($childType == 'group' || $childType == 'layer' || $childType == 'control')
			{
				printParents(array('maps'=>all_from_table($dbh, $configSchema, 'maps')), $child);
			}	
			if ($childType == 'group' || $childType == 'layer')
			{
				printParents(array('groups'=>all_from_table($dbh, $configSchema, 'groups')), $child);
			}
			if ($childType == 'source' || $childType == 'contact' || $childType == 'export' || $childType == 'update' || $childType == 'origin' || $childType == 'table')
			{
				printParents(array('layers'=>all_from_table($dbh, $configSchema, 'layers')), $child);
			}
			if ($childType == 'contact')
			{
				printParents(array('schemas'=>all_from_table($dbh, $configSchema, 'schemas')), $child);
				printParents(array('tables'=>all_from_table($dbh, $configSchema, 'tables')), $child);
			}
			if ($childType == 'contact' || $childType == 'service' || $childType == 'tilegrid' || $childType == 'table')
			{
				printParents(array('sources'=>all_from_table($dbh, $configSchema, 'sources')), $child);
			}
		}
		echo '</div>';
		if (strpos($_SERVER['HTTP_REFERER'], 'manage') === false)
		{
			echo '<button onclick="history.back()">Tillbaks</button>';
		}
	}
?>
</body>
</html>
