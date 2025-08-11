<?php

	// Uses manage functions: isFullTarget, sizePosts, printTextarea, printHiddenInputs, printUpdateButton, printInfoButton, printDeleteButton, targetId

	// Takes a full help target (array), inheritPosts (array), and helps (array).
	// Prints form fields and buttons that are used to view and edit the configuration for the given help.
	function printHelpForm($help, $inheritPosts, $helps=array())
	{
		if (!isFullTarget($help))
		{
			die("printHelpForm($help, $inheritPosts, $helps=array()) failed!");
		}
		$sizePosts=sizePosts($inheritPosts);
		echo '<div><div class="printXFormDiv"><form method="post">';
		printTextarea($help, 'help_id', 'textareaMedium', 'Verktygsfält:', in_array('help_id', $helps), $sizePosts);
		printTextarea($help, 'abstract', 'textareaLarge', 'Hjälptext:', in_array('abstract', $helps), $sizePosts);
		printTextarea($help, 'info', 'textareaLarge', 'Info:', in_array('info', $helps), $sizePosts);
		printHiddenInputs($inheritPosts);
		echo '<div class="buttonDiv">';
		printUpdateButton('help');
		printCopyButton('help');
		$help=makeTargetBasic($help);
		printInfoButton($help);
		$deleteConfirmStr="Är du säker att du vill radera hjälpen ".targetId($help)."?";
		printDeleteButton($help, $deleteConfirmStr, $inheritPosts);
		echo '</div></form></div></div>';
	}

?>
