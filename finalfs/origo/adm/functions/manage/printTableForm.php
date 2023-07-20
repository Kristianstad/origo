<?php

	require_once("./functions/manage/printTextarea.php");
	require_once("./functions/manage/printHiddenInputs.php");
	require_once("./functions/manage/printUpdateButton.php");
	require_once("./functions/manage/printInfoButton.php");
	require_once("./functions/manage/printDeleteButton.php");

	function printTableForm($table, $selectables, $inheritPosts)
	{
		echo '<div><div class="printXFormDiv"><form method="post">';
		printTextarea($table, 'table_id', 'textareaMedium', 'Id:');
		printTextarea($table, 'abstract', 'textareaLarge', 'Beskrivning:');
		printTextarea($table, 'keywords', 'textareaLarge', 'Nyckelord:');
		printUpdateSelect($table, array('contact'=>$selectables['contacts']), 'bodySelect', 'Kontakt:');
		printUpdateSelect($table, array('origin'=>$selectables['origins']), 'bodySelect', 'Ursprungskälla:');
		printTextarea($table, 'updated', 'textareaMedium', 'Uppdaterad (åååå-mm-dd):');
		printUpdateSelect($table, array('update'=>$selectables['updates']), 'bodySelect', 'Uppdatering:');
		printTextarea($table, 'history', 'textareaLarge', 'Tillkomsthistorik:');
		printTextarea($table, 'info', 'textareaLarge', 'Info:');
		printHiddenInputs($inheritPosts);
		echo '<div class="buttonDiv">';
		printUpdateButton('table');
		$table['table']=$table['table']['table_id'];
		printInfoButton($table);
		echo '</div></form></div>';
		$deleteConfirmStr="Är du säker att du vill radera all metadata för tabellen ".$table['table']."?";
		printDeleteButton($table, $deleteConfirmStr, $inheritPosts);
		echo '</div>';
	}

?>
