<?php

	require_once("./functions/manage/isFullTarget.php");
	require_once("./functions/manage/printTextarea.php");
	require_once("./functions/manage/printHiddenInputs.php");
	require_once("./functions/manage/printUpdateButton.php");
	require_once("./functions/manage/printInfoButton.php");
	require_once("./functions/manage/printReadDbSchemasButton.php");
	require_once("./functions/manage/printDeleteButton.php");
	require_once("./functions/manage/targetId.php");

	// Takes a full database target (array), inheritPosts (array), and helps (array).
	// Prints form fields and buttons that are used to view and edit the configuration for the given database.
	function printDatabaseForm($database, $inheritPosts, $helps=array())
	{
		if (!isFullTarget($database))
		{
			die("printDatabaseForm($database, $inheritPosts, $helps=array()) failed!");
		}
		echo '<div><div class="printXFormDiv"><form method="post">';
		printTextarea($database, 'database_id', 'textareaMedium', 'Id:', in_array('database_id', $helps));
		printTextarea($database, 'connectionstring', 'textareaLarge', 'Anslutningssträng:', in_array('connectionstring', $helps));
		printTextarea($database, 'abstract', 'textareaLarge', 'Beskrivning:', in_array('abstract', $helps));
		printTextarea($database, 'info', 'textareaLarge', 'Info:', in_array('info', $helps));
		printHiddenInputs($inheritPosts);
		echo '<div class="buttonDiv">';
		printUpdateButton('database');
		$database=makeTargetBasic($database);
		printInfoButton($database);
		printReadDbSchemasButton(targetId($database));
		$deleteConfirmStr="Är du säker att du vill radera all metadata för databasen ".targetId($database)."? Metadata för ingående scheman och tabeller hanteras separat.";
		printDeleteButton($database, $deleteConfirmStr, $inheritPosts);
		echo '</div></form></div></div>';
	}

?>
