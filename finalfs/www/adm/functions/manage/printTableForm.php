<?php

	require_once("./functions/manage/isFullTarget.php");
	require_once("./functions/manage/printTextarea.php");
	require_once("./functions/manage/printHiddenInputs.php");
	require_once("./functions/manage/printUpdateButton.php");
	require_once("./functions/manage/printInfoButton.php");
	require_once("./functions/manage/printDeleteButton.php");
	require_once("./functions/manage/targetId.php");
	require_once("./functions/manage/setTargetConfigParam.php");

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
		//echo '<span style="vertical-align: text-bottom"> Updaterad: <iframe src="updated.php?table='.substr(targetId($table), strpos(targetId($table), '.')+1).'" style="width:6rem; height:1rem; margin:0; padding: 0; border:none"></iframe></span>';
		$dbh2=dbh($dbhConnectionString);
		$tableWithSchema=substr(targetId($table), strpos(targetId($table), '.')+1);
		$updated=substr(updated_from_table($dbh2, $tableWithSchema)[0], 0, 10);
		setTargetConfigParam($table, 'updated', $updated);
		printTextarea($table, 'updated', 'textareaMedium', 'Uppdaterad:', in_array('updated', $helps), true);
		printUpdateSelect($table, array('update'=>$selectables['updates']), 'bodySelect', 'Uppdatering:', in_array('update', $helps));
		printTextarea($table, 'history', 'textareaLarge', 'Tillkomsthistorik:', in_array('history', $helps));
		printTextarea($table, 'info', 'textareaLarge', 'Info:', in_array('info', $helps));
		printHiddenInputs($inheritPosts);
		echo '<div class="buttonDiv">';
		printUpdateButton('table');
		printCopyButton('table');
		$table=makeTargetBasic($table);
		printInfoButton($table);
		$deleteConfirmStr="Är du säker att du vill radera all metadata för tabellen ".targetId($table)."?";
		printDeleteButton($table, $deleteConfirmStr, $inheritPosts);
		echo '</div></form></div></div>';
	}

?>
