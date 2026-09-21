<?php

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
			$targetToRemoveType=targetType($targetToRemove);
			$targetToRemoveTypeSwe=toSwedish($targetToRemoveType);
			$tableToRemoveFromTypeSwe=toSwedish($tableToRemoveFromType);
			echo '<button title="Ta bort '.$targetToRemoveTypeSwe.' från '.$tableToRemoveFromTypeSwe.'" type="submit" name="'.$targetToRemoveType.'Button" value="operation">'.$buttontext.'</button>';
			echo '</form>';
		}
	}