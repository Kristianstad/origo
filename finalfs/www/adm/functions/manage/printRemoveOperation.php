<?php

	require_once("./functions/findParents.php");
	require_once("./functions/manage/printSelectOptions.php");
	require_once("./functions/manage/printHiddenInputs.php");
	require_once("./functions/manage/targetType.php");
	require_once("./functions/toSwedish.php");

	function printRemoveOperation($targetToRemove, $tableToRemoveFrom, $buttontext, $inheritPosts)
	{
		$tableToRemoveFromType=rtrim(key($tableToRemoveFrom), 's');
		$str=ucfirst($tableToRemoveFromType);
		$parents=findParents($tableToRemoveFrom, $targetToRemove);
		if (!empty($parents))
		{
			echo '<form class="addForm" method="post">';
			echo '<select class="addSelect" name="from'.$str.'Id">';
			printSelectOptions(array_merge(array(""), $parents));
			echo '</select>&nbsp;';
			printHiddenInputs($inheritPosts);
			$targetToRemoveTypeSwe=toSwedish(targetType($targetToRemove));
			$tableToRemoveFromTypeSwe=toSwedish($tableToRemoveFromType);
			echo '<button title="Ta bort '.$targetToRemoveTypeSwe.' från '.$tableToRemoveFromTypeSwe.'" type="submit" name="'.key($targetToRemove).'Button" value="operation">'.$buttontext.'</button>';
			echo '</form>';
		}
	}

?>
