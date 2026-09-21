<?php

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