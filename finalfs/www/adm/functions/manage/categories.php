<?php

	require_once("./functions/pgArrayToPhp.php");

	function categories($config, $catParam)
	{
		$categories=array();
		foreach (str_replace('"', '', array_filter(array_column($config, 'keywords', $catParam))) as $id => $pgarray)
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
		ksort($categories, SORT_LOCALE_STRING);
		$categories=array_merge(array("Alla" => array_column($config, $catParam)), $categories);
		return $categories;
	}

?>
