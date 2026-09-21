<?php

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
		$targetType=targetType($target);
		$targetId=targetId($target);
		$parentTable=key($potentialParents);
		$parentIdColumn=pkColumnOfTable($parentTable);
		require("./constants/arrayColumns.php");
		$parents=array();
		foreach (current($potentialParents) as $potentialParent)
		{
			if (in_array($targetType.'s', $arrayColumns))
			{
				if (in_array($targetId, pgArrayToPhp($potentialParent[$targetType.'s'])))
				{
					$parents[]=$potentialParent[$parentIdColumn];
				}
			}
			else
			{
				if ($targetId == $potentialParent[$targetType])
				{
					$parents[]=$potentialParent[$parentIdColumn];
				}
			}
		}
		return $parents;
	}