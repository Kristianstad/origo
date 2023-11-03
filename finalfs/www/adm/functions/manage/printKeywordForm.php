<?php

	require_once("./functions/manage/isFullTarget.php");
	require_once("./functions/manage/printTextarea.php");
	require_once("./functions/manage/printHiddenInputs.php");
	require_once("./functions/manage/printUpdateButton.php");
	require_once("./functions/manage/printInfoButton.php");
	require_once("./functions/manage/printDeleteButton.php");

	function printKeywordForm($keyword, $inheritPosts, $helps=array())
	{
		if (!isFullTarget($keyword))
		{
			die("printKeywordForm($keyword, $inheritPosts, $helps=array()) failed!");
		}
		echo '<div><div class="printXFormDiv"><form method="post">';
		printTextarea($keyword, 'keyword_id', 'textareaMedium', 'Id:', in_array('keyword_id', $helps));
		printTextarea($keyword, 'abstract', 'textareaLarge', 'Beskrivning:', in_array('abstract', $helps));
		printTextarea($keyword, 'info', 'textareaLarge', 'Info:', in_array('info', $helps));
		printHiddenInputs($inheritPosts);
		echo '<div class="buttonDiv">';
		printUpdateButton('keyword');
		$keyword['keyword']=$keyword['keyword']['keyword_id'];
		printInfoButton($keyword);
		$deleteConfirmStr="Är du säker att du vill radera nyckelordet ".$keyword['keyword']."? Referenser till nyckelordet hanteras separat.";
		printDeleteButton($keyword, $deleteConfirmStr, $inheritPosts);
		echo '</div></form></div></div>';
	}

?>
