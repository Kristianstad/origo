<?php

	require_once("./functions/manage/isTarget.php");
	require_once("./functions/manage/targetType.php");
	require_once("./functions/manage/targetConfig.php");

	// Takes a basic target array as first parameter and configTables (array) or database handle as second parameter.
	// Returns a full target array
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
