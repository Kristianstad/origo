<?php

	function updatePosts($post)
	{
		return array_filter($post, function($key) {return (substr($key, 0, 6) == 'update');}, ARRAY_FILTER_USE_KEY);
	}

?>
