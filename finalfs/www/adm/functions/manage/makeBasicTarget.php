<?php

	// Takes a type string and an id string and combine them into a basic target array.
	// The basic target array has the type as only key and the id as value
	function makeBasicTarget($type, $id)
	{
		if (is_string($type) && !empty($type) && is_string($id) && !empty($id))
		{
			return array($type=>$id);
		}
		else
		{
			die("makeBasicTarget($type, $id) failed!");
		}
	}

?>
