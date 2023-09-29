<?php

	require_once("./functions/pgArrayToPhp.php");

	function categories($config, $catParam)
	{
		$categories=array("Alla" => array_column($config, $catParam));
		foreach (array_filter(array_column($config, 'keywords', $catParam)) as $id => $pgarray)
		{
			foreach (pgArrayToPhp($pgarray) as $category)
			{
				if (!isset($categories[$category]))
				{
					$categories[$category]=array();
				}
				$categories[$category][]=$id;
			}
		}
		return $categories;
	}

?>
