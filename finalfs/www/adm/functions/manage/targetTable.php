<?php

	require_once("./functions/manage/isTarget.php");
	require_once("./functions/manage/targetType.php");
	require_once("./functions/manage/typeTableName.php");

	function targetTable($target)
	{
		if (isTarget($target))
		{
			return typeTableName(targetType($target));
		}
		else
		{
			die("targetTable($target) failed!");
		}
	}

?>
