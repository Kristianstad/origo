<?php

	// Uses manage functions: isFullTarget, sizePosts, printTextarea, printHiddenInputs, printUpdateButton, printInfoButton, printDeleteButton, targetId

	// Takes a full aduser target (array), inheritPosts (array), and helps (array).
	// Prints form fields and buttons that are used to view and edit the configuration for the given aduser.
	function printAduserForm($aduser, $inheritPosts, $helps=array())
	{
		if (!isFullTarget($aduser))
		{
			die("printAduserForm($aduser, $inheritPosts, $helps=array()) failed!");
		}
		$sizePosts=sizePosts($inheritPosts);
		echo '<div><div class="printXFormDiv"><form method="post">';
		printTextarea($aduser, 'aduser_id', 'textareaMedium', 'Id:', in_array('aduser_id', $helps), $sizePosts, true);
		printTextarea($aduser, 'name', 'textareaMedium', 'Namn:', in_array('name', $helps), $sizePosts, true);
		printTextarea($aduser, 'email', 'textareaMedium', 'E-mail:', in_array('email', $helps), $sizePosts, true);
		printTextarea($aduser, 'company', 'textareaMedium', 'Företag/Förvaltning:', in_array('company', $helps), $sizePosts, true);
		printTextarea($aduser, 'department', 'textareaMedium', 'Avdelning:', in_array('department', $helps), $sizePosts, true);
		printTextarea($aduser, 'lastlogin', 'textareaMedium', 'Senast inloggad:', in_array('lastlogin', $helps), $sizePosts, true);
		printTextarea($aduser, 'adgroups', 'textareaMedium', 'AD-grupper:', in_array('adgroups', $helps), $sizePosts, true);
		printTextarea($aduser, 'abstract', 'textareaLarge', 'Beskrivning:', in_array('abstract', $helps), $sizePosts);
		printTextarea($aduser, 'info', 'textareaLarge', 'Info:', in_array('info', $helps), $sizePosts);
		printHiddenInputs($inheritPosts);
		echo '<div class="buttonDiv">';
		printUpdateButton('aduser');
		printCopyButton('aduser');
		$aduser=makeTargetBasic($aduser);
		printInfoButton($aduser);
		$deleteConfirmStr="Är du säker att du vill radera AD-användaren ".targetId($aduser)."? Referenser till AD-användaren hanteras separat.";
		printDeleteButton($aduser, $deleteConfirmStr, $inheritPosts);
		echo '</div></form></div></div>';
	}

?>
