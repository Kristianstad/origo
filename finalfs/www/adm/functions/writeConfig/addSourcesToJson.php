<?php

	// Uses common functions: pgArrayToPhp, array_column_search
	
	// Uses writeConfig functions: pgBoolToText, pgArrayToText, pgBoxToText

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
				$restricted = array_column_search($source['service'], 'service_id', $services, 'restricted');
				$sourceProject = trim(explode('#', $source['source_id'], 2)[0]);
				if (strpos($sourceId, '@wfs') !== false)
				{
					$wfsSource = true;
				}
				else
				{
					$wfsSource = false;
				}
				$url = rtrim($url, '/') . '/' . $sourceProject;
				$queryParams = [];
				if (!$wfsSource) {
					foreach (array_keys($source) as $column) {
						if (in_array($column, $sourcesQueryColumns) && !empty($source[$column])) {
							$queryParams[$column] = pgBoolToText($source[$column]);
						}
					}
				}
				if ($restricted === 't') {
					$queryParams['restricted'] = 't';
				}
				if (!empty($queryParams)) {
					$url .= '?' . http_build_query($queryParams);
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
