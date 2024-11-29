<?php

	require_once("./functions/manage/isFullTarget.php");
	require_once("./functions/manage/printTextarea.php");
	require_once("./functions/manage/printHiddenInputs.php");
	require_once("./functions/manage/printUpdateButton.php");
	require_once("./functions/manage/printInfoButton.php");
	require_once("./functions/manage/printDeleteButton.php");

	// Takes a full table target (array), pg_connect connection string, table selectables (array), inheritPosts (array), and helps (array).
	// Prints form fields and buttons that are used to view and edit the configuration for the given table.
	function printTableForm($table, $dbhConnectionString, $selectables, $inheritPosts, $helps=array())
	{
		if (!isFullTarget($table))
		{
			die("printTableForm($table, $selectables, $inheritPosts, $helps=array()) failed!");
		}
		echo '<div><div class="printXFormDiv"><form method="post">';
		printTextarea($table, 'table_id', 'textareaMedium', 'Id:', in_array('table_id', $helps));
		printTextarea($table, 'abstract', 'textareaLarge', 'Beskrivning:', in_array('abstract', $helps));
		printTextarea($table, 'keywords', 'textareaLarge', 'Nyckelord:', in_array('keywords', $helps));
		printUpdateSelect($table, array('contact'=>$selectables['contacts']), 'bodySelect', 'Kontakt:', in_array('contact', $helps));
		printUpdateSelect($table, array('origin'=>$selectables['origins']), 'bodySelect', 'Ursprungskälla:', in_array('origin', $helps));
		//printTextarea($table, 'updated', 'textareaMedium', 'Uppdaterad (åååå-mm-dd):', in_array('updated', $helps));
		$dbh2=dbh($dbhConnectionString);
		$tableWithSchema=substr($table['table']['table_id'], strpos($table['table']['table_id'], '.')+1);
		$updated=substr(updated_from_table($dbh2, $tableWithSchema)[0], 0, 10);
		echo '<span style="vertical-align: text-bottom; margin-left:0.6em">Updaterad: </span><textarea readonly rows="1" class="textareaMedium">'.$updated.'</textarea>';
		printUpdateSelect($table, array('update'=>$selectables['updates']), 'bodySelect', 'Uppdatering:', in_array('update', $helps));
		printTextarea($table, 'history', 'textareaLarge', 'Tillkomsthistorik:', in_array('history', $helps));
		printTextarea($table, 'info', 'textareaLarge', 'Info:', in_array('info', $helps));
		printHiddenInputs($inheritPosts);
		echo '<div class="buttonDiv">';
		printUpdateButton('table');
		$table['table']=$table['table']['table_id'];
		printInfoButton($table);
		$deleteConfirmStr="Är du säker att du vill radera all metadata för tabellen ".$table['table']."?";
		printDeleteButton($table, $deleteConfirmStr, $inheritPosts);
		echo '</div></form></div></div>';
	}

?>
