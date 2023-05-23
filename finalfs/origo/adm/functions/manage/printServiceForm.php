<?php

	require_once("./functions/manage/printTextarea.php");
	require_once("./functions/manage/printHiddenInputs.php");
	require_once("./functions/manage/printUpdateButton.php");
	require_once("./functions/manage/printInfoButton.php");
	require_once("./functions/manage/printDeleteButton.php");

	function printServiceForm($service, $inheritPosts)
	{
		echo '<div><div class="printXFormDiv"><form method="post">';
		printTextarea($service, 'service_id', 'textareaMedium', 'Id:');
		printTextarea($service, 'base_url', 'textareaLarge', 'Huvudurl:');
		printTextarea($service, 'type', 'textareaMedium', 'Typ:');
		printTextarea($service, 'abstract', 'textareaLarge', 'Beskrivning:');
		printTextarea($service, 'info', 'textareaLarge', 'Info:');
		printHiddenInputs($inheritPosts);
		echo '<div class="buttonDiv">';
		printUpdateButton('service');
		$service['service']=$service['service']['service_id'];
		printInfoButton($service);
		echo '</div></form></div>';
		$deleteConfirmStr="Är du säker att du vill radera tjänsten ".$service['service']."? Referenser till tjänsten hanteras separat.";
		printDeleteButton($service, $deleteConfirmStr, $inheritPosts);
		echo '</div>';
	}
	
?>
