<?php

	require_once("./functions/manage/printTextarea.php");
	require_once("./functions/manage/printHiddenInputs.php");
	require_once("./functions/manage/printUpdateButton.php");
	require_once("./functions/manage/printInfoButton.php");
	require_once("./functions/manage/printDeleteButton.php");

	function printKeywordForm($keyword, $inheritPosts)
	{
		echo '<div><div class="printXFormDiv"><form method="post">';
		printTextarea($keyword, 'keyword_id', 'textareaMedium', 'Id:');
		printTextarea($keyword, 'abstract', 'textareaLarge', 'Beskrivning:');
		printTextarea($keyword, 'info', 'textareaLarge', 'Info:');
		printHiddenInputs($inheritPosts);
		echo '<div class="buttonDiv">';
		printUpdateButton('keyword');
		$keyword['keyword']=$keyword['keyword']['keyword_id'];
		printInfoButton($keyword);
		echo '</div></form></div>';
		$deleteConfirmStr="Är du säker att du vill radera nyckelordet ".$keyword['keyword']."? Referenser till nyckelordet hanteras separat.";
		printDeleteButton($keyword, $deleteConfirmStr, $inheritPosts);
		echo '</div>';
	}

?>
