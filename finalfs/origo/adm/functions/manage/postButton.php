<?php

	function postButton($post)
	{
		$postButton=array_keys(array_filter($post, function($key) {return (substr($key, -6) == 'Button');}, ARRAY_FILTER_USE_KEY));
		if (isset($postButton[0]))
		{
			return $postButton[0];
		}
		else
		{
			return null;
		}
	}

?>
