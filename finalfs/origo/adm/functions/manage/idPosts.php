<?php

	function idPosts($post)
	{
		$idPosts=array_filter($post, function($key) {return (substr($key, -2) == 'Id');}, ARRAY_FILTER_USE_KEY);
		unset($idPosts['fromMapId'], $idPosts['toMapId'], $idPosts['fromGroupId'], $idPosts['toGroupId']);
		return $idPosts;
	}

?>
