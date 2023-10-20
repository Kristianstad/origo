<?php

	require_once("./functions/pgArrayToPhp.php");
	require_once("./functions/array_column_search.php");

	function addControlsToJson($mapControls=null)
	{
		GLOBAL $json, $map, $controls;
		if (!isset($mapControls))
		{
			$mapControls = pgArrayToPhp($map['controls']);
		}
		$json = $json.'"controls": [';
		$firstControl = true;
		foreach ($mapControls as $control)
		{
			if ($firstControl)
			{
				$firstControl = false;
			}
			else
			{
				$json = $json.', ';
			}
			$control = array_column_search($control, 'control_id', $controls);
			$controlName = trim(explode('#', $control['control_id'], 2)[0]);
			$json = $json.'{ "name": "'.$controlName.'"';
			if (!empty($control['options']) && $control['options'] !== 'null')
			{
				$json = $json.', "options": '.$control['options'];
			}
			$json = $json.' }';
		}
		$json = $json.']';
	}

?>
