<?php

	// Uses common functions: isTarget, isBasicTarget, targetType, typeTableName, pkColumnOfTable

	// Takes a target array and returns it as a basic target array.
	function makeTargetBasic($target)
	{
		if (isTarget($target))
		{
			if (!isBasicTarget($target))
			{
				$targetType=targetType($target);
				$targetTableName=typeTableName($targetType);
				$targetIdColumn=pkColumnOfTable($targetTableName);
				$target[$targetType]=$target[$targetType][$targetIdColumn];
			}
			return $target;
		}
		else
		{
			die("makeTargetBasic($target) failed!");
		}
	}

?>
