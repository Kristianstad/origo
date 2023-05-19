<?php

	require_once("./functions/manage/printTextarea.php");
	require_once("./functions/manage/printHiddenInputs.php");
	require_once("./functions/manage/printUpdateButton.php");
	require_once("./functions/manage/printInfoButton.php");
	require_once("./functions/manage/printDeleteButton.php");

	function printOriginForm($origin, $inheritPosts)
	{
		echo '<div><div class="printXFormDiv"><form method="post">';
		printTextarea($origin, 'origin_id', 'textareaMedium', 'Id:');
		printTextarea($origin, 'name', 'textareaMedium', 'Namn:');
		printTextarea($origin, 'web', 'textareaMedium', 'Webbsida:');
		printTextarea($origin, 'email', 'textareaMedium', 'E-mail:');
		echo '</br>';
		printTextarea($origin, 'abstract', 'textareaLarge', 'Beskrivning:');
		printTextarea($origin, 'info', 'textareaLarge', 'Info:');
		printHiddenInputs($inheritPosts);
		echo '<div class="buttonDiv">';
		printUpdateButton('origin');
		$origin['origin']=$origin['origin']['origin_id'];
		printInfoButton($origin);
		echo '</div></form></div>';
		$deleteConfirmStr="Är du säker att du vill radera ursprungskällan ".$origin['origin']."? Referenser till ursprungskällan hanteras separat.";
		printDeleteButton($origin, $deleteConfirmStr, $inheritPosts);
		echo '</div>';
	}
	
?>
