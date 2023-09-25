<?php

	require_once("./functions/manage/isFullTarget.php");
	require_once("./functions/manage/printTextarea.php");
	require_once("./functions/manage/printUpdateSelect.php");
	require_once("./functions/manage/printHiddenInputs.php");
	require_once("./functions/manage/printUpdateButton.php");
	require_once("./functions/manage/printInfoButton.php");
	require_once("./functions/manage/printDeleteButton.php");
	require_once("./functions/manage/printAddOperation.php");
	require_once("./functions/manage/printRemoveOperation.php");

	function printGroupForm($group, $operationTables, $inheritPosts, $helps=array())
	{
		if (!isFullTarget($group))
		{
			die("printGroupForm($group, $operationTables, $inheritPosts, $helps=array()) failed!");
		}
		echo '<div><div class="printXFormDiv"><form method="post">';
		printTextarea($group, 'group_id', 'textareaMedium', 'Id:', in_array('group_id', $helps));
		printTextarea($group, 'layers', 'textareaLarge', 'Lager:', in_array('layers', $helps));
		printTextarea($group, 'groups', 'textareaLarge', 'Grupper:', in_array('groups', $helps));
		printTextarea($group, 'title', 'textareaMedium', 'Titel:', in_array('title', $helps));
		printUpdateSelect($group, array('expanded'=>array("f", "t")), 'miniSelect', 'Expanderad:', in_array('expanded', $helps));
		printUpdateSelect($group, array('show_meta'=>array("f", "t")), 'miniSelect', 'Visa metadata:', in_array('show_meta', $helps));
		printTextarea($group, 'abstract', 'textareaMedium', 'Beskrivning:', in_array('abstract', $helps));
		printTextarea($group, 'keywords', 'textareaLarge', 'Nyckelord:', in_array('keywords', $helps));
		printTextarea($group, 'info', 'textareaLarge', 'Info:', in_array('info', $helps));
		printHiddenInputs($inheritPosts);
		echo '<hr class="dashedHr">';
		echo '<div class="buttonDiv">';
		printUpdateButton('group');
		$group['group']=$group['group']['group_id'];
		printInfoButton($group);
		$deleteConfirmStr="Är du säker på att du vill radera gruppen ".$group['group']."? Ingående lager påverkas ej och referenser till gruppen hanteras separat.";
		printDeleteButton($group, $deleteConfirmStr, $inheritPosts);
		echo '</div></form></div></div><div class="addRemoveDiv">';
		printAddOperation($group, array('maps'=>array_column($operationTables['maps'], 'map_id')), 'Lägg till i karta', $inheritPosts);
		printRemoveOperation($group, array('maps'=>$operationTables['maps']), 'Ta bort från karta', $inheritPosts);
		printAddOperation($group, array('groups'=>array_column($operationTables['groups'], 'group_id')), 'Lägg till i grupp', $inheritPosts);
		printRemoveOperation($group, array('groups'=>$operationTables['groups']),'Ta bort från grupp', $inheritPosts);
		echo '</div>';
	}

?>
