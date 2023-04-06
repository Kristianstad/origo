<?php

	require_once("./functions/findParents.php");
	require_once("./functions/toSwedish.php");

	function printParents($potentialParents, $child)
	{
		$parents=findParents($potentialParents, $child);
		$parentsTable=key($potentialParents);
		$parentsTableSv=toSwedish($parentsTable);
		if (!empty($parents))
		{
			echo "<b>$parentsTableSv: </b>";
			$first=true;
			$parentType=rtrim($parentsTable, 's');
			foreach ($parents as $parent)
			{
				if (!$first)
				{
					echo ', ';
				}
				else
				{
					$first=false;
				}
				echo '<a href="info.php?type='.$parentType.'&id='.urlencode($parent).'">'.$parent.'</a>';
			}
			echo "</br>";
		}
		else
		{
			return false;
		}
	}

?>
