<?php

	require_once("./functions/manage/printTextarea.php");
	require_once("./functions/manage/printUpdateSelect.php");
	require_once("./functions/manage/printHiddenInputs.php");
	require_once("./functions/manage/printUpdateButton.php");
	require_once("./functions/manage/printInfoButton.php");
	require_once("./functions/manage/printDeleteButton.php");

	function printSourceForm($source, $selectables, $inheritPosts)
	{
		echo '<div><div class="printXFormDiv"><form method="post">';
		printTextarea($source, 'source_id', 'textareaMedium', 'Id:');
		printUpdateSelect($source, array('service'=>$selectables['services']), 'bodySelect', 'Tjänst:');
		printUpdateSelect($source, array('with_geometry'=>array("f", "t")), 'miniSelect', 'With_geometry:');
		printTextarea($source, 'fi_point_tolerance', 'textareaSmall', 'Fi_point_tolerance:');
		printTextarea($source, 'ttl', 'textareaSmall', 'Ttl:');
		printUpdateSelect($source, array('tilegrid'=>$selectables['tilegrids']), 'bodySelect', 'Tilegrid:');
		printTextarea($source, 'softversion', 'textareaSmall', 'Programversion:');
		printTextarea($source, 'abstract', 'textareaLarge', 'Beskrivning:');
		printUpdateSelect($source, array('contact'=>$selectables['contacts']), 'bodySelect', 'Kontakt:');
		printTextarea($source, 'updated', 'textareaMedium', 'Uppdaterad (åååå-mm-dd):');
		printTextarea($source, 'history', 'textareaLarge', 'Tillkomsthistorik:');
		printTextarea($source, 'info', 'textareaLarge', 'Info:');
		if (isset(current($source)['tables']) && !empty(trim(current($source)['tables'], '{}')))
		{
			printTextarea($source, 'tables', 'textareaLarge', 'Tabeller:', 'yes');
		}
		printHiddenInputs($inheritPosts);
		echo '<div class="buttonDiv">';
		printUpdateButton('source');
		$source['source']=$source['source']['source_id'];
		printInfoButton($source);
		echo '</div></form></div>';
		$deleteConfirmStr="Är du säker att du vill radera källan ".$source['source']."? Referenser till källan hanteras separat.";
		printDeleteButton($source, $deleteConfirmStr, $inheritPosts);
		echo '</div>';
	}
	
?>
