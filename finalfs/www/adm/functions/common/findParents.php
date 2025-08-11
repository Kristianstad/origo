<?php

	// Uses common functions: isTarget, makeTargetBasic, pgArrayToPhp, pkColumnOfTable

	// Takes an array of potentian parents and a target, and returns the actual parents.
	function findParents($potentialParents, $target)
	{
		if (isTarget($target))
		{
			$target=makeTargetBasic($target);
		}
		else
		{
			die("findParents(\$potentialParents, $target) failed! Child not a target.");
		}
		
		require("./constants/arrayColumns.php");
		$parents=array();
		foreach (current($potentialParents) as $potentialParent)
		{
			if (in_array(key($target).'s', $arrayColumns))
			{
				if (in_array(current($target), pgArrayToPhp($potentialParent[key($target).'s'])))
				{
					$parents[]=$potentialParent[pkColumnOfTable(key($potentialParents))];
				}
			}
			else
			{
				if (current($target) == $potentialParent[key($target)])
				{
					$parents[]=$potentialParent[pkColumnOfTable(key($potentialParents))];
				}
			}
		}
		return $parents;
	}

?>
