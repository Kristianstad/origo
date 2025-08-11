<?php

	// Uses manage functions: isFullTarget, sizePosts, printTextarea, printHiddenInputs, printUpdateButton, printInfoButton, printDeleteButton, targetId

	// Takes a full keyword target (array), inheritPosts (array), and helps (array).
	// Prints form fields and buttons that are used to view and edit the configuration for the given keyword.
	function printKeywordForm($keyword, $inheritPosts, $helps=array())
	{
		if (!isFullTarget($keyword))
		{
			die("printKeywordForm($keyword, $inheritPosts, $helps=array()) failed!");
		}
		$sizePosts=sizePosts($inheritPosts);
		echo '<div><div class="printXFormDiv"><form method="post">';
		printTextarea($keyword, 'keyword_id', 'textareaMedium', 'Id:', in_array('keyword_id', $helps), $sizePosts);
		printTextarea($keyword, 'abstract', 'textareaLarge', 'Beskrivning:', in_array('abstract', $helps), $sizePosts);
		printTextarea($keyword, 'info', 'textareaLarge', 'Info:', in_array('info', $helps), $sizePosts);
		printHiddenInputs($inheritPosts);
		echo '<div class="buttonDiv">';
		printUpdateButton('keyword');
		printCopyButton('keyword');
		$keyword=makeTargetBasic($keyword);
		printInfoButton($keyword);
		$deleteConfirmStr="Är du säker att du vill radera nyckelordet ".targetId($keyword)."? Referenser till nyckelordet hanteras separat.";
		printDeleteButton($keyword, $deleteConfirmStr, $inheritPosts);
		echo '</div></form></div></div>';
	}

?>
