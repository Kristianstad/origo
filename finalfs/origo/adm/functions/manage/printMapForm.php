<?php

	require_once("./functions/manage/isFullTarget.php");
	require_once("./functions/manage/printTextarea.php");
	require_once("./functions/manage/printUpdateSelect.php");
	require_once("./functions/manage/printHiddenInputs.php");
	require_once("./functions/manage/printUpdateButton.php");
	require_once("./functions/manage/printInfoButton.php");
	require_once("./functions/manage/printWriteConfigButton.php");
	require_once("./functions/manage/printExportJsonButton.php");
	require_once("./functions/manage/printDeleteButton.php");

	function printMapForm($map, $selectables, $inheritPosts, $helps=array())
	{
		if (!isFullTarget($map))
		{
			die("printMapForm($map, $selectables, $inheritPosts, $helps=array()) failed!");
		}
		echo '<div><div class="printXFormDiv"><form method="post">';
		printTextarea($map, 'map_id', 'textareaMedium', 'Id:', in_array('map_id', $helps));
		printTextarea($map, 'title', 'textareaMedium', 'Titel:', in_array('title', $helps));
		printTextarea($map, 'url', 'textareaLarge', 'Url:', in_array('url', $helps));
		printTextarea($map, 'layers', 'textareaLarge', 'Lager:', in_array('layers', $helps));
		printTextarea($map, 'groups', 'textareaLarge', 'Grupper:', in_array('groups', $helps));
		printTextarea($map, 'controls', 'textareaLarge', 'Kontroller:', in_array('controls', $helps));
		printTextarea($map, 'proj4defs', 'textareaMedium', 'Proj4defs:', in_array('proj4defs', $helps));
		printUpdateSelect($map, array('footer'=>$selectables['footers']), 'bodySelect', 'Sidfot:', in_array('footer', $helps));
		printTextarea($map, 'featureinfooptions', 'textareaMedium', 'FeatureInfoOptions:', in_array('featureinfooptions', $helps));
		printTextarea($map, 'projectioncode', 'textareaMedium', 'Projektion:', in_array('projectioncode', $helps));
		printTextarea($map, 'projectionextent', 'textareaMedium', 'Projektionsutbredning:', in_array('projectionextent', $helps));
		printTextarea($map, 'extent', 'textareaMedium', 'Utbredning:', in_array('extent', $helps));
		printTextarea($map, 'center', 'textareaMedium', 'Mittpunkt:', in_array('center', $helps));
		printTextarea($map, 'zoom', 'textareaXSmall', 'Zoom:', in_array('zoom', $helps));
		printUpdateSelect($map, array('mapgrid'=>array("f", "t")), 'miniSelect', 'Visa rutnät:', in_array('mapgrid', $helps));
		printUpdateSelect($map, array('enablerotation'=>array("f", "t")), 'miniSelect', 'Roterbar:', in_array('enablerotation', $helps));
		printUpdateSelect($map, array('embedded'=>array("f", "t")), 'miniSelect', 'Inbäddad:', in_array('embedded', $helps));
		printTextarea($map, 'resolutions', 'textareaMedium', 'Upplösningar:', in_array('resolutions', $helps));
		printUpdateSelect($map, array('constrainresolution'=>array("f", "t")), 'miniSelect', 'Upplösningsbegränsad:', in_array('constrainresolution', $helps));
		printUpdateSelect($map, array('tilegrid'=>$selectables['tilegrids']), 'bodySelect', 'Tilegrid:', in_array('tilegrid', $helps));
		printUpdateSelect($map, array('show_meta'=>array("f", "t")), 'miniSelect', 'Visa metadata:', in_array('show_meta', $helps));
		printTextarea($map, 'icon', 'textareaMedium', 'ikon:', in_array('icon', $helps));
		printTextarea($map, 'css_files', 'textareaLarge', 'CSS-filer:', in_array('css_files', $helps));
		printTextarea($map, 'css', 'textareaLarge', 'CSS:', in_array('css', $helps));
		printTextarea($map, 'js_files', 'textareaLarge', 'JS-filer:', in_array('js_files', $helps));
		printTextarea($map, 'js', 'textareaLarge', 'JS:', in_array('js', $helps));
		printTextarea($map, 'abstract', 'textareaLarge', 'Beskrivning:', in_array('abstract', $helps));
		printTextarea($map, 'keywords', 'textareaLarge', 'Nyckelord:', in_array('keywords', $helps));
		printTextarea($map, 'info', 'textareaLarge', 'Info:', in_array('info', $helps));
		printHiddenInputs($inheritPosts);
		echo '<div class="buttonDiv">';
		printUpdateButton('map');
		$url=$map['map']['url'];
		$map['map']=$map['map']['map_id'];
		printInfoButton($map);
		$deleteConfirmStr="Är du säker på att du vill radera kartan ".$map['map']."? Ingående kontroller, grupper och lager påverkas ej.";
		printDeleteButton($map, $deleteConfirmStr, $inheritPosts);
		printConfigPreviewButton($map['map']);
		printWriteConfigButton($map['map']);
		printExportJsonButton($map['map']);
		if (!empty($url))
		{
			printUrlButton($url);
		}
		echo '</div></form></div></div>';
	}

?>
