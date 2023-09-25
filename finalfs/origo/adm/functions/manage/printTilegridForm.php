<?php

	require_once("./functions/manage/isFullTarget.php");
	require_once("./functions/manage/printTextarea.php");
	require_once("./functions/manage/printHiddenInputs.php");
	require_once("./functions/manage/printUpdateButton.php");
	require_once("./functions/manage/printInfoButton.php");
	require_once("./functions/manage/printDeleteButton.php");

	function printTilegridForm($tilegrid, $inheritPosts, $helps=array())
	{
		if (!isFullTarget($tilegrid))
		{
			die("printTilegridForm($tilegrid, $inheritPosts, $helps=array()) failed!");
		}
		echo '<div><div class="printXFormDiv"><form method="post">';
		printTextarea($tilegrid, 'tilegrid_id', 'textareaMedium', 'Id:', in_array('tilegrid_id', $helps));
		printTextarea($tilegrid, 'tilesize', 'textareaSmall', 'Tile-storlek:', in_array('tilesize', $helps));
		printTextarea($tilegrid, 'abstract', 'textareaLarge', 'Beskrivning:', in_array('abstract', $helps));
		printTextarea($tilegrid, 'info', 'textareaLarge', 'Info:', in_array('info', $helps));
		printHiddenInputs($inheritPosts);
		echo '<div class="buttonDiv">';
		printUpdateButton('tilegrid');
		$tilegrid['tilegrid']=$tilegrid['tilegrid']['tilegrid_id'];
		printInfoButton($tilegrid);
		$deleteConfirmStr="Är du säker att du vill radera tilegriden ".$tilegrid['tilegrid']."? Referenser till tilegriden hanteras separat.";
		printDeleteButton($tilegrid, $deleteConfirmStr, $inheritPosts);
		echo '</div></form></div></div>';
	}

?>
