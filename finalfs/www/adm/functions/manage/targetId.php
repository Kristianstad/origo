<?php

	// Uses common functions: pkColumnOfTable
	
	// Uses manage functions: isFullTarget, targetTable

	function targetId($target)
	{
		if (isFullTarget($target))
		{
			$targetTable=targetTable($target);
			$targetPkColumn=pkColumnOfTable($targetTable);
			$targetId=current($target)[$targetPkColumn];
		}
		else
		{
			$targetId=current($target);
		}
		return $targetId;
	}

?>
