<?php

	require_once("./functions/pgArrayToPhp.php");
	require_once("./functions/array_column_search.php");
	require_once("./functions/writeConfig/pgBoolToText.php");
	require_once("./functions/writeConfig/pgArrayToText.php");
	require_once("./functions/writeConfig/pgBoxToText.php");

	function addSourcesToJson()
	{
		GLOBAL $json, $mapSources, $map, $sources, $services, $tilegrids;
		require("./constants/sourcesQueryColumns.php");
		$json = $json.'"source": { ';
		if (!is_array($mapSources))
		{
			$mapSources = pgArrayToPhp($mapSources);
		}

		$mapSources = array_unique($mapSources);
		$firstSource = true;
		foreach ($mapSources as $sourceId)
		{
			$source = array_column_search(trim(explode('@', $sourceId, 2)[0]), 'source_id', $sources);
			if (!empty($source))
			{
				if ($firstSource)
				{
					$firstSource = false;
				}
				else
				{
					$json = $json.', ';
				}
				$type = array_column_search($source['service'], 'service_id', $services, 'type');
				$url = array_column_search($source['service'], 'service_id', $services, 'base_url');
				$sourceProject = trim(explode('#', $source['source_id'], 2)[0]);
				if (strpos($sourceId, '@wfs') !== false)
				{
					$wfsSource = true;
				}
				else
				{
					$wfsSource = false;
				}
				$url = rtrim($url, '/').'/'.$sourceProject;
				$sourceColumns = array_keys($source);
				$queryColumns = array();
				if (!$wfsSource)
				{
					foreach ($sourceColumns as $column)
					{
						if (in_array($column, $sourcesQueryColumns) && !empty($source[$column]))
						{
							$queryColumns[] = $column;
						}
					}
					foreach ($queryColumns as $query)
					{
						if (strpos($url, '?') === false)
						{
							$url = $url.'?';
						}
						else
						{
							$url = $url.'&';
						}
						$url = $url.$query.'='.pgBoolToText($source[$query]);
					}
				}
				$json = $json.'"'.$sourceId.'": { "url": "'.$url.'"';
				if ($wfsSource)
				{
					$json = $json.', "workspace": "qgs"';
				}
				if (!empty($type))
				{
					$json = $json.', "type": "'.$type.'"';
				}
				if (!empty($source['tilegrid']))
				{
					$tilegrid = array_column_search($source['tilegrid'], 'tilegrid_id', $tilegrids);
					$json = $json.', "tileGrid": { ';
					if (!empty($tilegrid['tilesize']))
					{
						$json = $json.'"tileSize": '.$tilegrid['tilesize'].', ';
					}
					if (!empty($tilegrid['resolutions']))
					{
						$resolutions=$tilegrid['resolutions'];
					}
					else
					{
						$resolutions=$map['resolutions'];
					}
					$json = $json.'"resolutions": [ '.pgArrayToText($resolutions).' ], ';
					if (!empty($tilegrid['extent']))
					{
						$extent=$tilegrid['extent'];
					}
					else
					{
						$extent=$map['extent'];
					}
					$json = $json.'"extent": ['.pgBoxToText($extent).'] ';
					$json = $json.'}';
				}
				$json = $json.'}';
			}
		}
		$json = $json.' }';
	}

?>
