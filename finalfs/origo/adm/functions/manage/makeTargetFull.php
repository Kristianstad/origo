<?php

	require_once("./functions/manage/isTarget.php");
	require_once("./functions/manage/targetType.php");
	require_once("./functions/manage/targetConfig.php");

	function makeTargetFull($target, $configTablesOrDbh)
	{
		if (isTarget($target))
		{
			return array(targetType($target)=>targetConfig($target, $configTablesOrDbh));
		}
		else
		{
			die("makeTargetFull($target, $configTablesOrDbh) failed!");
		}
	}

?>
