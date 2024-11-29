<?php

	require_once("./functions/manage/isFullTarget.php");

	// Takes a full target array and an Origo configuration parameter name (string).
	// Returns the value for the given configuration parameter for the given target
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
