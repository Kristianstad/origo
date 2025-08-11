<?php

	// Uses common functions: isTarget, targetType
	
	// Uses manage functions: targetConfig

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
