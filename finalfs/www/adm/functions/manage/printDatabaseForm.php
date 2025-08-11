<?php

	// Uses manage functions: isFullTarget, sizePosts, printTextarea, printHiddenInputs, printUpdateButton, printInfoButton, printReadDbSchemasButton, printDeleteButton, targetId

	// Takes a full database target (array), inheritPosts (array), and helps (array).
	// Prints form fields and buttons that are used to view and edit the configuration for the given database.
	function printDatabaseForm($database, $inheritPosts, $helps=array())
	{
		if (!isFullTarget($database))
		{
			die("printDatabaseForm($database, $inheritPosts, $helps=array()) failed!");
		}
		$sizePosts=sizePosts($inheritPosts);
		echo '<div><div class="printXFormDiv"><form method="post">';
		printTextarea($database, 'database_id', 'textareaMedium', 'Id:', in_array('database_id', $helps), $sizePosts);
		printTextarea($database, 'connectionstring', 'textareaLarge', 'Anslutningssträng:', in_array('connectionstring', $helps), $sizePosts);
		printTextarea($database, 'abstract', 'textareaLarge', 'Beskrivning:', in_array('abstract', $helps), $sizePosts);
		printTextarea($database, 'info', 'textareaLarge', 'Info:', in_array('info', $helps), $sizePosts);
		printHiddenInputs($inheritPosts);
		echo '<div class="buttonDiv">';
		printUpdateButton('database');
		printCopyButton('database');
		$database=makeTargetBasic($database);
		printInfoButton($database);
		printReadDbSchemasButton(targetId($database));
		$deleteConfirmStr="Är du säker att du vill radera all metadata för databasen ".targetId($database)."? Metadata för ingående scheman och tabeller hanteras separat.";
		printDeleteButton($database, $deleteConfirmStr, $inheritPosts);
		echo '</div></form></div></div>';
	}

?>
