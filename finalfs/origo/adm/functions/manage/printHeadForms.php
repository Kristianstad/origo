<?php

	require_once("./functions/toSwedish.php");
	require_once("./functions/manage/headForm.php");
	require_once("./functions/manage/printMultiselectButton.php");

	function printHeadForms($view, $configTables, $focusTable, $inheritPosts)
	{
		require("./constants/views.php");
		require("./constants/multiselectables.php");
		if (empty($view))
		{
			$view=key($views);
		}
		if (empty($views[$view]))
		{
			$forms=$configTables;
		}
		else
		{
			$forms=array_flip($views[$view]);
			foreach ($forms as $k => $v)
			{
				$forms[$k]=$configTables[$k];
			}
		}
		if (isset($focusTable))
		{
			unset($forms[$focusTable]);
			$forms=array_merge(array($focusTable=>$configTables[$focusTable]), $forms);
		}
		unset($configTables);
		echo <<<HERE
			<div style="width:calc( 100vw - 2rem ); overflow-x:auto; margin-bottom: 5px">
				<table style="border-bottom:dashed 1px lightgray; margin-bottom: 2px; border-top:dashed 1px lightgray;">
					<tr style="height:7rem">
		HERE;
		$i=1;
		$l=count($forms);
		foreach ($forms as $tableName=>$table)
		{
			$tmpInheritPosts=$inheritPosts;
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
				unset($tmpInheritPosts[rtrim($tableName, 's').'Id']);
			}
			echo "<h3 class='$focusClass'>".ucfirst(toSwedish($tableName))."</h3><div style='width:16em;white-space:nowrap;display:inline-block'>";
			headForm(array($tableName=>$table), $tmpInheritPosts);
			echo "</div>";
			if (in_array($tableName, $multiselectables))
			{
				printMultiselectButton($tableName);
			}
			echo '</th>';
		}
		echo <<<HERE
					</tr>
				</table>
			</div>
		HERE;
	}
	
?>
