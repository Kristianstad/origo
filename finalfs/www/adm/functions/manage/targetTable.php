<?php

	// Uses common functions: isTarget, targetType, typeTableName

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
