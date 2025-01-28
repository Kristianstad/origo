<?php

	require_once("./functions/manage/isFullTarget.php");
	require_once("./functions/manage/targetConfigParam.php");
	require_once("./functions/manage/printTextarea.php");
	require_once("./functions/manage/printUpdateSelect.php");
	require_once("./functions/manage/printHiddenInputs.php");
	require_once("./functions/manage/printUpdateButton.php");
	require_once("./functions/manage/printInfoButton.php");
	require_once("./functions/manage/printDeleteButton.php");

	// Takes a full source target (array), source selectables (array), inheritPosts (array), and helps (array).
	// Prints form fields and buttons that are used to view and edit the configuration for the given source.
	function printSourceForm($source, $selectables, $inheritPosts, $helps=array())
	{
		if (!isFullTarget($source))
		{
			die("printSourceForm($source, $selectables, $inheritPosts, $helps=array()) failed!");
		}
		echo '<div><div class="printXFormDiv"><form method="post">';
		printTextarea($source, 'source_id', 'textareaMedium', 'Id:', in_array('source_id', $helps));
		printUpdateSelect($source, array('service'=>$selectables['services']), 'bodySelect', 'Tjänst:', in_array('service', $helps));
		$sourceServiceId=targetConfigParam($source, 'service');
		if (!empty($sourceServiceId))
		{
			$sourceServiceType=targetConfigParam($source, 'service_type');
			if ($sourceServiceType == "File")
			{
				printTextarea($source, 'file', 'textareaLarge', 'Fil:', in_array('file', $helps));
			}
			else
			{
				printUpdateSelect($source, array('with_geometry'=>array("f", "t")), 'miniSelect', 'With_geometry:', in_array('with_geometry', $helps));
				printTextarea($source, 'fi_point_tolerance', 'textareaSmall', 'Fi_point_tolerance:', in_array('fi_point_tolerance', $helps));
				printTextarea($source, 'ttl', 'textareaSmall', 'Ttl:', in_array('ttl', $helps));
				printUpdateSelect($source, array('tilegrid'=>$selectables['tilegrids']), 'bodySelect', 'Tilegrid:', in_array('tilegrid', $helps));
				printTextarea($source, 'softversion', 'textareaSmall', 'Programversion:', in_array('softversion', $helps));
				if (isset(current($source)['tables']) && !empty(trim(current($source)['tables'], '{}')))
				{
					printTextarea($source, 'tables', 'textareaLarge', 'Tabeller:', 'yes');
				}
			}
			printTextarea($source, 'abstract', 'textareaLarge', 'Beskrivning:', in_array('abstract', $helps));
			printUpdateSelect($source, array('contact'=>$selectables['contacts']), 'bodySelect', 'Kontakt:', in_array('contact', $helps));
			printTextarea($source, 'updated', 'textareaMedium', 'Uppdaterad (åååå-mm-dd):', in_array('updated', $helps));
			printTextarea($source, 'history', 'textareaLarge', 'Tillkomsthistorik:', in_array('history', $helps));
		}
		printTextarea($source, 'info', 'textareaLarge', 'Info:', in_array('info', $helps));
		printHiddenInputs($inheritPosts);
		echo '<div class="buttonDiv">';
		printUpdateButton('source');
		$source=makeTargetBasic($source);
		printInfoButton($source);
		$deleteConfirmStr="Är du säker att du vill radera källan ".$source['source']."? Referenser till källan hanteras separat.";
		printDeleteButton($source, $deleteConfirmStr, $inheritPosts);
		echo '</div></form></div></div>';
	}

?>
