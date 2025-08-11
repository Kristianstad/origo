<?php

	// Uses manage functions: isFullTarget, sizePosts, printTextarea, printHiddenInputs, printUpdateButton, printInfoButton, printDeleteButton, targetId

	// Takes a full tilegrid target (array), inheritPosts (array), and helps (array).
	// Prints form fields and buttons that are used to view and edit the configuration for the given tilegrid.
	function printTilegridForm($tilegrid, $inheritPosts, $helps=array())
	{
		if (!isFullTarget($tilegrid))
		{
			die("printTilegridForm($tilegrid, $inheritPosts, $helps=array()) failed!");
		}
		$sizePosts=sizePosts($inheritPosts);
		echo '<div><div class="printXFormDiv"><form method="post">';
		printTextarea($tilegrid, 'tilegrid_id', 'textareaMedium', 'Id:', in_array('tilegrid_id', $helps), $sizePosts);
		printTextarea($tilegrid, 'tilesize', 'textareaSmall', 'Tile-storlek:', in_array('tilesize', $helps), $sizePosts);
		printTextarea($tilegrid, 'abstract', 'textareaLarge', 'Beskrivning:', in_array('abstract', $helps), $sizePosts);
		printTextarea($tilegrid, 'info', 'textareaLarge', 'Info:', in_array('info', $helps), $sizePosts);
		printHiddenInputs($inheritPosts);
		echo '<div class="buttonDiv">';
		printUpdateButton('tilegrid');
		printCopyButton('tilegrid');
		$tilegrid=makeTargetBasic($tilegrid);
		printInfoButton($tilegrid);
		$deleteConfirmStr="Är du säker att du vill radera tilegriden ".targetId($tilegrid)."? Referenser till tilegriden hanteras separat.";
		printDeleteButton($tilegrid, $deleteConfirmStr, $inheritPosts);
		echo '</div></form></div></div>';
	}

?>
