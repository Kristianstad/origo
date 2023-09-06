<?php

	require_once("./functions/manage/isFullTarget.php");

	function setTargetConfigParam(&$fullTarget, $configParam, $value)
	{
		if (isFullTarget($fullTarget))
		{
			$fullTarget[key($fullTarget)][$configParam]=$value;
		}
		else
		{
			die("setTargetConfigParam(&$fullTarget, $configParam, $value) failed!");
		}
	}

?>
