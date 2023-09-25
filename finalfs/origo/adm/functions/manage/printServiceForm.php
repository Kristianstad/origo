<?php

	require_once("./functions/manage/isFullTarget.php");
	require_once("./functions/manage/printTextarea.php");
	require_once("./functions/manage/printHiddenInputs.php");
	require_once("./functions/manage/printUpdateButton.php");
	require_once("./functions/manage/printInfoButton.php");
	require_once("./functions/manage/printDeleteButton.php");

	function printServiceForm($service, $inheritPosts, $helps=array())
	{
		if (!isFullTarget($service))
		{
			die("printServiceForm($service, $inheritPosts, $helps=array()) failed!");
		}
		echo '<div><div class="printXFormDiv"><form method="post">';
		printTextarea($service, 'service_id', 'textareaMedium', 'Id:', in_array('service_id', $helps));
		printTextarea($service, 'base_url', 'textareaLarge', 'Huvudurl:', in_array('base_url', $helps));
		printTextarea($service, 'type', 'textareaMedium', 'Typ:', in_array('type', $helps));
		printTextarea($service, 'abstract', 'textareaLarge', 'Beskrivning:', in_array('abstract', $helps));
		printTextarea($service, 'info', 'textareaLarge', 'Info:', in_array('info', $helps));
		printHiddenInputs($inheritPosts);
		echo '<div class="buttonDiv">';
		printUpdateButton('service');
		$service['service']=$service['service']['service_id'];
		printInfoButton($service);
		$deleteConfirmStr="Är du säker att du vill radera tjänsten ".$service['service']."? Referenser till tjänsten hanteras separat.";
		printDeleteButton($service, $deleteConfirmStr, $inheritPosts);
		echo '</div></form></div></div>';
	}

?>
