<?php

	// Uses common functions: array_column_search, isTarget
	
	// Uses manage functions: isFullTarget, targetId, targetIdColumn, targetTable, tableConfigs

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
