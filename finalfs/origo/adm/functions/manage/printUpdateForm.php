<?php

	require_once("./functions/manage/printTextarea.php");
	require_once("./functions/manage/printHiddenInputs.php");
	require_once("./functions/manage/printUpdateButton.php");
	require_once("./functions/manage/printInfoButton.php");
	require_once("./functions/manage/printDeleteButton.php");

	function printUpdateForm($update, $inheritPosts)
	{
		echo '<div><div class="printXFormDiv"><form method="post">';
		printTextarea($update, 'update_id', 'textareaMedium', 'Id:');
		printTextarea($update, 'name', 'textareaMedium', 'Name:');
		printTextarea($update, 'interval', 'textareaMedium', 'Intervall:');
		printTextarea($update, 'method', 'textareaMedium', 'Metod:');
		printTextarea($update, 'abstract', 'textareaLarge', 'Beskrivning:');
		printTextarea($update, 'info', 'textareaLarge', 'Info:');
		printHiddenInputs($inheritPosts);
		echo '<div class="buttonDiv">';
		printUpdateButton('update');
		$update['update']=$update['update']['update_id'];
		printInfoButton($update);
		echo '</div></form></div>';
		$deleteConfirmStr="Är du säker att du vill radera uppdateringsrutinen ".$update['update']."? Referenser till uppdateringsrutinen hanteras separat.";
		printDeleteButton($update, $deleteConfirmStr, $inheritPosts);
		echo '</div>';
	}
	
?>
