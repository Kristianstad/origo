<?php

	require_once("./functions/manage/isTarget.php");
	require_once("./functions/manage/isBasicTarget.php");
	require_once("./functions/manage/targetType.php");
	require_once("./functions/manage/typeTableName.php");
	require_once("./functions/pkColumnOfTable.php");

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
