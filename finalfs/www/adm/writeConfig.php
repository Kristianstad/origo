<?php
	require_once("./functions/dbh.php");
	require_once("./functions/pgArrayToPhp.php");
	require_once("./functions/array_column_search.php");
	require_once("./functions/all_from_table.php");
	require_once("./functions/configTables.php");
	require_once("./functions/includeDirectory.php");
	includeDirectory("./functions/writeConfig");

	require("./constants/webRoot.php");
	$mapId = $_GET['map'];
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
	$json = $json.'"projectionExtent": ['.pgBoxToText($map['projectionextent']).'], ';
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
	$json = $json.'"constrainResolution": '.pgBoolToText($map['constrainresolution']).', ';
	$json = $json.'"resolutions": [ '.pgArrayToText($map['resolutions']).' ]';
	$mapLayers = array('root' => pgArrayToPhp($map['layers']));
	if ($_GET['getHtml'] == 'y' && (!empty($_GET['group']) || !empty($_GET['layer'])))
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
			$mapLayers['root'][]=$_GET['layer'];
		}
	}
	addGroupsToJson($map['groups']);
	$json = $json.', ';
	addLayersToJson($mapLayers);
	$json = $json.' }';
	$json=json_encode(json_decode($json));
	$jsonPretty = json_format($json);
	if ($_GET['getJson'] == 'y')
	{
		if ($_GET['download'] == 'y')
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
				<title>{$map['title']}</title>
				<link rel="shortcut icon" href="{$map['icon']}">
		HERE;
		if ($_GET['getHtml'] == 'y')
		{
			require("./constants/previewBase.php");
			$html = $html."\n\t\t<base href='$previewBase'>";
		}
		$cssFiles = pgArrayToPhp($map['css_files']);
		foreach ($cssFiles as $file)
		{
			if (filter_var($file, FILTER_VALIDATE_URL))
			{
				$css=file_get_contents($file);
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
				$html=$html."\n\t\t<script>".file_get_contents($file)."</script>";
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
					<!--const origoConfig = {$json}; Funkar ej med mapstate?-->
					const origoConfig = 'index.json';
					const origo = Origo(origoConfig);
		HERE;
		if (!empty($map['js']))
		{
			$html=$html."\n{$map['js']}";
		}
		$html=$html. <<<HERE
				</script>
			</body>
		</html>
		HERE;
		if ($_GET['getHtml'] == 'y')
		{
			if ($_GET['download'] == 'y')
			{
				header('Content-Type: application/octet-stream');
				header("Content-Disposition: attachment;filename=$mapId.html");
			}
			echo "$html";
		}
		else
		{
			file_put_contents($htmlFile, $html);
			file_put_contents($jsonFile, $jsonPretty);
			$configSymlink="$webRoot/$mapId.json";
			if (!file_exists("$configSymlink") && !is_link("$configSymlink"))
			{
				symlink("$jsonFile", "$configSymlink");
			}
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
