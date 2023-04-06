<?php

	require_once("./functions/manage/printTextarea.php");
	require_once("./functions/manage/printHiddenInputs.php");
	require_once("./functions/manage/printUpdateButton.php");
	require_once("./functions/manage/printInfoButton.php");
	require_once("./functions/manage/printDeleteButton.php");

	function printTableForm($table, $inheritPosts)
	{
		echo '<div><div style="float:left;"><form method="post" style="line-height:2">';
		printTextarea($table, 'table_id', 'textareaMedium', 'Id:');
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
