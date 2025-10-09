<?php

	// Uses manage functions: isFullTarget, sizePosts, printTextarea, printHiddenInputs, printUpdateButton, printInfoButton, printDeleteButton, printAddOperation, 
	// printRemoveOperation, targetId

	// Takes a full control target (array), maps (array), inheritPosts (array), and helps (array).
	// Prints form fields and buttons that are used to view and edit the configuration for the given control.
	function printControlForm($control, $maps, $inheritPosts, $helps=array())
	{
		if (!isFullTarget($control))
		{
			die("printControlForm($control, $maps, $inheritPosts, $helps=array()) failed!");
		}
		$sizePosts=sizePosts($inheritPosts);
		echo '<div><div class="printXFormDiv"><form method="post">';
		printTextarea($control, 'control_id', 'textareaMedium', 'Id:', in_array('control_id', $helps), $sizePosts);
		printTextarea($control, 'abstract', 'textareaLarge', 'Beskrivning:', in_array('abstract', $helps), $sizePosts);
		printTextarea($control, 'options', 'textareaLarge', 'Inställningar:', in_array('options', $helps), $sizePosts);
		printTextarea($control, 'css', 'textareaLarge', 'CSS:', in_array('css', $helps), $sizePosts);
		printTextarea($control, 'js', 'textareaLarge', 'JS:', in_array('js', $helps), $sizePosts);
		printTextarea($control, 'info', 'textareaLarge', 'Info:', in_array('info', $helps), $sizePosts);
		printHiddenInputs($inheritPosts);
		echo '<hr class="dashedHr">';
		echo '<div class="buttonDiv">';
		printUpdateButton('control');
		printCopyButton('control');
		$control=makeTargetBasic($control);
		printInfoButton($control);
		$deleteConfirmStr="Är du säker att du vill radera kontrollen ".targetId($control)."? Referenser till kontrollen hanteras separat.";
		printDeleteButton($control, $deleteConfirmStr, $inheritPosts);
		echo '</div></form></div></div><div class="addRemoveDiv">';
		printAddOperation($control, array('maps'=>array_column($maps['maps'], 'map_id')), 'Lägg till i karta', $inheritPosts);
		printRemoveOperation($control, $maps, 'Ta bort från karta', $inheritPosts);
		echo '</div>';
	}

?>
