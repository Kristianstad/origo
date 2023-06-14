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
		echo '<div><div class="printXFormDiv"><form method="post">';
		printTextarea($map, 'map_id', 'textareaMedium', 'Id:');
		printTextarea($map, 'layers', 'textareaLarge', 'Lager:');
		printTextarea($map, 'groups', 'textareaLarge', 'Grupper:');
		printTextarea($map, 'controls', 'textareaLarge', 'Kontroller:');
		printTextarea($map, 'proj4defs', 'textareaMedium', 'Proj4defs:');
		printUpdateSelect($map, array('footer'=>$selectables['footers']), 'bodySelect', 'Sidfot:');
		printTextarea($map, 'featureinfooptions', 'textareaMedium', 'FeatureInfoOptions:');
		printTextarea($map, 'projectioncode', 'textareaMedium', 'Projektion:');
		printTextarea($map, 'projectionextent', 'textareaMedium', 'Projektionsutbredning:');
		printTextarea($map, 'extent', 'textareaMedium', 'Utbredning:');
		printTextarea($map, 'center', 'textareaMedium', 'Mittpunkt:');
		printTextarea($map, 'zoom', 'textareaXSmall', 'Zoom:');
		printUpdateSelect($map, array('mapgrid'=>array("f", "t")), 'miniSelect', 'Visa rutnät:');
		printUpdateSelect($map, array('enablerotation'=>array("f", "t")), 'miniSelect', 'Roterbar:');
		printUpdateSelect($map, array('embedded'=>array("f", "t")), 'miniSelect', 'Inbäddad:');
		printTextarea($map, 'resolutions', 'textareaMedium', 'Upplösningar:');
		printUpdateSelect($map, array('constrainresolution'=>array("f", "t")), 'miniSelect', 'Upplösningsbegränsad:');
		printUpdateSelect($map, array('tilegrid'=>$selectables['tilegrids']), 'bodySelect', 'Tilegrid:');
		printUpdateSelect($map, array('show_meta'=>array("f", "t")), 'miniSelect', 'Visa metadata:');
		printTextarea($map, 'abstract', 'textareaLarge', 'Beskrivning:');
		printTextarea($map, 'info', 'textareaLarge', 'Info:');
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
