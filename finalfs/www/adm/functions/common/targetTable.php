<?php

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