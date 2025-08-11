<?php

	// Uses manage functions: isFullTarget

	// Takes a full target array, an Origo configuration parameter name (string), and a configuration parameter value.
	// Sets the configuration parameter to the new value in the given target.
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
