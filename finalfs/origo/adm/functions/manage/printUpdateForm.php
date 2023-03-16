<?php

	function printUpdateForm($update, $inheritPosts)
	{
		echo '<div><div style="float:left;"><form method="post" style="line-height:2">';
		printTextarea($update, 'update_id', 'textareaMedium', 'Id:');
		printTextarea($update, 'name', 'textareaMedium', 'Name:');
		printTextarea($update, 'interval', 'textareaMedium', 'Intervall:');
		printTextarea($update, 'method', 'textareaMedium', 'Metod:');
		echo '</br>';
		printTextarea($update, 'info', 'textareaLarge', 'Info:');
		printHiddenInputs($inheritPosts);
		echo '<div class="buttonDiv">';
		printUpdateButton('update');
		$update['update']=$update['update']['update_id'];
		printInfoButton($update);
		echo '</div></form></div>';
		$deleteConfirmStr="Är du säker att du vill radera uppdateringsrutinen ".$update['update']."? Referenser till uppdateringsrutinen hanteras separat.";
		printDeleteButton($update, $deleteConfirmStr);
		echo '</div>';
	}
	
?>
