<?php

	require_once("./functions/manage/printTextarea.php");
	require_once("./functions/manage/printHiddenInputs.php");
	require_once("./functions/manage/printUpdateButton.php");
	require_once("./functions/manage/printInfoButton.php");
	require_once("./functions/manage/printReadDbSchemasButton.php");
	require_once("./functions/manage/printDeleteButton.php");

	function printDatabaseForm($database, $inheritPosts)
	{
		echo '<div><div style="float:left;"><form method="post" style="line-height:2">';
		printTextarea($database, 'database_id', 'textareaMedium', 'Id:');
		printTextarea($database, 'connectionstring', 'textareaLarge', 'Anslutningssträng:');
		echo '<br>';
		printTextarea($database, 'info', 'textareaLarge', 'Info:');
		printHiddenInputs($inheritPosts);
		echo '<div class="buttonDiv">';
		printUpdateButton('database');
		$database['database']=$database['database']['database_id'];
		printInfoButton($database);
		printReadDbSchemasButton($database['database']);
		echo '</div></form></div>';
		$deleteConfirmStr="Är du säker att du vill radera all metadata för databasen ".$database['database']."? Metadata för ingående scheman och tabeller hanteras separat.";
		printDeleteButton($database, $deleteConfirmStr, $inheritPosts);
		echo '</div>';
	}
	
?>
