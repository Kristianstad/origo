<?php
	// Note! Can't begin with <!DOCTYPE html> because it causes the swiper-plugin to stop working in the preview.

	// Tell browsers to not cache response
	header("Cache-Control: must-revalidate, max-age=0, s-maxage=0, no-cache, no-store");

	// Expose specific functions
	require_once("./functions/includeDirectory.php");

	// Expose all functions in given folders
	includeDirectory("./functions/common");
	includeDirectory("./functions/writeConfig");

	require("./constants/webRoot.php");
	
	// Workaround for swiper in preview https://github.com/SigtunaGIS/swiper-plugin/issues/41 <start>
	$getMapParam=explode('\\', $_GET['map'], 2);
	$mapId = $getMapParam[0];
	if (isset($getMapParam[1]))
	{
		$_GET['getJson'] = 'y';
	}
	// Workaround for swiper in preview </end>
	
	$mapIdArray = explode('#', $mapId, 2);
	$mapName = trim($mapIdArray[0]);
	if (!empty($mapIdArray[1]))
	{
		$mapNumber = trim($mapIdArray[1]);
	}
	else
	{
		$mapNumber = '';
	}
	$configDir="$webRoot/$mapName";
	if (!file_exists("$configDir"))
	{
		mkdir("$configDir");
		chmod("$configDir", 0770);
	}
	$htmlFile = "$configDir/index$mapNumber.html";
	$jsonFile = "$configDir/index$mapNumber.json";
	$sitemapFile = "$configDir/sitemap.xml";
	ignore_user_abort(true);
	$dbh=dbh();
	$configTables=configTables($dbh);
	extract($configTables);
	$map = array_column_search($mapId, 'map_id', $maps);
	$json = '{ ';
	$mapControls = pgArrayToPhp($map['controls']);
	addControlsToJson($mapControls);
	$json = $json.', ';
	// PageSettings <start>
	$json = $json.'"pageSettings": {';
	if (!empty($map['footer']))
	{
		$footer = array_column_search($map['footer'], 'footer_id', $footers);
		if (!empty($footer['text']))
		{
			$footerText=', "text": "'.$footer['text'].'"';
		}
		else
		{
			$footerText='';
		}
		$json = $json.'"footer": { "img": "'.$footer['img'].'", "url" : "'.$footer['url'].'"'.$footerText.' },';
	}
	$json = $json.'"mapGrid": { "visible": '.pgBoolToText($map['mapgrid']).' }';
	if ($map['embedded'] == 'f')
	{
		$json=$json.', "mapInteractions": { "embedded": '.pgBoolToText($map['embedded']).' }';
	}
	$json = $json.' },';
	// PageSettings </end>
	$json = $json.'"projectionCode": "'.$map['projectioncode'].'", ';
	if (!empty(array_column_search($map['projectioncode'], 'code', $proj4defs)['projectionextent']))
	{
		$mapProjectionExtent=pgBoxToText(array_column_search($map['projectioncode'], 'code', $proj4defs)['projectionextent']);
		$json = $json.'"projectionExtent": ['.$mapProjectionExtent.'], ';
	}
	else
	{
		if ($map['projectioncode'] != 'EPSG:3857' && $map['projectioncode'] != 'EPSG:4326')
		{
			require_once("./constants/proxyRoot.php");
			echo '<script>alert("Projektionsutbredning saknas! Ingen konfiguration skriven."); window.location.href="'.$proxyRoot.$_SERVER["REQUEST_URI"].'&badJson=y";</script>';
			exit;
		}
	}
	$json = $json.'"featureinfoOptions": '.$map['featureinfooptions'].', ';
	if (!empty($map['tilegrid']))
	{
		$tilegrid = array_column_search($map['tilegrid'], 'tilegrid_id', $tilegrids);
		$json = $json.'"tileGridOptions": { "tileSize": '.$tilegrid['tilesize'].' },';
	}
	// Proj4Defs <start>
	$mapProj4defs = pgArrayToPhp($map['proj4defs']);
	$json = $json.'"proj4Defs": [';
	$firstProj4def = true;
	foreach ($mapProj4defs as $proj4def)
	{
		if ($firstProj4def)
		{
			$firstProj4def = false;
		}
		else
		{
			$json = $json.', ';
		}
		$proj4def = array_column_search($proj4def, 'code', $proj4defs);
		if (!empty($proj4def['alias']))
		{
			$proj4defAlias=', "alias": "'.$proj4def['alias'].'"';
		}
		else
		{
			$proj4defAlias='';
		}
		$json = $json.'{ "code": "'.$proj4def['code'].'", "projection": "'.$proj4def['projection'].'"'.$proj4defAlias.' }';
	}
	$json = $json.'], ';
	// Proj4Defs </end>
	$json = $json.'"extent": ['.pgBoxToText($map['extent']).'], ';
	$json = $json.'"center": ['.pgCoordsToText($map['center']).'], ';
	$json = $json.'"zoom": '.$map['zoom'].', ';
	$json = $json.'"enableRotation": '.pgBoolToText($map['enablerotation']).', ';
	if (!empty($map['palette']))
	{
		$json = $json.'"palette": '.$map['palette'].', ';
	}
	$json = $json.'"constrainResolution": '.pgBoolToText($map['constrainresolution']).', ';
	$json = $json.'"resolutions": [ '.pgArrayToText($map['resolutions']).' ]';
	$mapLayers=array();
	if (!empty(pgArrayToPhp($map['layers'])))
	{
		$mapLayers['root'] = pgArrayToPhp($map['layers']);
		$mapLayers['root'] = preg_filter('/^/', 'root>', $mapLayers['root']);
	}
	if (isset($_GET['getHtml']) && $_GET['getHtml'] == 'y' && (!empty($_GET['group']) || !empty($_GET['layer'])))
	{
		if (!empty($_GET['group']))
		{
			if (empty(trim($map['groups'], '{}')) || explode('#', $_GET['group'], 2)[0] == 'background')
			{
				$map['groups']='{'.$_GET['group'].'}';
			}
			else
			{
				$map['groups']='{'.$_GET['group'].','.trim($map['groups'], '{}').'}';
			}
		}
		elseif (!empty($_GET['layer']))
		{
			$mapLayers['root']=array();
			$mapLayers['root'][]='root>'.$_GET['layer'];
		}
	}
	$mapGroups = pgArrayToPhp($map['groups']);
	$mapLayerIds=groupDepth($mapGroups, $mapLayers);
	$mapLayersList=getArrayValuesRecursively($mapLayerIds);
	$mapLayersList=indexweightedLayersList($mapLayersList);
	addGroupsToJson($map['groups']);
	$json = $json.', ';
	$layersMeta=array();
	addLayersToJson($mapLayersList, $layersMeta);
	$json = $json.' }';
	if (isset($_GET['badJson']))
	{
		header('Content-Type: application/octet-stream');
		header("Content-Disposition: attachment;filename=$mapId.json");
		echo "$json";
		exit;
	}
	if (json_decode($json) === null)
	{
		require_once("./constants/proxyRoot.php");
		echo '<script>alert("Fel i Json! Ingen konfiguration skriven."); window.location.href="'.$proxyRoot.$_SERVER["REQUEST_URI"].'&badJson=y";</script>';
		exit;
	}
	$json=json_encode(json_decode($json), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
	$jsonPretty = json_format($json);
	if (isset($_GET['getJson']) && $_GET['getJson'] == 'y')
	{
		if (isset($_GET['download']) && $_GET['download'] == 'y')
		{
			header('Content-Type: application/octet-stream');
			header("Content-Disposition: attachment;filename=$mapId.json");
		}
		echo "$jsonPretty";
	}
	else
	{
		$html = <<<HERE
		<!DOCTYPE html>
		<html lang="sv">
			<head>
				<meta charset="utf-8">
				<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
				<meta http-equiv="X-UA-Compatible" content="IE=Edge;chrome=1">
				<meta name="robots" content="index, follow">
				<title>{$map['title']}</title>
				<link rel="shortcut icon" href="{$map['icon']}">
		HERE;
		if (isset($_GET['getHtml']) && $_GET['getHtml'] == 'y')
		{
			require("./constants/previewBase.php");
			$html = $html."\n\t\t<base href='$previewBase'>";
		}
		/*
		elseif ($map['searchengineindexable'] == "t")
		{
			$html = $html."\n\t\t<script type=\"application/ld+json\" src=\"structured-data".$mapNumber.".json\"></script>";
		}
		*/
		$cssFiles = pgArrayToPhp($map['css_files']);
		foreach ($cssFiles as $file)
		{
			if (filter_var($file, FILTER_VALIDATE_URL))
			{
				$css=file_get_contents($file);
				if ($css === false)
				{
					require_once("./constants/proxyRoot.php");
					echo '<script>alert("Filen '.$file.' kunde inte läsas! Ingen konfiguration skriven."); window.location.href="'.$proxyRoot.$_SERVER["REQUEST_URI"].'&badJson=y";</script>';
					exit;
				}
				// Remove comments
 				$css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
 				// Remove spaces before and after selectors, braces, and colons
 				$css = preg_replace('/\s*([{}|:;,])\s+/', '$1', $css);
 				// Remove remaining spaces and line breaks
 				$css = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '',$css);
				$html=$html."\n\t\t<style>$css</style>";
				unset($css);
			}
			else
			{
				$html=$html."\n\t\t<link href='$file' rel='stylesheet'>";
			}
		}
		if (!empty($map['css']))
		{
			$css=$map['css'];
			// Remove comments
 			$css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
 			// Remove spaces before and after selectors, braces, and colons
 			$css = preg_replace('/\s*([{}|:;,])\s+/', '$1', $css);
 			// Remove remaining spaces and line breaks
 			$css = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '',$css);
			$html=$html."\n\t\t<style>$css</style>";
			unset($css);
		}
		$jsFiles = pgArrayToPhp($map['js_files']);
		foreach ($jsFiles as $file)
		{
			if (filter_var($file, FILTER_VALIDATE_URL))
			{
				$js=file_get_contents($file);
				if ($js === false)
				{
					require_once("./constants/proxyRoot.php");
					echo '<script>alert("Filen '.$file.' kunde inte läsas! Ingen konfiguration skriven."); window.location.href="'.$proxyRoot.$_SERVER["REQUEST_URI"].'&badJson=y";</script>';
					exit;
				}
				$html=$html."\n\t\t<script>".$js."</script>";
			}
			else
			{
				$html=$html."\n\t\t<script src='$file'></script>";
			}
		}
		$html=$html."\n". <<<HERE
			</head>
			<body>
				<div id="app-wrapper"></div>
				<script>
					const urlParams = new URL(document.location.href).searchParams;
					let origo;
		HERE;
		//const origoConfig = {$json}; Funkar ej med mapstate?
		if (isset($_GET['getHtml']) && $_GET['getHtml'] == 'y')
		{
			$html=$html. <<<HERE

						const origoConfig = {$json};
						origo = Origo(origoConfig);

			HERE;
		}
		else
		{
			$html=$html. <<<HERE

						const map = urlParams.get('map');
						if (map != null)
						{
							origo = Origo(map.replace('#', '%23') + '.json');
						}
						else
						{
							const htmlFileMatch = document.location.pathname.match(/([^\/]+)\.html$/i);
							if (htmlFileMatch)
							{
								origo = Origo(htmlFileMatch[1] + '.json');
							}
							else
							{
								origo = Origo('index.json');
							}
						}

			HERE;
		}
		if (!empty($map['js']))
		{
			$html=$html."\n{$map['js']}\n";
		}
		$html=$html. <<<HERE
		</script>
		
		HERE;
		if (isset($_GET['getHtml']) && $_GET['getHtml'] == 'y')
		{
			$html=$html. <<<HERE
			</body>
			</html>
			HERE;
			if (isset($_GET['download']) && $_GET['download'] == 'y')
			{
				header('Content-Type: application/octet-stream');
				header("Content-Disposition: attachment;filename=$mapId.html");
			}
			echo "$html";
		}
		else
		{
			if ($map['searchengineindexable'] == "t")
			{
				$mapTitle=json_encode(trim($map['title'], " \t\n\r\0\x0B\""), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
				$mapAbstract=json_encode(trim($map['abstract'], " \t\n\r\0\x0B\""), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
				$mapUrl=trim(json_encode(trim($map['url'], " \t\n\r\0\x0B\""), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), '"');
				$tmpMapKeywords=array_unique(array_filter(array_column($layersMeta, 'keywords')));
				$mapKeywords=array();
				foreach ($tmpMapKeywords as $layerKeywords)
				{
					$mapKeywords=array_merge($mapKeywords, explode(',', $layerKeywords));
				}
				$mapKeywords=array_unique($mapKeywords);
				$mapKeywords=implode(',', $mapKeywords);
				$structuredDataJson = <<<HERE
				{
					"@context": "https://schema.org",
					"@type": "WebPage",
					"name": {$mapTitle},
					"description": {$mapAbstract},
					"url": "{$mapUrl}",
					"keywords": "{$mapKeywords}",
					"about": [ 
				HERE;
				$firstTitle=true;
				foreach ($layersMeta as $lmeta)
				{
					if ($firstTitle)
					{
						$firstTitle=false;
					}
					else
					{
						$structuredDataJson=$structuredDataJson.', ';
					}
					$structuredDataJson=$structuredDataJson.'{ "@type": "Thing", "name": "'.$lmeta['title'].'"';
					if (isset($lmeta['abstract']) && !empty($lmeta['abstract']) && $lmeta['abstract'] !== 'null')
					{
						$structuredDataJson=$structuredDataJson.', "description": "'.$lmeta['abstract'].'"';
					}
					if (isset($lmeta['keywords']) && !empty($lmeta['keywords']) && $lmeta['keywords'] !== 'null')
					{
						$structuredDataJson=$structuredDataJson.', "keywords": "'.$lmeta['keywords'].'"';
					}
					$structuredDataJson=$structuredDataJson.' }';
				}
				require("./constants/searchEngineMeta.php");
				$structuredDataJson=$structuredDataJson.<<<HERE
					],
					"geo": {
						"@type": "GeoCoordinates",
						"latitude": {$geoLatitude},
						"longitude": {$geoLongitude}
					},
					"contentLocation": {
						"@type": "Place",
						"name": "{$contentLocationName}",
						"address": {
							"@type": "PostalAddress",
							"addressLocality": "{$contentLocationAddressLocality}",
							"addressCountry": "{$contentLocationAddressCountry}"
						}
					},
					"publisher": {
						"@type": "Organization",
						"name": "{$publisherName}",
						"url": "{$publisherUrl}"
					}
				}
				HERE;
				$structuredDataJson=json_encode(json_decode($structuredDataJson), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
				$html=$html. <<<HERE
				<script type="application/ld+json">
				{$structuredDataJson}
				</script>
				
				HERE;
				$structuredDataJson=json_format($structuredDataJson);
				$structuredDataFile = "$configDir/structured-data$mapNumber.json";
				file_put_contents($structuredDataFile, $structuredDataJson);
				$sitemapStr = <<<HERE
				<?xml version="1.0" encoding="UTF-8"?>
				<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
				  <url>
				    <loc>{$mapUrl}</loc>
				    <changefreq>weekly</changefreq>
				    <priority>1.0</priority>
				  </url>
				</urlset>
				HERE;
				file_put_contents($sitemapFile, $sitemapStr);
			}
			$html=$html. <<<HERE
			</body>
			</html>
			HERE;
			file_put_contents($htmlFile, $html);
			file_put_contents($jsonFile, $jsonPretty);
			$htmlSymlink="$webRoot/$mapId.html";
			$configSymlink="$webRoot/$mapId.json";
			if (!file_exists("$htmlSymlink") && !is_link("$htmlSymlink"))
			{
				symlink("$htmlFile", "$htmlSymlink");
			}
			if (!file_exists("$configSymlink") && !is_link("$configSymlink"))
			{
				symlink("$jsonFile", "$configSymlink");
			}
			markMapUnchanged($dbh, $mapId);
			require("./constants/configSchema.php");
			$layers=all_from_table($dbh, $configSchema, 'layers');
			$sources=all_from_table($dbh, $configSchema, 'sources');
			unset($configSchema);
			$restrictedLayers=array();
			foreach ($layers as $layer)
			{
				$layerService=array_column_search($layer['source'], 'source_id', $sources)['service'];
				if ($layerService == 'restricted')
				{
					$restrictedLayers[]=array('name' => explode('#', $layer['layer_id'])[0], 'authorized_users' => $layer['adusers'], 'authorized_groups' => $layer['adgroups']);
				}
			}
			array_walk($restrictedLayers, function(&$restrictedLayer) {
				$restrictedLayer['authorized_users'] = pgArrayToPhp(str_replace('"', '', (strtolower($restrictedLayer['authorized_users']))));
				$restrictedLayer['authorized_groups'] = pgArrayToPhp(str_replace('"', '', (strtolower($restrictedLayer['authorized_groups']))));
			});
			defineFileConstant('RESTRICTEDLAYERS', $restrictedLayers);
		}
	}
?>
