<?php

	require_once("./functions/manage/isFullTarget.php");
	require_once("./functions/manage/printTextarea.php");
	require_once("./functions/manage/printHiddenInputs.php");
	require_once("./functions/manage/printUpdateButton.php");
	require_once("./functions/manage/printInfoButton.php");
	require_once("./functions/manage/printDeleteButton.php");
	require_once("./functions/manage/targetId.php");

	// Takes a full format target (array), inheritPosts (array), and helps (array).
	// Prints form fields and buttons that are used to view and edit the configuration for the given format.
	function printFormatForm($format, $inheritPosts, $helps=array())
	{
		if (!isFullTarget($format))
		{
			die("printFormatForm($format, $inheritPosts, $helps=array()) failed!");
		}
		echo '<div><div class="printXFormDiv"><form method="post">';
		printTextarea($format, 'format_id', 'textareaMedium', 'Format:', in_array('format_id', $helps));
		printTextarea($format, 'abstract', 'textareaLarge', 'Beskrivning:', in_array('abstract', $helps));
		printTextarea($format, 'info', 'textareaLarge', 'Info:', in_array('info', $helps));
		printHiddenInputs($inheritPosts);
		echo '<div class="buttonDiv">';
		printUpdateButton('format');
		printCopyButton('format');
		$format=makeTargetBasic($format);
		printInfoButton($format);
		$deleteConfirmStr="Är du säker att du vill radera formatet ".targetId($format)."? Referenser till formatet hanteras separat.";
		printDeleteButton($format, $deleteConfirmStr, $inheritPosts);
		echo '</div></form></div></div>';
	}

?>
