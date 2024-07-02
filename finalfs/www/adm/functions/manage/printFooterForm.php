<?php

	require_once("./functions/manage/isFullTarget.php");
	require_once("./functions/manage/printTextarea.php");
	require_once("./functions/manage/printHiddenInputs.php");
	require_once("./functions/manage/printUpdateButton.php");
	require_once("./functions/manage/printInfoButton.php");
	require_once("./functions/manage/printDeleteButton.php");

	function printFooterForm($footer, $inheritPosts, $helps=array())
	{
		if (!isFullTarget($footer))
		{
			die("printFooterForm($footer, $inheritPosts, $helps=array()) failed!");
		}
		echo '<div><div class="printXFormDiv"><form method="post">';
		printTextarea($footer, 'footer_id', 'textareaMedium', 'Id:', in_array('footer_id', $helps));
		printTextarea($footer, 'img', 'textareaLarge', 'Logotyp:', in_array('img', $helps));
		printTextarea($footer, 'url', 'textareaLarge', 'Url:', in_array('url', $helps));
		printTextarea($footer, 'text', 'textareaMedium', 'Text:', in_array('text', $helps));
		printTextarea($footer, 'abstract', 'textareaLarge', 'Beskrivning:', in_array('abstract', $helps));
		printTextarea($footer, 'info', 'textareaLarge', 'Info:', in_array('info', $helps));
		printHiddenInputs($inheritPosts);
		echo '<div class="buttonDiv">';
		printUpdateButton('footer');
		$footer['footer']=$footer['footer']['footer_id'];
		printInfoButton($footer);
		$deleteConfirmStr="Är du säker att du vill radera sidfoten ".$footer['footer']."? Referenser till sidfoten hanteras separat.";
		printDeleteButton($footer, $deleteConfirmStr, $inheritPosts);
		echo '</div></form></div></div>';
	}

?>