<?php

	require_once("./functions/manage/printTextarea.php");
	require_once("./functions/manage/printHiddenInputs.php");
	require_once("./functions/manage/printUpdateButton.php");
	require_once("./functions/manage/printInfoButton.php");
	require_once("./functions/manage/printDeleteButton.php");

	function printTilegridForm($tilegrid, $inheritPosts)
	{
		echo '<div><div class="printXFormDiv"><form method="post">';
		printTextarea($tilegrid, 'tilegrid_id', 'textareaMedium', 'Id:');
		printTextarea($tilegrid, 'tilesize', 'textareaSmall', 'Tile-storlek:');
		echo '</br>';
		printTextarea($tilegrid, 'info', 'textareaLarge', 'Info:');
		printHiddenInputs($inheritPosts);
		echo '<div class="buttonDiv">';
		printUpdateButton('tilegrid');
		$tilegrid['tilegrid']=$tilegrid['tilegrid']['tilegrid_id'];
		printInfoButton($tilegrid);
		echo '</div></form></div>';
		$deleteConfirmStr="Är du säker att du vill radera tilegriden ".$tilegrid['tilegrid']."? Referenser till tilegriden hanteras separat.";
		printDeleteButton($tilegrid, $deleteConfirmStr, $inheritPosts);
		echo '</div>';
	}
	
?>
