<?php

	// Uses common functions: targetType
	
	// Uses manage functions: isFullTarget, targetConfig, isArrayColumn, makeFullTarget

	function updatedFullTarget($fullTarget, $updatePosts)
	{
		if (isFullTarget($fullTarget))
		{
			$config=targetConfig($fullTarget);
			foreach ($config as $column=>$value)
			{
				if (isset($updatePosts['update'.ucfirst($column)]))
				{
					$newValue=$updatePosts['update'.ucfirst($column)];
				}
				else
				{
					$newValue='';
				}
				if (isArrayColumn($column))
				{
					$newValue='{'.$newValue.'}';
				}
				$config[$column]=$newValue;
			}
			return makeFullTarget(targetType($fullTarget), $config);
		}
		else
		{
			die("updatedFullTarget($fullTarget, $updatePosts) failed!");
		}
	}

?>
