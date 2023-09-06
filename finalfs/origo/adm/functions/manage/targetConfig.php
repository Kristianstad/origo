<?php

	require_once("./functions/array_column_search.php");
	require_once("./functions/manage/isTarget.php");
	require_once("./functions/manage/isFullTarget.php");
	require_once("./functions/manage/targetId.php");
	require_once("./functions/manage/targetIdColumn.php");
	require_once("./functions/manage/targetTable.php");
	require_once("./functions/manage/tableConfigs.php");

	function targetConfig($target, $configTablesOrDbh=null)
	{
		if (isTarget($target))
		{
			if (isFullTarget($target))
			{
				$config=current($target);
			}
			elseif (isset($configTablesOrDbh))
			{
				$config=array_column_search(targetId($target), targetIdColumn($target), tableConfigs(targetTable($target), $configTablesOrDbh));
			}
			else
			{
				die("targetConfig($target, $configTablesOrDbh=null) failed!");
			}
			return $config;
		}
		else
		{
			die("targetConfig($target, $configTablesOrDbh=null) failed!");
		}
	}

?>
