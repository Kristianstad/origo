<?php

	// Takes an associative array and returns all values (and keys) where the key ends with Id, with the exception of following keys: fromMapId, toMapId, fromGroupId, toGroupId
	function idPosts($post)
	{
		$idPosts=array_filter($post, function($key) {return (substr($key, -2) == 'Id');}, ARRAY_FILTER_USE_KEY);
		unset($idPosts['fromMapId'], $idPosts['toMapId'], $idPosts['fromGroupId'], $idPosts['toGroupId']);
		return $idPosts;
	}

?>
