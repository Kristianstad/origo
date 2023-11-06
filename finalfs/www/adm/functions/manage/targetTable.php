<?php

	require_once("./functions/manage/isTarget.php");
	require_once("./functions/manage/targetType.php");
	require_once("./functions/manage/typeTable.php");

	function targetTable($target)
	{
		if (isTarget($target))
		{
			return typeTable(targetType($target));
		}
		else
		{
			die("targetTable($target) failed!");
		}
	}

?>
