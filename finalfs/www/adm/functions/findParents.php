<?php

	require_once("./functions/pgArrayToPhp.php");
	require_once("./functions/pkColumnOfTable.php");

	function findParents($potentialParents, $child)
	{
		require("./constants/arrayColumns.php");
		$parents=array();
		foreach (current($potentialParents) as $potentialParent)
		{
			if (in_array(key($child).'s', $arrayColumns))
			{
				if (in_array(current($child), pgArrayToPhp($potentialParent[key($child).'s'])))
				{
					$parents[]=$potentialParent[pkColumnOfTable(key($potentialParents))];
				}
			}
			else
			{
				if (current($child) == $potentialParent[key($child)])
				{
					$parents[]=$potentialParent[pkColumnOfTable(key($potentialParents))];
				}
			}
		}
		return $parents;
	}
	
?>
