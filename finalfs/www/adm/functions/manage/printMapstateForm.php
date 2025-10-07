<?php

	// Uses manage functions: isFullTarget, sizePosts, printTextarea, printHiddenInputs, printUpdateButton, printInfoButton, printDeleteButton, targetId

	// Takes a full mapstate target (array), inheritPosts (array), and helps (array).
	// Prints form fields and buttons that are used to view and edit the configuration for the given mapstate.
	function printMapstateForm($mapstate, $inheritPosts, $helps=array())
	{
		if (!isFullTarget($mapstate))
		{
			die("printMapstateForm($mapstate, $inheritPosts, $helps=array()) failed!");
		}
		$sizePosts=sizePosts($inheritPosts);
		echo '<div><div class="printXFormDiv"><form method="post">';
		printTextarea($mapstate, 'mapstate_id', 'textareaMedium', 'Id:', in_array('mapstate_id', $helps), $sizePosts, true);
		printTextarea($mapstate, 'lastuse', 'textareaMedium', 'Senast använd:', in_array('lastuse', $helps), $sizePosts, true);
		printTextarea($mapstate, 'abstract', 'textareaLarge', 'Beskrivning:', in_array('abstract', $helps), $sizePosts);
		printTextarea($mapstate, 'info', 'textareaLarge', 'Info:', in_array('info', $helps), $sizePosts);
		printHiddenInputs($inheritPosts);
		echo '<div class="buttonDiv">';
		printUpdateButton('mapstate');
		printCopyButton('mapstate');
		$mapstate=makeTargetBasic($mapstate);
		printInfoButton($mapstate);
		$deleteConfirmStr="Är du säker att du vill radera mapstatet ".targetId($mapstate)."? Referenser till mapstatet hanteras separat.";
		printDeleteButton($mapstate, $deleteConfirmStr, $inheritPosts);
		echo '</div></form></div></div>';
	}

?>
