<?php

	// Uses common functions: pgArrayToPhp, array_column_search

	function addPlugins($mapPlugins=null, &$mapCssFiles=array(), &$mapJsFiles=array(), &$mapCss='', &$mapJs='')
	{
		GLOBAL $map, $plugins;
		if (!isset($mapPlugins))
		{
			$mapPlugins = pgArrayToPhp($map['plugins']);
		}
		foreach ($mapPlugins as $plugin)
		{
			$plugin = array_column_search($plugin, 'plugin_id', $plugins);
			if (!empty($plugin['js']))
			{
				$mapJs=$mapJs.$plugin['js'];
			}
			if (!empty($plugin['css']))
			{
				$mapCss=$mapCss.$plugin['css'];
			}
			if (!empty($plugin['js_files']))
			{
				$mapJsFiles=array_merge($mapJsFiles, pgArrayToPhp($plugin['js_files']));
			}
			if (!empty($plugin['css_files']))
			{
				$mapCssFiles=array_merge($mapCssFiles, pgArrayToPhp($plugin['css_files']));
			}
		}
	}

?>
