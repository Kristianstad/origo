<?php

	require_once("./functions/manage/isTarget.php");

	// Takes a target array and returns its type (string)
	function targetType($target)
	{
		if (isTarget($target))
		{
			return key($target);
		}
		else
		{
			die("targetType($target) failed!");
		}
	}

?>
