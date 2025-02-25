<?php

	require_once("./functions/manage/isFullTarget.php");
	require_once("./functions/manage/printTextarea.php");
	require_once("./functions/manage/printHiddenInputs.php");
	require_once("./functions/manage/printUpdateButton.php");
	require_once("./functions/manage/printInfoButton.php");
	require_once("./functions/manage/printDeleteButton.php");
	require_once("./functions/manage/targetId.php");

	// Takes a full contact target (array), inheritPosts (array), and helps (array).
	// Prints form fields and buttons that are used to view and edit the configuration for the given contact.
	function printContactForm($contact, $inheritPosts, $helps=array())
	{
		if (!isFullTarget($contact))
		{
			die("printContactForm($contact, $inheritPosts, $helps=array()) failed!");
		}
		echo '<div><div class="printXFormDiv"><form method="post">';
		printTextarea($contact, 'contact_id', 'textareaMedium', 'Id:', in_array('contact_id', $helps));
		printTextarea($contact, 'name', 'textareaMedium', 'Namn:', in_array('name', $helps));
		printTextarea($contact, 'web', 'textareaMedium', 'Webbsida:', in_array('web', $helps));
		printTextarea($contact, 'email', 'textareaMedium', 'E-mail:', in_array('email', $helps));
		printTextarea($contact, 'abstract', 'textareaLarge', 'Beskrivning:', in_array('abstract', $helps));
		printTextarea($contact, 'info', 'textareaLarge', 'Info:', in_array('info', $helps));
		printHiddenInputs($inheritPosts);
		echo '<div class="buttonDiv">';
		printUpdateButton('contact');
		printCopyButton('contact');
		$contact=makeTargetBasic($contact);
		printInfoButton($contact);
		$deleteConfirmStr="Är du säker att du vill radera kontakten ".targetId($contact)."? Referenser till kontakten hanteras separat.";
		printDeleteButton($contact, $deleteConfirmStr, $inheritPosts);
		echo '</div></form></div></div>';
	}

?>
