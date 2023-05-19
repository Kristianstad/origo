<?php

	require_once("./functions/manage/printTextarea.php");
	require_once("./functions/manage/printHiddenInputs.php");
	require_once("./functions/manage/printUpdateButton.php");
	require_once("./functions/manage/printInfoButton.php");
	require_once("./functions/manage/printDeleteButton.php");
	require_once("./functions/manage/printAddOperation.php");
	require_once("./functions/manage/printRemoveOperation.php");

	function printControlForm($control, $maps, $inheritPosts)
	{
		echo '<div><div class="printXFormDiv"><form method="post">';
		printTextarea($control, 'control_id', 'textareaMedium', 'Id:');
		printTextarea($control, 'abstract', 'textareaLarge', 'Beskrivning:');
		echo '<br>';
		printTextarea($control, 'options', 'textareaLarge', 'Inställningar:');
		printTextarea($control, 'info', 'textareaLarge', 'Info:');
		printHiddenInputs($inheritPosts);
		echo '<hr class="dashedHr">';
		echo '<div class="buttonDiv">';
		printUpdateButton('control');
		$control['control']=$control['control']['control_id'];
		printInfoButton($control);
		echo '</div></form></div>';
		$deleteConfirmStr="Är du säker att du vill radera kontrollen ".$control['control']."? Referenser till kontrollen hanteras separat.";
		printDeleteButton($control, $deleteConfirmStr, $inheritPosts);
		echo '</div><div class="addRemoveDiv">';
		printAddOperation($control, array('maps'=>array_column($maps['maps'], 'map_id')), 'Lägg till i karta', $inheritPosts);
		printRemoveOperation($control, $maps, 'Ta bort från karta', $inheritPosts);
		echo '</div>';
	}
	
?>
