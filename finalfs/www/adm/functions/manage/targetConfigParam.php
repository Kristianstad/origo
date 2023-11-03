<?php

	require_once("./functions/manage/isFullTarget.php");

	function targetConfigParam($fullTarget, $configParam)
	{
		if (isFullTarget($fullTarget))
		{
			return current($fullTarget)[$configParam];
		}
		else
		{
			die("targetConfigParam($fullTarget, $configParam) failed!");
		}
	}

?>
