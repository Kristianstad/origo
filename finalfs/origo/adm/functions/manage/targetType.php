<?php

	require_once("./functions/manage/isTarget.php");

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
