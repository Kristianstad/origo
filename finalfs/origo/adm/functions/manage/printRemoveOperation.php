<?php

	require_once("./functions/findParents.php");
	require_once("./functions/manage/printSelectOptions.php");
	require_once("./functions/manage/printHiddenInputs.php");

	function printRemoveOperation($targetToRemove, $tableToRemoveFrom, $buttontext, $inheritPosts)
	{
		$parents=findParents($tableToRemoveFrom, $targetToRemove);
		if (!empty($parents))
		{
			echo '<form class="addForm" method="post">';
			echo '<select class="addSelect" name="from'.ucfirst(rtrim(key($tableToRemoveFrom), 's')).'Id">';
			printSelectOptions(array_merge(array(""), $parents));
			echo '</select>&nbsp;';
			printHiddenInputs($inheritPosts);
			echo '<button type="submit" name="'.key($targetToRemove).'Button" value="operation">'.$buttontext.'</button>';
			echo '</form>';
		}
	}
	
?>
