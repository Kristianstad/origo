<?php

	require_once("./functions/pkColumnOfTable.php");
	require_once("./functions/manage/isFullTarget.php");
	require_once("./functions/manage/targetTable.php");

	function targetId($target)
	{
		if (isFullTarget($target))
		{
			$targetTable=targetTable($target);
			$targetPkColumn=pkColumnOfTable($targetTable);
			$targetId=current($target)[$targetPkColumn];
		}
		else
		{
			$targetId=current($target);
		}
		return $targetId;
	}

?>
