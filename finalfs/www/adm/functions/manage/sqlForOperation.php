<?php

	require_once("./functions/pgArrayToPhp.php");
	require_once("./functions/pkColumnOfTable.php");

	function sqlForOperation($operation, $child, $parent)
	{
		require("./constants/configSchema.php");
		$sql="";
		$parentColumn=key($child).'s';
		$parentColumnArray=pgArrayToPhp(current($parent)[$parentColumn]);
		$parentTable=key($parent);
		$parentPkColumn=pkColumnOfTable($parentTable);
		$parentId=current($parent)[$parentPkColumn];
		if (!isset($parentColumnArray[0]))
		{
			$parentColumnArray=array();
		}
		if ($operation == 'add' && !in_array(current($child), $parentColumnArray))
		{
			$parentColumnArray[]=current($child);
		}
		elseif ($operation == 'remove' && ($key = array_search(current($child), $parentColumnArray)) !== false)
		{
			unset($parentColumnArray[$key]);
		}
		$parentColumnNewValue='{'.implode(',', $parentColumnArray).'}';
		$sql="UPDATE $configSchema.$parentTable SET $parentColumn = '$parentColumnNewValue' WHERE $parentPkColumn = '$parentId'";
		return $sql;
	}
	
?>
