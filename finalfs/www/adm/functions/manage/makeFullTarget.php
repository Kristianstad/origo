<?php

	function makeFullTarget($type, $config)
	{
		if (is_string($type) && !empty($type) && is_array($config) && !empty($config))
		{
			return array($type=>$config);
		}
		else
		{
			die("makeFullTarget($type, $config) failed!");
		}
	}

?>

