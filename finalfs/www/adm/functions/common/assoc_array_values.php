<?php

	// Takes an assocciative array and returns a numeric array containing all the values.
	function assoc_array_values($assocArray)
	{
		array_walk_recursive($assocArray, function($value, $key) use (&$values) {
			$values[]=$value;
		}, $values=array());
		return $values;
	}

?>
