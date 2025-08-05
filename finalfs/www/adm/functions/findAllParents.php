<?php

	// Takes a database handle and a target, and returns an array of all direct parents.
	require_once("./functions/manage/isTarget.php");
	require_once("./functions/manage/makeTargetBasic.php");
	require_once("./functions/all_from_table.php");
	require_once("./functions/findParents.php");

	function findAllParents($dbh, $target)
	{
		if (isTarget($target))
		{
			$target=makeTargetBasic($target);
		}
		else
		{
			die("findAllParents(\$dbh, $target) failed! Child not a target.");
		}
		
		require("./constants/configSchema.php");
		$targetType=key($target);
		$allParents=array();
		if ($targetType != 'map')
		{
 			if ($targetType == 'group' || $targetType == 'layer' || $targetType == 'control' || $targetType == 'keyword')
			{
				$allParents['maps']['maps']=findParents(array('maps'=>all_from_table($dbh, $configSchema, 'maps')), $target);
			}	
			if ($targetType == 'group' || $targetType == 'layer' || $targetType == 'keyword')
			{
				$allParents['groups']['groups']=findParents(array('groups'=>all_from_table($dbh, $configSchema, 'groups')), $target);
			}
			if ($targetType == 'layer' || $targetType == 'source' || $targetType == 'contact' || $targetType == 'export' || $targetType == 'update' || $targetType == 'origin' || $targetType == 'table' || $targetType == 'keyword')
			{
				$allParents['layers']['layers']=findParents(array('layers'=>all_from_table($dbh, $configSchema, 'layers')), $target);
			}
			if ($targetType == 'layer')
			{
				$allParents['layers']['exports']=findParents(array('layers'=>all_from_table($dbh, $configSchema, 'layers')), array('export'=>current($target)));
			}
			if ($targetType == 'contact' || $targetType == 'keyword')
			{
				$allParents['schemas']['schemas']=findParents(array('schemas'=>all_from_table($dbh, $configSchema, 'schemas')), $target);
				$allParents['tables']['tables']=findParents(array('tables'=>all_from_table($dbh, $configSchema, 'tables')), $target);
			}
			if ($targetType == 'contact' || $targetType == 'service' || $targetType == 'tilegrid' || $targetType == 'table')
			{
				$allParents['sources']['sources']=findParents(array('sources'=>all_from_table($dbh, $configSchema, 'sources')), $target);
			}
		}
		return $allParents;
	}

?>
