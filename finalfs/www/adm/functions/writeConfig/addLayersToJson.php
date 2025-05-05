<?php

	require_once("./functions/pgArrayToPhp.php");
	require_once("./functions/array_column_search.php");
	require_once("./functions/writeConfig/addSourcesToJson.php");
	require_once("./functions/writeConfig/addStylesToJson.php");

	function addLayersToJson($mapLayersList, &$layersMeta, $groupLayer=false)
	{
		GLOBAL $map, $layers, $json, $mapStyles, $mapSources, $sources, $services, $mapStyleLayers, $contacts, $origins, $tables;
		$json = $json. '"layers": [ ';
		$firstLayer = true;
		if (!isset($mapSources))
		{
			$mapSources = array();
		}
		if (!isset($mapStyleLayers))
		{
			$mapStyleLayers = array();
		}
		if (!isset($mapStyles))
		{
			$mapStyles = array();
		}
		foreach ($mapLayersList as $listItem)
		{
			$listItem=explode('>', $listItem);
			if (isset($listItem[1]))
			{
				$group=explode('#', $listItem[0])[0];
				$layerId=$listItem[1];
			}
			else
			{
				$group='root';
				$layerId=$listItem[0];
			}
			if ($firstLayer)
			{
				$firstLayer = false;
			}
			else
			{
				$json = $json.', ';
			}
			$layer = array_column_search($layerId, 'layer_id', $layers);
			$layersMeta[]=array('title'=>trim(json_encode(trim($layer['title'], " \t\n\r\0\x0B\""), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), '"'), 'abstract'=>trim(json_encode(trim($layer['abstract'], " \t\n\r\0\x0B\""), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), '"'), 'keywords'=>trim(json_encode(str_replace(array("'", "\""), '', trim($layer['keywords'], " \t\n\r\0\x0B\"{}")), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), '"'));
			if ($layer['type'] !== 'GROUP')
			{
				$source = array_column_search($layer['source'], 'source_id', $sources);
				if ($layer['type'] == 'WFS')
				{
					$layer['source'] = $layer['source'].'@wfs';
				}
				$service = array_column_search($source['service'], 'service_id', $services);
				if ($layer['attributes'] == '[]' || $layer['attributes'] == '{}' || $layer['attributes'] == '""' || $layer['attributes'] == 'null')
				{
					$layer['attributes'] = '';
				}
			}
			if ($layer['style_config'] == '[]' || $layer['style_config'] == '{}' || $layer['style_config'] == '""' || $layer['style_config'] == 'null')
			{
				$layer['style_config'] = '';
			}
			// Set default values <start>
			if (empty($layer['type']))
			{
				$layer['type'] = 'WMS';
			}
			if (empty($layer['style_layer']) && ($layer['type'] == 'WMS' || $layer['type'] == 'GROUP' || (!empty($layer['icon']) || !empty($layer['style_config']))))
			{
				$layer['style_layer'] = $layer['layer_id'];
			}
			if (empty($layer['queryable']) && $layer['type'] !== 'GROUP')
			{
				$layer['queryable'] = 't';
			}
			if (empty($layer['visible']))
			{
				$layer['visible'] = 'f';
			}
			if (empty($layer['legend']) && $layer['type'] !== 'GROUP')
			{
				$layer['legend'] = 'f';
			}
			// Set default values </end>
			$layerName = trim(explode('#', $layer['layer_id'], 2)[0]);
			$json = $json.'{ "name": "'.$layerName.'", "title": "'.$layer['title'].'", "type": "'.$layer['type'].'"';
			if (!$groupLayer)
			{
				$json = $json.', "group": "'.$group.'"';
			}
			if (!empty($layer['format']) && $layer['format'] !== 'image/png')
			{
				$json = $json.', "format": "'.$layer['format'].'"';
			}
			if (!empty($layer['attribution']))
			{
				$json = $json.', "attribution": "'.$layer['attribution'].'"';
			}
			if (!empty($layer['style_layer']))
			{
				$styleLayerName = trim(explode('#', $layer['style_layer'], 2)[0]);
				$styleLayer = array_column_search($layer['style_layer'], 'layer_id', $layers);
				if ($group == 'background')
				{
					$layer['style_layer'] = $layer['style_layer'].'-bg';
				}
				if ($styleLayer['show_icon'] != 'f' || $styleLayer['show_iconext'] == 't' || $layer['type'] == 'GROUP' || $layer['type'] == 'GEOJSON')
				{
					$json = $json.', "style": "'.$layer['style_layer'].'"';
				}
			}
			if ($layer['queryable'] == 'f')
			{
				$json = $json.', "queryable": false';
			}
			if ($layer['visible'] == 't')
			{
				$json = $json.', "visible": true';
			}
			elseif ($layer['visible'] == 'f')
			{
				$json = $json.', "visible": false';
			}
			if ($layer['swiper'] == 't')
			{
				$json = $json.', "isSwiperLayer": true';
			}
			elseif ($layer['swiper'] == 'under')
			{
				$json = $json.', "isUnderSwiper": true';
			}
			if (empty($group) && $layer['legend'] == 't')
			{
				$json = $json.', "legend": true';
			}
			if (isset($layer['opacity']) && $layer['opacity'] < 1)
			{
				$json = $json.', "opacity": '.$layer['opacity'];
			}
			if ($layer['type'] == 'GEOJSON')
			{
				$json = $json.', "source": "'.$source['file'].'"';
				$json = $json.', "headers": { "Accept": "application/geo+json" }';
			}
			else
			{
				if ($layer['type'] != 'OSM')
				{
					$json = $json.', "source": "'.$layer['source'].'"';
					if ($layer['type'] == 'WMS')
					{
						if (!empty($layer['gutter']))
						{
							$json = $json.', "gutter": '.$layer['gutter'];
						}
						if ($layer['tiled'] == 'f')
						{
							$json = $json.', "renderMode": "image"';
						}
						if (!empty($layer['featureinfolayer']))
						{
							$json = $json.', "featureinfoLayer": "'.$layer['featureinfolayer'].'"';
						}
					}
					elseif ($layer['type'] == 'WFS')
					{
						$json = $json.', "projection": "EPSG:4326"';
						if ($layer['editable'] == 't')
						{
							$json = $json.', "editable": true';
							if (!empty($layer['allowededitoperations']))
							{
								$allowededitoperations=explode(',', str_replace(["\r\n", "\r", "\n", ' ', '"', '[', ']'], '', $layer['allowededitoperations']));
								array_walk($allowededitoperations, function(&$value, $key) { $value = '"'.$value.'"'; });
								$json = $json.', "allowedEditOperations": ['.implode(',', $allowededitoperations).']';
								unset($allowededitoperations);
							}
							if (!empty($layer['geometryname']))
							{
								$json = $json.', "geometryName": "'.$layer['geometryname'].'"';
							}
							if (!empty($layer['geometrytype']))
							{
								$json = $json.', "geometryType": "'.$layer['geometrytype'].'"';
							}
							if (!empty($layer['featurelistattributes']))
							{
								$featurelistattributes=explode(',', str_replace(["\r\n", "\r", "\n", ' ', '"', '[', ']'], '', $layer['featurelistattributes']));
								array_walk($featurelistattributes, function(&$value, $key) { $value = '"'.$value.'"'; });
								$json = $json.', "featureListAttributes": ['.implode(',', $featurelistattributes).']';
								unset($featurelistattributes);
							}
							if (!empty($layer['drawtools']))
							{
								$json = $json.', "drawTools": '.$layer['drawtools'];
							}
						}
					}
				}
			}
			if (empty($layer['abstract']) && empty($layer['resources']))
			{
				$beskr='';
				foreach (pgArrayToPhp($layer['tables']) as $tableId)
				{
					$table = array_column_search($tableId, 'table_id', $tables);
					if (empty($beskr))
					{
						$beskr=$table['abstract'];
					}
					else
					{
						$beskr=$beskr.' '.$table['abstract'];
					}
				}
			}
			else
			{
				$beskr=$layer['abstract'];
			}
			if (!empty($layer['web']))
			{
				$beskr=$beskr." <a href='".$layer['web']."' target='_blank'>Mer info.</a>";
			}
			if ($layer['show_meta'] != 'f')
			{
				if ($map['show_meta'] == 't')
				{
					$layerContact = array_column_search($layer['contact'], 'contact_id', $contacts);
					if (!empty($layerContact['web']))
					{
						$contactStr="<a href='".$layerContact['web']."' target='_blank'>".$layerContact['name']."</a>";
					}
					elseif (!empty($layerContact['email']))
					{
						$contactStr="<a href='mailto:".$layerContact['email']."'>".$layerContact['name']."</a>";
					}
					elseif (!empty($layerContact['name']))
					{
						$contactStr=$layerContact['name'];
					}
					else
					{
						$contactStr='';
					}
					$layerOrigin = array_column_search($layer['origin'], 'origin_id', $origins);
					if (!empty($layerOrigin['web']))
					{
						$originStr="<a href='".$layerOrigin['web']."' target='_blank'>".$layerOrigin['name']."</a>";
					}
					elseif (!empty($layerOrigin['email']))
					{
						$originStr="<a href='mailto:".$layerOrigin['email']."'>".$layerOrigin['name']."</a>";
					}
					else
					{
						$originStr=$layerOrigin['name'];
					}
					$json = $json.', "abstract": "<b>Beskrivning: </b>'.$beskr.'<br><b>Resurser: </b>';
					$layerTables=trim($layer['tables'], '{}');
					if (empty($layer['resources']))
					{
						$json = $json.str_replace(',', ', ', $layerTables);
					}
					else
					{
						$json = $json.$layer['resources'];
					}
					$json = $json.'<br><b>Kontakt: </b>'.$contactStr.'<br><b>Källa: </b>'.$originStr.'<br>';
					if (!empty($layer['updated']))
					{
						$json = $json.'<b>Uppdaterad: </b>'.$layer['updated'];
					}
					elseif (!empty($layerTables))
					{
						$json = $json."<b>Uppdaterad: </b><iframe id='".$layer['layer_id']."uppd' src='' style='display:none;width:6em;height:1em;padding-top:3px'></iframe><button onclick='var iframe=document.getElementById(\\\"".$layer['layer_id']."uppd\\\");iframe.src=\\\"/php/adm/updated.php?table=".$layerTables."\\\";iframe.style.display=null;this.style.display=\\\"none\\\";'>Visa</button>";
					}
					$json = $json.'<br>';
					unset($layerTables);
				}
				else
				{
					$json = $json.', "abstract": "';
					if (!empty($beskr))
					{
						$json = $json.$beskr.'<br>';
					}
				}
			}
			else
			{
				$json = $json.', "abstract": "';
			}
			$adminForm="<form action='".parse_url($_SERVER["HTTP_REFERER"], PHP_URL_PATH)."' method='post' target='_blank'><button type='submit' name='layerId' value='".$layer['layer_id']."' style='float:right; color:blue'>Administrera</button></form>";
			$json = $json.$adminForm.'"';
			if (!empty($layer['attributes']) && $layer['type'] !== 'GROUP')
			{
				$json = $json.', "attributes": '.$layer['attributes'];
			}
			if (!empty($layer['maxscale']))
			{
				$json = $json.', "maxScale": '.$layer['maxscale'];
			}
			if (!empty($layer['minscale']))
			{
				$json = $json.', "minScale": '.$layer['minscale'];
			}
			if (!empty($layer['layertype']) && $layer['layertype'] !== 'vector')
			{
				$json = $json.', "layerType": "'.$layer['layertype'].'"';
				if ($layer['layertype'] == 'cluster')
				{
					$json = $json.', "clusterStyle": "'.$layer['style_layer'].'-cluster"';
					if (!empty($layer['clusteroptions']) && $layer['clusteroptions'] !== '{}')
					{
						$json = $json.', "clusterOptions": '.$layer['clusteroptions'];
					}
				}
			}
			if ($layer['type'] === 'GROUP')
			{
				$json = $json.', ';
				addLayersToJson(pgArrayToPhp($layer['layers']), $layersMeta, true);
			}
			if (!empty($layer['source']) && !in_array($layer['source'], $mapSources))
			{
				$mapSources[] = $layer['source'];
			}
			if (!empty($layer['style_layer']))
			{
				if ($styleLayer['style_config'] == '[]' || $styleLayer['style_config'] == '{}' || $styleLayer['style_config'] == '""' || $styleLayer['style_config'] == 'null')
				{
					$styleLayer['style_config'] = '';
				}
				if (empty($styleLayer['style_config']))
				{
					$styleSource = array_column_search($styleLayer['source'], 'source_id', $sources);
					$styleService = array_column_search($styleSource['service'], 'service_id', $services);
					if (empty($styleLayer['type']))
					{
						$styleLayer['type'] = 'WMS';
					}
					if (strtoupper($styleLayer['type']) == 'WMS')
					{
						$styleSourceProject = trim(explode('#', $styleSource['source_id'], 2)[0]);
						if (strpos($styleService['base_url'], '?') === false)
						{
							$paramSeparator='?';
						}
						else
						{
							$paramSeparator='&';
						}
						if (isset($styleLayer['show_icon']) && $styleLayer['show_icon'] == 't' && empty($styleLayer['icon']))
						{
							$styleLayer['icon'] = $styleService['base_url'].'/'.$styleSourceProject.$paramSeparator.'SERVICE=WMS&REQUEST=GetLegendGraphic&DPI=96&FORMAT=image/png&ICONLABELSPACE=0&LAYERTITLE=TRUE&RULELABEL=FALSE&ITEMFONTSIZE=1&TRANSPARENT=TRUE&BOXSPACE=1.8&SYMBOLWIDTH=6&SYMBOLHEIGHT=6&LAYERSPACE=5&LAYERTITLESPACE=-6&LAYERFONTSIZE=0.5&LAYERFONTCOLOR=%23FFFFFF&LAYERS='.$styleLayerName;
						}
						if (empty($styleLayer['icon_extended']) && $group != 'background')
						{
							if ($styleLayer['show_icon'] == 'f')
							{
								$styleLayer['icon_extended'] = $styleService['base_url'].'/'.$styleSourceProject.$paramSeparator.'SERVICE=WMS&REQUEST=GetLegendGraphic&DPI=72&FORMAT=image/png&ICONLABELSPACE=3&LAYERTITLE=TRUE&RULELABEL=TRUE&TRANSPARENT=TRUE&BOXSPACE=1&SYMBOLWIDTH=6&SYMBOLHEIGHT=4&SYMBOLSPACE=3&LAYERSPACE=5&LAYERTITLESPACE=-5.3&LAYERFONTSIZE=0.5&LAYERFONTCOLOR=%23FFFFFF&LAYERS='.$styleLayerName;
							}
							else
							{
								$styleLayer['icon_extended'] = $styleService['base_url'].'/'.$styleSourceProject.$paramSeparator.'SERVICE=WMS&REQUEST=GetLegendGraphic&DPI=96&FORMAT=image/png&ICONLABELSPACE=3&LAYERTITLE=TRUE&RULELABEL=TRUE&TRANSPARENT=TRUE&BOXSPACE=1&SYMBOLWIDTH=6&SYMBOLHEIGHT=4&SYMBOLSPACE=3&LAYERSPACE=5&LAYERTITLESPACE=-5.3&LAYERFONTSIZE=0.5&LAYERFONTCOLOR=%23FFFFFF&LAYERS='.$styleLayerName;
							}
						}
					}
					elseif (strtoupper($styleLayer['type']) == 'WFS')
					{
						$styleLayer['label'] = $styleLayer['title'];
					}
					if (isset($styleLayer['show_icon']) && $styleLayer['show_icon'] == 't' && !empty($styleLayer['icon']))
					{
						if (strpos($styleLayer['icon'], '?') === false)
						{
							$styleLayer['icon'] = $styleLayer['icon'].'?';
						}
						else
						{
							$styleLayer['icon'] = $styleLayer['icon'].'&';
						}
						$styleLayer['icon'] = $styleLayer['icon'].'ttl=36000';
					}
					else
					{
						unset($styleLayer['icon']);
						if (($styleLayer['show_iconext'] != 't' || empty($styleLayer['icon_extended'])) && $layer['type'] != 'GROUP')
						{
							unset($styleLayer['icon_extended']);
							$json = $json.', "hasThemeLegend":true';
							if ($styleLayer['thematicstyling'] == 't')
							{
								$json = $json.', "thematicStyling":true';
								$json = $json.', "legendParams" : { "FORMAT" : "image/png", "LAYERTITLE" : true, "SHOWRULEDETAILS": true, "TRANSPARENT" : true }';
							}
							else
							{
								$json = $json.', "legendParams" : { "FORMAT" : "image/png", "LAYERTITLE" : true, "ICONLABELSPACE" : 3, "RULELABEL" : true, "TRANSPARENT" : true, "BOXSPACE" : 3, "SYMBOLWIDTH" : 6, "SYMBOLHEIGHT" : 4, "SYMBOLSPACE" : 2, "LAYERSPACE" : 5, "LAYERTITLESPACE" : -7, "LAYERFONTSIZE" : 0.5, "LAYERFONTCOLOR" : "#FFFFFF", "ITEMFONTSIZE" : 8 }';
							}
						}
					}
					if ($styleLayer['show_iconext'] != 'f' && !empty($styleLayer['icon_extended']))
					{
						if (strpos($styleLayer['icon_extended'], '?') === false)
						{
							$styleLayer['icon_extended'] = $styleLayer['icon_extended'].'?';
						}
						else
						{
							$styleLayer['icon_extended'] = $styleLayer['icon_extended'].'&';
						}
						$styleLayer['icon_extended'] = $styleLayer['icon_extended'].'ttl=36000';
					}
				}
				$styleLayerId=$styleLayer['layer_id'];
				if ($group == 'background')
				{
					$styleLayer['layer_id'] = $styleLayerId.'-bg';
				}
				if (!in_array($styleLayer['layer_id'], $mapStyleLayers))
				{
					$mapStyleLayers[] = $styleLayer['layer_id'];
					$mapStyles[] = $styleLayer;
					if (!empty($styleLayer['clusterstyle']) && $styleLayer['clusterstyle'] !== '[]')
					{
						$styleLayer['layer_id'] = $styleLayerId.'-cluster';
						if (!in_array($styleLayer['layer_id'], $mapStyleLayers))
						{
							$mapStyleLayers[] = $styleLayer['layer_id'];
							$styleLayer['style_config']=$styleLayer['clusterstyle'];
							$mapStyles[] = $styleLayer;
						}
					}
				}
			}
			$json = $json.'}';
		}
		$json = $json.' ]';
		if (!$groupLayer)
		{
			$json = $json.', ';
			addSourcesToJson();
			$json = $json.', ';
			addStylesToJson();
		}
	}

?>
