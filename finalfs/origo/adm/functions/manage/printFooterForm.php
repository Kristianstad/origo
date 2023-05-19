<?php

	require_once("./functions/manage/printTextarea.php");
	require_once("./functions/manage/printHiddenInputs.php");
	require_once("./functions/manage/printUpdateButton.php");
	require_once("./functions/manage/printInfoButton.php");
	require_once("./functions/manage/printDeleteButton.php");

	function printFooterForm($footer, $inheritPosts)
	{
		echo '<div><div class="printXFormDiv"><form method="post">';
		printTextarea($footer, 'footer_id', 'textareaMedium', 'Id:');
		printTextarea($footer, 'img', 'textareaLarge', 'Logotyp:');
		printTextarea($footer, 'url', 'textareaLarge', 'Url:');
		echo '<br>';
		printTextarea($footer, 'text', 'textareaMedium', 'Text:');
		printTextarea($footer, 'abstract', 'textareaLarge', 'Beskrivning:');
		echo '<br>';
		printTextarea($footer, 'info', 'textareaLarge', 'Info:');
		printHiddenInputs($inheritPosts);
		echo '<div class="buttonDiv">';
		printUpdateButton('footer');
		$footer['footer']=$footer['footer']['footer_id'];
		printInfoButton($footer);
		echo '</div></form></div>';
		$deleteConfirmStr="Är du säker att du vill radera sidfoten ".$footer['footer']."? Referenser till sidfoten hanteras separat.";
		printDeleteButton($footer, $deleteConfirmStr, $inheritPosts);
		echo '</div>';
	}
	
?>
