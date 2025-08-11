<?php

	// Uses common functions: isTarget, makeTargetBasic, findAllParents, assoc_array_values, tableType

	// Takes a database handle and a target, and returns an array of maps that uses this target.
	function usedInMaps(&$dbh, $target, $checkedTargets=array(), $usedInMaps=array())
	{
		if (in_array($target, $checkedTargets))
		{
			return $usedInMaps;
		}
		else
		{
			$checkedTargets[]=$target;
		}
		if (isTarget($target))
		{
			$target=makeTargetBasic($target);
		}
		else
		{
			die("usedInMaps(\$dbh, $target) failed! Child not a target.");
		}
		$targetType=key($target);
		$targetId=current($target);
		if ($targetType == 'map')
		{
			$usedInMaps[]=$targetId;
		}
		else
		{
			$targetParents=findAllParents($dbh, $target);
			foreach ($targetParents as $parentsTable=>$options)
			{
				$parentIds=assoc_array_values($options);
				if ($parentsTable=='maps')
				{
					$usedInMaps=array_merge($usedInMaps, $parentIds);
				}
				else
				{
					foreach ($parentIds as $parentId)
					{
						$usedInMaps=usedInMaps($dbh, array(tableType($parentsTable)=>$parentId), $checkedTargets, $usedInMaps);
					}
				}
			}
		}
		return array_unique($usedInMaps);
	}

?>
