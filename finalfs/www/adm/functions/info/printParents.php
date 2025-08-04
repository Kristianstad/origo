<?php

	require_once("./functions/assoc_array_values.php");
	require_once("./functions/toSwedish.php");

	function printParents($allParents)
	{
		if (empty(assoc_array_values($allParents)))
		{
			return false;
		}
		else
		{
			foreach ($allParents as $parentTable=>$parentTableOptions)
			{
				if (!empty($parentTableOptions))
				{
					foreach ($parentTableOptions as $parentOption=>$parents)
					{
						if (!empty($parents))
						{
							$parentTableSv=toSwedish($parentTable);
							$parentOptionSv=toSwedish($parentOption);
							$parentType=rtrim($parentTable, 's');
							$first=true;
							$headerString="$parentTableSv ($parentOptionSv): ";
							echo "<b>$headerString</b>";
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
					}
				}
			}
		}
	}

?>
