<?php

	function printAddOperation($target, $addToTable, $buttontext, $inheritPosts)
	{
		$addToTableType=rtrim(key($addToTable), 's');
		$str=ucfirst($addToTableType);
		echo '<form class="addForm" method="post">';
		echo '<select class="addSelect" name="to'.$str.'Id">';
		printSelectOptions(array_merge(array(""),current($addToTable)));
		echo '</select>&nbsp;';
		printHiddenInputs($inheritPosts);
		$targetType=targetType($target);
		$targetTypeSwe=toSwedish($targetType);
		$addToTableTypeSwe=toSwedish($addToTableType);
		echo '<button title="Lägg till '.$targetTypeSwe.' i '.$addToTableTypeSwe.'" type="submit" name="'.$targetType.'Button" value="operation">'.$buttontext.'</button>';
		echo '</form>';
	}