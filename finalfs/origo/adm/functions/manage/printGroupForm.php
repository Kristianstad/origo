<?php

	require_once("./functions/manage/printTextarea.php");
	require_once("./functions/manage/printUpdateSelect.php");
	require_once("./functions/manage/printHiddenInputs.php");
	require_once("./functions/manage/printUpdateButton.php");
	require_once("./functions/manage/printInfoButton.php");
	require_once("./functions/manage/printDeleteButton.php");
	require_once("./functions/manage/printAddOperation.php");
	require_once("./functions/manage/printRemoveOperation.php");

	function printGroupForm($group, $operationTables, $inheritPosts)
	{
		echo '<div><div class="printXFormDiv"><form method="post">';
		printTextarea($group, 'group_id', 'textareaMedium', 'Id:');
		printTextarea($group, 'layers', 'textareaLarge', 'Lager:');
		printTextarea($group, 'groups', 'textareaLarge', 'Grupper:');
		echo '<br>';
		printTextarea($group, 'title', 'textareaMedium', 'Titel:');
		printUpdateSelect($group, array('expanded'=>array("f", "t")), 'miniSelect', 'Expanderad:');
		printTextarea($group, 'abstract', 'textareaMedium', 'Beskrivning:');
		printTextarea($group, 'info', 'textareaLarge', 'Info:');
		printHiddenInputs($inheritPosts);
		echo '<hr class="dashedHr">';
		echo '<div class="buttonDiv">';
		printUpdateButton('group');
		$group['group']=$group['group']['group_id'];
		printInfoButton($group);
		echo '</div></form></div>';
		$deleteConfirmStr="Är du säker på att du vill radera gruppen ".$group['group']."? Ingående lager påverkas ej och referenser till gruppen hanteras separat.";
		printDeleteButton($group, $deleteConfirmStr, $inheritPosts);
		echo '</div><div class="addRemoveDiv">';
		printAddOperation($group, array('maps'=>array_column($operationTables['maps'], 'map_id')), 'Lägg till i karta', $inheritPosts);
		printRemoveOperation($group, array('maps'=>$operationTables['maps']), 'Ta bort från karta', $inheritPosts);
		printAddOperation($group, array('groups'=>array_column($operationTables['groups'], 'group_id')), 'Lägg till i grupp', $inheritPosts);
		printRemoveOperation($group, array('groups'=>$operationTables['groups']),'Ta bort från grupp', $inheritPosts);
		echo '</div>';
	}
	
?>
