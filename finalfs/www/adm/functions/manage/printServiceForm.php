<?php

	require_once("./functions/manage/isFullTarget.php");
	require_once("./functions/manage/printTextarea.php");
	require_once("./functions/manage/targetConfigParam.php");
	require_once("./functions/manage/printHiddenInputs.php");
	require_once("./functions/manage/printUpdateButton.php");
	require_once("./functions/manage/printInfoButton.php");
	require_once("./functions/manage/printDeleteButton.php");

	// Takes a full service target (array), inheritPosts (array), and helps (array).
	// Prints form fields and buttons that are used to view and edit the configuration for the given service.
	function printServiceForm($service, $inheritPosts, $helps=array())
	{
		if (!isFullTarget($service))
		{
			die("printServiceForm($service, $inheritPosts, $helps=array()) failed!");
		}
		echo '<div><div class="printXFormDiv"><form method="post">';
		printTextarea($service, 'service_id', 'textareaMedium', 'Id:', in_array('service_id', $helps));
		printUpdateSelect($service, array('type'=>array("File","QGIS","Geoserver")), 'bodySelect', 'Typ:', in_array('type', $helps));
		$serviceType=targetConfigParam($service, 'type');
		if (!empty($serviceType))
		{
			if ($serviceType != 'File')
			{
				printTextarea($service, 'base_url', 'textareaLarge', 'Huvudurl:', in_array('base_url', $helps));
			}
			printUpdateSelect($service, array('restricted'=>array("f", "t")), 'miniSelect', 'Rättighetsstyrd:', in_array('restricted', $helps));
			printTextarea($service, 'formats', 'textareaLarge', 'Tillgängliga format:', in_array('formats', $helps));
			printTextarea($service, 'abstract', 'textareaLarge', 'Beskrivning:', in_array('abstract', $helps));
		}
		printTextarea($service, 'info', 'textareaLarge', 'Info:', in_array('info', $helps));
		printHiddenInputs($inheritPosts);
		echo '<div class="buttonDiv">';
		printUpdateButton('service');
		$service=makeTargetBasic($service);
		printInfoButton($service);
		$deleteConfirmStr="Är du säker att du vill radera tjänsten ".$service['service']."? Referenser till tjänsten hanteras separat.";
		printDeleteButton($service, $deleteConfirmStr, $inheritPosts);
		echo '</div></form></div></div>';
	}

?>
