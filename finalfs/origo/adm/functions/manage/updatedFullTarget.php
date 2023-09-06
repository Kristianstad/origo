<?php

	require_once("./functions/manage/isFullTarget.php");
	require_once("./functions/manage/targetConfig.php");
	require_once("./functions/manage/isArrayColumn.php");
	require_once("./functions/manage/makeFullTarget.php");
	require_once("./functions/manage/targetType.php");

	function updatedFullTarget($fullTarget, $updatePosts)
	{
		if (isFullTarget($fullTarget))
		{
			$config=targetConfig($fullTarget);
			foreach ($config as $column=>$value)
			{
				$newValue=$updatePosts['update'.ucfirst($column)];
				if (!isset($newValue))
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