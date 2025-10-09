<?php

	// Uses common functions: pgArrayToPhp, array_column_search

	function addControlsToJson($mapControls=null, &$mapCss='', &$mapJs='')
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
			if (!empty($control['css']))
			{
				$mapCss=$mapCss.$control['css'];
			}
			if (!empty($control['js']))
			{
				$mapJs=$mapJs.$control['js'];
			}
		}
		$json = $json.']';
	}

?>
