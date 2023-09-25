<?php

	require_once("./functions/manage/isFullTarget.php");
	require_once("./functions/manage/printTextarea.php");
	require_once("./functions/manage/printHiddenInputs.php");
	require_once("./functions/manage/printUpdateButton.php");
	require_once("./functions/manage/printInfoButton.php");
	require_once("./functions/manage/printDeleteButton.php");

	function printTableForm($table, $selectables, $inheritPosts, $helps=array())
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
		printTextarea($table, 'updated', 'textareaMedium', 'Uppdaterad (åååå-mm-dd):', in_array('updated', $helps));
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
