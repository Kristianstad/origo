<?php

	require_once("./functions/manage/printTextarea.php");
	require_once("./functions/manage/printUpdateSelect.php");
	require_once("./functions/manage/printHiddenInputs.php");
	require_once("./functions/manage/printUpdateButton.php");
	require_once("./functions/manage/printInfoButton.php");
	require_once("./functions/manage/printWriteConfigButton.php");
	require_once("./functions/manage/printExportJsonButton.php");
	require_once("./functions/manage/printDeleteButton.php");

	function printMapForm($map, $selectables, $inheritPosts)
	{
		echo '<div><div style="float:left;"><form method="post" style="line-height:2">';
		printTextarea($map, 'map_id', 'textareaMedium', 'Id:');
		printTextarea($map, 'layers', 'textareaLarge', 'Lager:');
		printTextarea($map, 'groups', 'textareaLarge', 'Grupper:');
		echo '<br>';
		printUpdateSelect($map, array('footer'=>$selectables['footers']), 'bodySelect', 'Sidfot:');
		printTextarea($map, 'controls', 'textareaLarge', 'Kontroller:');
		printTextarea($map, 'proj4defs', 'textareaMedium', 'Proj4defs:');
		printTextarea($map, 'featureinfooptions', 'textareaMedium', 'FeatureInfoOptions:');
		echo '<br>';
		printTextarea($map, 'center', 'textareaMedium', 'Mittpunkt:');
		printTextarea($map, 'zoom', 'textareaXSmall', 'Zoom:');
		printUpdateSelect($map, array('tilegrid'=>$selectables['tilegrids']), 'bodySelect', 'Tilegrid:');
		printTextarea($map, 'info', 'textareaLarge', 'Info:');
		echo '<br>';
		printUpdateSelect($map, array('show_meta'=>array("f", "t")), 'miniSelect', 'Visa metadata:');
		printUpdateSelect($map, array('embedded'=>array("f", "t")), 'miniSelect', 'Inbäddad:');
		$map['map']=$map['map']['map_id'];
		printHiddenInputs($inheritPosts);
		echo '<div class="buttonDiv">';
		printUpdateButton('map');
		printInfoButton($map);
		printWriteConfigButton($map['map']);
		echo '</div>';
		printExportJsonButton($map['map']);
		echo '</form></div>';
		$deleteConfirmStr="Är du säker på att du vill radera kartan ".$map['map']."? Ingående kontroller, grupper och lager påverkas ej.";
		printDeleteButton($map, $deleteConfirmStr, $inheritPosts);
		echo '</div>';
	}
	
?>
