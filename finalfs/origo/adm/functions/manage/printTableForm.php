<?php

	require_once("./functions/manage/printTextarea.php");
	require_once("./functions/manage/printHiddenInputs.php");
	require_once("./functions/manage/printUpdateButton.php");
	require_once("./functions/manage/printInfoButton.php");
	require_once("./functions/manage/printDeleteButton.php");

	function printTableForm($table, $selectables, $inheritPosts)
	{
		echo '<div><div style="float:left;"><form method="post" style="line-height:2">';
		printTextarea($table, 'table_id', 'textareaMedium', 'Id:');
		printTextarea($table, 'info', 'textareaLarge', 'Info:');
		printUpdateSelect($table, array('contact'=>$selectables['contacts']), 'bodySelect', 'Kontakt:');
		echo '<br>';
		printUpdateSelect($table, array('origin'=>$selectables['origins']), 'bodySelect', 'Ursprungskälla:');
		printTextarea($table, 'updated', 'textareaMedium', 'Uppdaterad (åååå-mm-dd):');
		printUpdateSelect($table, array('update'=>$selectables['updates']), 'bodySelect', 'Uppdatering:');
		echo '<br>';
		printTextarea($table, 'history', 'textareaLarge', 'Tillkomsthistorik:');
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
