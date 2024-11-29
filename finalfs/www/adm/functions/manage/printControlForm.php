<?php

	require_once("./functions/manage/isFullTarget.php");
	require_once("./functions/manage/printTextarea.php");
	require_once("./functions/manage/printHiddenInputs.php");
	require_once("./functions/manage/printUpdateButton.php");
	require_once("./functions/manage/printInfoButton.php");
	require_once("./functions/manage/printDeleteButton.php");
	require_once("./functions/manage/printAddOperation.php");
	require_once("./functions/manage/printRemoveOperation.php");

	// Takes a full control target (array), maps (array), inheritPosts (array), and helps (array).
	// Prints form fields and buttons that are used to view and edit the configuration for the given control.
	function printControlForm($control, $maps, $inheritPosts, $helps=array())
	{
		if (!isFullTarget($control))
		{
			die("printControlForm($control, $maps, $inheritPosts, $helps=array()) failed!");
		}
		echo '<div><div class="printXFormDiv"><form method="post">';
		printTextarea($control, 'control_id', 'textareaMedium', 'Id:', in_array('control_id', $helps));
		printTextarea($control, 'abstract', 'textareaLarge', 'Beskrivning:', in_array('abstract', $helps));
		printTextarea($control, 'options', 'textareaLarge', 'Inställningar:', in_array('options', $helps));
		printTextarea($control, 'info', 'textareaLarge', 'Info:', in_array('info', $helps));
		printHiddenInputs($inheritPosts);
		echo '<hr class="dashedHr">';
		echo '<div class="buttonDiv">';
		printUpdateButton('control');
		$control['control']=$control['control']['control_id'];
		printInfoButton($control);
		$deleteConfirmStr="Är du säker att du vill radera kontrollen ".$control['control']."? Referenser till kontrollen hanteras separat.";
		printDeleteButton($control, $deleteConfirmStr, $inheritPosts);
		echo '</div></form></div></div><div class="addRemoveDiv">';
		printAddOperation($control, array('maps'=>array_column($maps['maps'], 'map_id')), 'Lägg till i karta', $inheritPosts);
		printRemoveOperation($control, $maps, 'Ta bort från karta', $inheritPosts);
		echo '</div>';
	}

?>
