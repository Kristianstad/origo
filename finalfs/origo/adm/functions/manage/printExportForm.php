<?php

	function printExportForm($export, $inheritPosts)
	{
		echo '<div><div style="float:left;"><form method="post" style="line-height:2">';
		printTextarea($export, 'export_id', 'textareaMedium', 'Id:');
		printTextarea($export, 'resource', 'textareaMedium', 'Resurs:');
		printTextarea($export, 'style', 'textareaMedium', 'Stil:');
		echo '</br>';
		printTextarea($export, 'info', 'textareaLarge', 'Info:');
		printHiddenInputs($inheritPosts);
		echo '<div class="buttonDiv">';
		printUpdateButton('export');
		$export['export']=$export['export']['export_id'];
		printInfoButton($export);
		echo '</div></form></div>';
		$deleteConfirmStr="Är du säker att du vill radera exportinställningen ".$export['export']."? Referenser till exportinställningen hanteras separat.";
		printDeleteButton($export, $deleteConfirmStr);
		echo '</div>';
	}
	
?>
