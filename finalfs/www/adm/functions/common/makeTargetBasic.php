<?php

	// Takes a target array and returns it as a basic target array.
	function makeTargetBasic($target)
	{
		if (isTarget($target))
		{
			if (!isBasicTarget($target))
			{
				$targetType=targetType($target);
				$target[$targetType]=targetId($target);
			}
			return $target;
		}
		else
		{
			die("makeTargetBasic($target) failed!");
		}
	}