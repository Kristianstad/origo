<?php

	require_once("./functions/manage/isTarget.php");
	require_once("./functions/manage/makeTargetBasic.php");
	require_once("./functions/pgArrayToPhp.php");
	require_once("./functions/pkColumnOfTable.php");

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
