<?php

	function findParents($potentialParents, $child)
	{
		$parents=array();
		foreach (current($potentialParents) as $potentialParent)
		{
			if ((key($child) == 'control' || key($child) == 'group' || key($child) == 'layer' || key($child) == 'proj4def') && (key($potentialParents) == 'maps' || key($potentialParents) == 'groups'))
			{
				if (in_array(current($child), pgArrayToPhp($potentialParent[key($child).'s'])))
				{
					$parents[]=$potentialParent[pkColumnOfTable(key($potentialParents))];
				}
			}
			elseif (key($child) == 'export' && key($potentialParents) == 'layers')
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
