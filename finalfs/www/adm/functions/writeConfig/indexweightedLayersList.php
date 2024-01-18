<?php

	require_once("./functions/array_column_search.php");
	require_once("./functions/writeConfig/array_move.php");

	function indexweightedLayersList($layersList)
	{
		GLOBAL $layers;
		$layerweights=array();
		$whileDo=true;
		while ($whileDo)
		{
			$whileDo=false;
			foreach ($layersList as $key=>$listItem)
			{
				$layerId=explode('>', $listItem)[1];
				$layer=array_column_search($layerId, 'layer_id', $layers);
				if (isset($layer['indexweight']) && !isset($layerweights[$layerId]))
				{
					$layerweights[$layerId]=$layer['indexweight'];
					$from=$key;
					$to=$key-$layer['indexweight'];
					array_move($layersList, $from, $to);
					$whileDo=true;
					break;
				}
			}
		}
		return $layersList;
	}

?>
