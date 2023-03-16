<?php

	function printOriginForm($origin, $inheritPosts)
	{
		echo '<div><div style="float:left;"><form method="post" style="line-height:2">';
		printTextarea($origin, 'origin_id', 'textareaMedium', 'Id:');
		printTextarea($origin, 'name', 'textareaMedium', 'Namn:');
		printTextarea($origin, 'web', 'textareaMedium', 'Webbsida:');
		printTextarea($origin, 'email', 'textareaMedium', 'E-mail:');
		echo '</br>';
		printTextarea($origin, 'info', 'textareaLarge', 'Info:');
		printHiddenInputs($inheritPosts);
		echo '<div class="buttonDiv">';
		printUpdateButton('origin');
		$origin['origin']=$origin['origin']['origin_id'];
		printInfoButton($origin);
		echo '</div></form></div>';
		$deleteConfirmStr="Är du säker att du vill radera ursprungskällan ".$origin['origin']."? Referenser till ursprungskällan hanteras separat.";
		printDeleteButton($origin, $deleteConfirmStr);
		echo '</div>';
	}
	
?>
