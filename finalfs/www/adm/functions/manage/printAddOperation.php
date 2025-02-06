<?php

	require_once("./functions/manage/printSelectOptions.php");
	require_once("./functions/manage/printHiddenInputs.php");
	require_once("./functions/manage/targetType.php");
	require_once("./functions/toSwedish.php");

	function printAddOperation($target, $addToTable, $buttontext, $inheritPosts)
	{
		$addToTableType=rtrim(key($addToTable), 's');
		$str=ucfirst($addToTableType);
		echo '<form class="addForm" method="post">';
		echo '<select class="addSelect" name="to'.$str.'Id">';
		printSelectOptions(array_merge(array(""),current($addToTable)));
		echo '</select>&nbsp;';
		printHiddenInputs($inheritPosts);
		$targetTypeSwe=toSwedish(targetType($target));
		$addToTableTypeSwe=toSwedish($addToTableType);
		echo '<button title="Lägg till '.$targetTypeSwe.' i '.$addToTableTypeSwe.'" type="submit" name="'.key($target).'Button" value="operation">'.$buttontext.'</button>';
		echo '</form>';
	}

?>
