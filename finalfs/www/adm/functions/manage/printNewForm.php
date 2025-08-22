<?php

	// Uses common functions: makeTargetBasic

	// Uses manage functions: isFullTarget, sizePosts, printTextarea, printHiddenInputs, printUpdateButton, printCopyButton, printInfoButton, printDeleteButton, targetId

	// Takes a full new target (array), inheritPosts (array), and helps (array).
	// Prints form fields and buttons that are used to view and edit the configuration for the given new.
	function printNewForm($new, $inheritPosts, $helps=array())
	{
		if (!isFullTarget($new))
		{
			die("printNewForm($new, $inheritPosts, $helps=array()) failed!");
		}
		$sizePosts=sizePosts($inheritPosts);
		echo '<div><div class="printXFormDiv"><form method="post">';
		printTextarea($new, 'new_id', 'textareaMedium', 'Id:', in_array('new_id', $helps), $sizePosts);
		printTextarea($new, 'abstract', 'textareaLarge', 'Beskrivning:', in_array('abstract', $helps), $sizePosts);
		printTextarea($new, 'text', 'textareaLarge', 'Text:', in_array('text', $helps), $sizePosts);
		printTextarea($new, 'date', 'textareaLarge', 'Skapad:', in_array('date', $helps), $sizePosts);
		printTextarea($new, 'reads', 'textareaLarge', 'Läst av:', in_array('reads', $helps), $sizePosts);
		printTextarea($new, 'deletes', 'textareaLarge', 'Raderad av:', in_array('deletes', $helps), $sizePosts);
		printTextarea($new, 'info', 'textareaLarge', 'Info:', in_array('info', $helps), $sizePosts);
		printHiddenInputs($inheritPosts);
		echo '<div class="buttonDiv">';
		printUpdateButton('new');
		printCopyButton('new');
		$new=makeTargetBasic($new);
		printInfoButton($new);
		$deleteConfirmStr="Är du säker att du vill radera nyheten ".targetId($new)."?";
		printDeleteButton($new, $deleteConfirmStr, $inheritPosts);
		echo '</div></form></div></div>';
	}

?>
