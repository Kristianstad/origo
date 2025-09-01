<?php
	// Tell browsers to not cache response
	header("Cache-Control: must-revalidate, max-age=0, s-maxage=0, no-cache, no-store");
	
	// Expose specific functions
	require_once("./functions/includeDirectory.php");
	
	// Expose all functions in given folders
	includeDirectory("./functions/common");
	includeDirectory("./functions/restrictedLayer");
	
	session_start(array('read_and_close' => true));
	includeFileConstant('RESTRICTEDLAYERS');
	initUser();
	//ini_set('output_buffering', 'off');
	if (isset($_SERVER['QUERY_STRING']))
	{
		$tmpfil='/tmp/'.uniqid(null, true);
		$_SERVER['QUERY_STRING']=str_replace('&?', '&', $_SERVER['QUERY_STRING']);
		$_SERVER['QUERY_STRING']=str_replace('?', '&', $_SERVER['QUERY_STRING']);
		parse_str($_SERVER['QUERY_STRING'], $queryarray);
		$queryarray=array_change_key_case($queryarray, CASE_UPPER);
		$path=$queryarray['PATH'];
		unset ($queryarray['PATH']);
		require("./constants/restrictedServiceUrl.php");
		$call=$restrictedServiceUrl.$path."?".$_SERVER['QUERY_STRING'];
		if ($queryarray['REQUEST'] == 'GetCapabilities' || (!isset($queryarray['FORMAT']) && !isset($queryarray['INFO_FORMAT']) && $queryarray['OUTPUTFORMAT'] == 'geojson'))
		{
			header('Content-Type: text/xml; charset=utf-8');
		}
		elseif (isset($queryarray['INFO_FORMAT']))
		{
			header('Content-Type: '.$queryarray['INFO_FORMAT']);
		}
		else
		{
			header('Content-Type: '.$queryarray['FORMAT']);
		}
		if (isset($queryarray['LAYERS']))
		{
			$callLayers=explode(',', $queryarray['LAYERS']);
		}
		else
		{
			$callLayers=array();
		}
		if (isset($queryarray['LAYER']))
		{
			$callLayers=array_merge($callLayers, explode(',', $queryarray['LAYER']));
		}
		if (isset($queryarray['TYPENAME']))
		{
			$callLayers=array_merge($callLayers, explode(',', $queryarray['TYPENAME']));
		}
		$unrestricted = false;
		//if (stripos($queryarray['SERVICE'], 'wms') !== false && stripos($queryarray['REQUEST'], 'getmap') === false && stripos($queryarray['REQUEST'], 'getfeatureinfo') === false && stripos($queryarray['REQUEST'], 'getlegendgraphic') === false)
		if (stripos($queryarray['SERVICE'], 'wms') !== false && stripos($queryarray['REQUEST'], 'getcapabilities') !== false)
		{
			$unrestricted = true;
		}
		elseif (count($callLayers) === count(array_diff($callLayers, array_column(RESTRICTEDLAYERS, 'name'))))
		{
			$unrestricted = true;
		}
		$headers=array("Connection: close");
		if (!empty($queryarray['TTL']))
		{
			$headers[]="ttl: ".$queryarray['TTL'];
		}
		if ($unrestricted)
		{
			$opts=array('http'=>array('protocol_version'=>1.1, 'method'=>"GET",'header'=>$headers));
			$context=stream_context_create($opts);
			$content=file_get_contents($call, false, $context);
			echo $content;
		}
		else
		{
			header('Restricted: 1');
			if (empty(array_diff($callLayers, authorization_names_filter($callLayers))))
			{
				$opts=array('http'=>array('protocol_version'=>1.1, 'method'=>"GET",'header'=>$headers));
				$context=stream_context_create($opts);
				$content=file_get_contents($call, false, $context);
				echo $content;
			}
			elseif ($queryarray['REQUEST'] == 'GetLegendGraphic')
			{
				$lockPng=file_get_contents('../img/png/lock_yellow.png');
				echo $lockPng;
			}
			elseif ($queryarray['REQUEST'] == 'GetMap')
			{
				$emptyPng=file_get_contents('../img/png/empty.png');
				echo $emptyPng;
			}
			else
			{
				finishError500('saknar rättigheter');
			}
		}
	}

?>
