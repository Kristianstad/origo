<?php

	// Uses common functions: isTarget, targetType
	
	// Uses manage functions: targetTable

	function targetIdColumn($target)
	{
		if (isTarget($target))
		{
			$targetTable=targetTable($target);
			if ($targetTable == 'proj4defs')
			{
				return 'code';
			}
			else
			{
				return targetType($target).'_id';
			}
		}
		else
		{
			die("targetIdColumn($target) failed!");
		}
	}

?>
