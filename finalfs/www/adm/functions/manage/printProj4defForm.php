<?php

	require_once("./functions/manage/isFullTarget.php");
	require_once("./functions/manage/printTextarea.php");
	require_once("./functions/manage/printHiddenInputs.php");
	require_once("./functions/manage/printUpdateButton.php");
	require_once("./functions/manage/printInfoButton.php");
	require_once("./functions/manage/printDeleteButton.php");

	// Takes a full proj4def target (array), inheritPosts (array), and helps (array).
	// Prints form fields and buttons that are used to view and edit the configuration for the given proj4def.
	function printProj4defForm($proj4def, $inheritPosts, $helps=array())
	{
		if (!isFullTarget($proj4def))
		{
			die("printProj4defForm($proj4def, $inheritPosts, $helps=array()) failed!");
		}
		echo '<div><div class="printXFormDiv"><form method="post">';
		printTextarea($proj4def, 'code', 'textareaMedium', 'Kod:', in_array('code', $helps));
		printTextarea($proj4def, 'projection', 'textareaLarge', 'Projektion:', in_array('projection', $helps));
		printTextarea($proj4def, 'projectionextent', 'textareaMedium', 'Projektionsutbredning:', in_array('projectionextent', $helps));
		printTextarea($proj4def, 'alias', 'textareaMedium', 'Alias:', in_array('alias', $helps));
		printTextarea($proj4def, 'abstract', 'textareaLarge', 'Beskrivning:', in_array('abstract', $helps));
		printTextarea($proj4def, 'info', 'textareaLarge', 'Info:', in_array('info', $helps));
		printHiddenInputs($inheritPosts);
		echo '<div class="buttonDiv">';
		printUpdateButton('proj4def');
		$proj4def=makeTargetBasic($proj4def);
		printInfoButton($proj4def);
		$deleteConfirmStr="Är du säker att du vill radera proj4def ".$proj4def['proj4def']."? Referenser till aktuell proj4def hanteras separat.";
		printDeleteButton($proj4def, $deleteConfirmStr, $inheritPosts);
		echo '</div></form></div></div>';
	}

?>
