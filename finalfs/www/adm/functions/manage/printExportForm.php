<?php

	require_once("./functions/manage/isFullTarget.php");
	require_once("./functions/manage/printTextarea.php");
	require_once("./functions/manage/printHiddenInputs.php");
	require_once("./functions/manage/printUpdateButton.php");
	require_once("./functions/manage/printInfoButton.php");
	require_once("./functions/manage/printDeleteButton.php");
	require_once("./functions/manage/targetId.php");

	// Takes a full export target (array), inheritPosts (array), and helps (array).
	// Prints form fields and buttons that are used to view and edit the configuration for the given export.
	function printExportForm($export, $inheritPosts, $helps=array())
	{
		if (!isFullTarget($export))
		{
			die("printExportForm($export, $inheritPosts, $helps=array()) failed!");
		}
		echo '<div><div class="printXFormDiv"><form method="post">';
		printTextarea($export, 'export_id', 'textareaMedium', 'Id:', in_array('export_id', $helps));
		printTextarea($export, 'resource', 'textareaMedium', 'Resurs:', in_array('resource', $helps));
		printTextarea($export, 'style', 'textareaMedium', 'Stil:', in_array('style', $helps));
		printTextarea($export, 'abstract', 'textareaLarge', 'Beskrivning:', in_array('abstract', $helps));
		printTextarea($export, 'info', 'textareaLarge', 'Info:', in_array('info', $helps));
		printHiddenInputs($inheritPosts);
		echo '<div class="buttonDiv">';
		printUpdateButton('export');
		$export=makeTargetBasic($export);
		printInfoButton($export);
		$deleteConfirmStr="Är du säker att du vill radera exportinställningen ".targetId($export)."? Referenser till exportinställningen hanteras separat.";
		printDeleteButton($export, $deleteConfirmStr, $inheritPosts);
		echo '</div></form></div></div>';
	}

?>
