<?php

	function updatedTarget($target, $updatePosts)
	{
		require("./constants/arrayColumns.php");
		$columns=current($target);
		foreach ($columns as $column=>$value)
		{
			$newValue=$updatePosts['update'.ucfirst($column)];
			if (!isset($newValue))
			{
				$newValue='';
			}
			if (in_array($column, $arrayColumns))
			{
				$newValue='{'.$newValue.'}';
			}
			$columns[$column]=$newValue;
		}
		return array(key($target)=>$columns);
	}

?>
