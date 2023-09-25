<?php

	require_once("./functions/manage/isFullTarget.php");
	require_once("./functions/manage/printTextarea.php");
	require_once("./functions/manage/printHiddenInputs.php");
	require_once("./functions/manage/printUpdateButton.php");
	require_once("./functions/manage/printInfoButton.php");
	require_once("./functions/manage/printDeleteButton.php");

	function printOriginForm($origin, $inheritPosts, $helps=array())
	{
		if (!isFullTarget($origin))
		{
			die("printOriginForm($origin, $inheritPosts, $helps=array()) failed!");
		}
		echo '<div><div class="printXFormDiv"><form method="post">';
		printTextarea($origin, 'origin_id', 'textareaMedium', 'Id:', in_array('origin_id', $helps));
		printTextarea($origin, 'name', 'textareaMedium', 'Namn:', in_array('name', $helps));
		printTextarea($origin, 'web', 'textareaMedium', 'Webbsida:', in_array('web', $helps));
		printTextarea($origin, 'email', 'textareaMedium', 'E-mail:', in_array('email', $helps));
		printTextarea($origin, 'abstract', 'textareaLarge', 'Beskrivning:', in_array('abstract', $helps));
		printTextarea($origin, 'info', 'textareaLarge', 'Info:', in_array('info', $helps));
		printHiddenInputs($inheritPosts);
		echo '<div class="buttonDiv">';
		printUpdateButton('origin');
		$origin['origin']=$origin['origin']['origin_id'];
		printInfoButton($origin);
		$deleteConfirmStr="Är du säker att du vill radera ursprungskällan ".$origin['origin']."? Referenser till ursprungskällan hanteras separat.";
		printDeleteButton($origin, $deleteConfirmStr, $inheritPosts);
		echo '</div></form></div></div>';
	}

?>
