<?php

	// Takes an associative array and returns all values (and keys) where the key starts with 'height' or 'newheight'.
	function heightPosts($post)
	{
		$heightPosts=array_filter($post, function($key) {return (substr($key, 0, 6) == 'height');}, ARRAY_FILTER_USE_KEY);
		$newheightPosts=array_filter($post, function($key) {return (substr($key, 0, 9) == 'newheight');}, ARRAY_FILTER_USE_KEY);
		foreach ($newheightPosts as $key=>$value)
		{
			$heightPosts[substr($key, 3)]=$value;
		}
		return $heightPosts;
	}

?>
