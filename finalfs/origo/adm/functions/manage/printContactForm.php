<?php

	require_once("./functions/manage/printTextarea.php");
	require_once("./functions/manage/printHiddenInputs.php");
	require_once("./functions/manage/printUpdateButton.php");
	require_once("./functions/manage/printInfoButton.php");
	require_once("./functions/manage/printDeleteButton.php");

	function printContactForm($contact, $inheritPosts)
	{
		echo '<div><div class="printXFormDiv"><form method="post">';
		printTextarea($contact, 'contact_id', 'textareaMedium', 'Id:');
		printTextarea($contact, 'name', 'textareaMedium', 'Namn:');
		printTextarea($contact, 'web', 'textareaMedium', 'Webbsida:');
		printTextarea($contact, 'email', 'textareaMedium', 'E-mail:');
		echo '</br>';
		printTextarea($contact, 'info', 'textareaLarge', 'Info:');
		printHiddenInputs($inheritPosts);
		echo '<div class="buttonDiv">';
		printUpdateButton('contact');
		$contact['contact']=$contact['contact']['contact_id'];
		printInfoButton($contact);
		echo '</div></form></div>';
		$deleteConfirmStr="Är du säker att du vill radera kontakten ".$contact['contact']."? Referenser till kontakten hanteras separat.";
		printDeleteButton($contact, $deleteConfirmStr, $inheritPosts);
		echo '</div>';
	}
	
?>
