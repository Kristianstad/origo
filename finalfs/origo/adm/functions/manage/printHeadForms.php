<?php

	function printHeadForms($configTables, $focusTable, $inheritPosts)
	{
		$multiselectables=array('controls', 'groups', 'layers', 'proj4defs', 'exports');
		$i=1;
		$l=count($configTables);
		foreach ($configTables as $tableName=>$table)
		{
			if ($i == $l)
			{
				echo '<th class="thRight">';
			}
			else
			{
				if ($i == 1)
				{
					echo '<th class="thLeft">';
				}
				else
				{
					echo '<th class="thMiddle">';
				}
				$i++;
			}
			if ($focusTable == $tableName)
			{
				$focusClass='h3Focus';
			}
			else
			{
				$focusClass='h3NoFocus';
			}
			echo "<h3 class='$focusClass'>Redigera ".toSwedish($tableName)."</h3>";
			headForm(array($tableName=>$table), $inheritPosts);
			if (in_array($tableName, $multiselectables))
			{
				printMultiselectButton($tableName);
			}
			echo '</th>';
		}
	}
	
?>
