<?php

	// Uses manage functions: isFullTarget, sizePosts, printTextarea, printHiddenInputs, printUpdateButton, printInfoButton, printDeleteButton, targetId

	// Takes a full contact target (array), inheritPosts (array), and helps (array).
	// Prints form fields and buttons that are used to view and edit the configuration for the given contact.
	function printContactForm($contact, $inheritPosts, $helps=array())
	{
		if (!isFullTarget($contact))
		{
			die("printContactForm($contact, $inheritPosts, $helps=array()) failed!");
		}
		$sizePosts=sizePosts($inheritPosts);
		echo '<div><div class="printXFormDiv"><form method="post">';
		printTextarea($contact, 'contact_id', 'textareaMedium', 'Id:', in_array('contact_id', $helps), $sizePosts);
		printTextarea($contact, 'name', 'textareaMedium', 'Namn:', in_array('name', $helps), $sizePosts);
		printTextarea($contact, 'web', 'textareaMedium', 'Webbsida:', in_array('web', $helps), $sizePosts);
		printTextarea($contact, 'email', 'textareaMedium', 'E-mail:', in_array('email', $helps), $sizePosts);
		printTextarea($contact, 'abstract', 'textareaLarge', 'Beskrivning:', in_array('abstract', $helps), $sizePosts);
		printTextarea($contact, 'info', 'textareaLarge', 'Info:', in_array('info', $helps), $sizePosts);
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
