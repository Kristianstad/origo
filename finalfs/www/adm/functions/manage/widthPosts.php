<?php

	// Takes an associative array and returns all values (and keys) where the key starts with 'width' or 'newwidth'.
	function widthPosts($post)
	{
		$widthPosts=array_filter($post, function($key) {return (substr($key, 0, 5) == 'width');}, ARRAY_FILTER_USE_KEY);
		$newwidthPosts=array_filter($post, function($key) {return (substr($key, 0, 8) == 'newwidth');}, ARRAY_FILTER_USE_KEY);
		foreach ($newwidthPosts as $key=>$value)
		{
			$widthPosts[substr($key, 3)]=$value;
		}
		return $widthPosts;
	}

?>
