<?php

	require_once("./functions/manage/isFullTarget.php");
	require_once("./functions/manage/printTextarea.php");
	require_once("./functions/manage/printSourceList.php");
	require_once("./functions/manage/printUpdateSelect.php");
	require_once("./functions/manage/printHiddenInputs.php");
	require_once("./functions/manage/printUpdateButton.php");
	require_once("./functions/manage/printInfoButton.php");
	require_once("./functions/manage/printDeleteButton.php");
	require_once("./functions/manage/printAddOperation.php");
	require_once("./functions/manage/printRemoveOperation.php");

	function printLayerForm($layer, $selectables, $operationTables, $sources, $inheritPosts, $helps=array())
	{
		if (!isFullTarget($layer))
		{
			die("printLayerForm($layer, $selectables, $operationTables, $sources, $inheritPosts, $helps=array()) failed!");
		}
		echo '<div><div class="printXFormDiv"><form method="post">';
		printTextarea($layer, 'layer_id', 'textareaMedium', 'Id:', in_array('layer_id', $helps));
		printTextarea($layer, 'title', 'textareaMedium', 'Titel:', in_array('title', $helps));
		printSourceList($layer, $sources, in_array('source', $helps));
		printUpdateSelect($layer, array('type'=>array("WMS", "WFS", "OSM", "GEOJSON", "GROUP", "WMTS")), 'miniSelect', 'Typ:', in_array('type', $helps));
		if (isset($layer['layer']['type']))
		{
			if ($layer['layer']['type'] == 'WFS')
			{
				printUpdateSelect($layer, array('layertype'=>array("vector", "cluster", "image")), 'miniSelect', 'WFS-typ:', in_array('layertype', $helps));
				if (isset($layer['layer']['layertype']) && $layer['layer']['layertype'] == 'cluster')
				{
					printTextarea($layer, 'clusterstyle', 'textareaLarge', 'Klusterstil:', in_array('clusterstyle', $helps));
					printTextarea($layer, 'clusteroptions', 'textareaLarge', 'Klusteralternativ:', in_array('clusteroptions', $helps));
				}
				printUpdateSelect($layer, array('editable'=>array("f", "t")), 'miniSelect', 'Redigerbar:', in_array('editable', $helps));
				if (current($layer)['editable'] == "t")
				{
					printTextarea($layer, 'allowededitoperations', 'textareaMedium', 'Redigeringsalt.:', in_array('allowededitoperations', $helps));
					printTextarea($layer, 'geometryname', 'textareaMedium', 'Geometrinamn:', in_array('geometryname', $helps));
					printTextarea($layer, 'geometrytype', 'textareaMedium', 'Geometrityp:', in_array('geometrytype', $helps));
				}
			}
			elseif ($layer['layer']['type'] == 'WMS')
			{
				printUpdateSelect($layer, array('tiled'=>array("f", "t")), 'miniSelect', 'Tiled:', in_array('tiled', $helps));
			}
			elseif ($layer['layer']['type'] == 'GROUP')
			{
				printTextarea($layer, 'layers', 'textareaMedium', 'Lager:', in_array('layers', $helps));
			}
		}
		printUpdateSelect($layer, array('queryable'=>array("f", "t")), 'miniSelect', 'Klickbar:', in_array('queryable', $helps));
		printUpdateSelect($layer, array('visible'=>array("f", "t")), 'miniSelect', 'Synlig:', in_array('visible', $helps));
		printTextarea($layer, 'opacity', 'textareaSmall', 'Opacitet:', in_array('opacity', $helps));
		if (isset($layer['layer']['service']) && $layer['layer']['service'] == 'restricted')
		{
			echo "<span><img class='yellowLock' src='../img/png/lock_yellow.png' alt='Skyddat lager' title='Skyddat lager'>";
			printTextarea($layer, 'adusers', 'textareaLarge', 'Användare:', in_array('adusers', $helps));
			echo "</span><wbr><span><img class='yellowLock' src='../img/png/lock_yellow.png' alt='Skyddat lager' title='Skyddat lager'>";
			printTextarea($layer, 'adgroups', 'textareaLarge', 'Grupper:', in_array('adgroups', $helps));
			echo "</span><wbr>";
		}
		printUpdateSelect($layer, array('swiper'=>array("f", "t", "under")), 'miniSelect', 'Swiper-lager:', in_array('swiper', $helps));
		if (isset($layer['layer']['type']) && $layer['layer']['type'] == 'WMS')
		{
			printTextarea($layer, 'format', 'textareaMedium', 'Format:', in_array('format', $helps));
			printTextarea($layer, 'featureinfolayer', 'textareaMedium', 'FeatureInfo-lager:', in_array('featureinfolayer', $helps));
		}
		printTextarea($layer, 'attributes', 'textareaLarge', 'Attribut:', in_array('attributes', $helps));
		printTextarea($layer, 'style_layer', 'textareaMedium', 'Stillager:', in_array('style_layer', $helps));
		if (!isset($layer['layer']['style_layer']) || empty(trim($layer['layer']['style_layer'])))
		{
			printTextarea($layer, 'style_config', 'textareaLarge', 'Stilkonfiguration:', in_array('style_config', $helps));
			if (!isset($layer['layer']['style_config']) || empty(trim($layer['layer']['style_config'], " []{}\n\r\t")) || $layer['layer']['style_config'] == 'null')
			{
				printTextarea($layer, 'style_filter', 'textareaLarge', 'Stilfilter:', in_array('style_filter', $helps));
				printUpdateSelect($layer, array('show_icon'=>array("f", "t")), 'miniSelect', 'Visa ikon:', in_array('show_icon', $helps));
				if (isset($layer['layer']['show_icon']) && $layer['layer']['show_icon'] == 't')
				{
					printTextarea($layer, 'icon', 'textareaLarge', 'Ikon:', in_array('icon', $helps));
				}
				else
				{
					printHiddenInputs(array(
						'updateIcon' => $layer['layer']['icon']
					));
				}
				printTextarea($layer, 'icon_extended', 'textareaLarge', 'Utfälld ikon:', in_array('icon_extended', $helps));
			}
			else
			{
				printHiddenInputs(array(
					'updateIcon' => $layer['layer']['icon'],
					'updateIcon_extended' => $layer['layer']['icon_extended'],
					'updateStyle_filter' => $layer['layer']['style_filter'],
					'updateShow_icon' => $layer['layer']['show_icon']
				));
			}
		}
		else
		{
			printHiddenInputs(array(
				'updateStyle_config' => $layer['layer']['style_config'],
				'updateIcon' => $layer['layer']['icon'],
				'updateIcon_extended' => $layer['layer']['icon_extended'],
				'updateStyle_filter' => $layer['layer']['style_filter'],
				'updateShow_icon' => $layer['layer']['show_icon']
			));
		}
		printTextarea($layer, 'maxscale', 'textareaSmall', 'Maxskala:', in_array('maxscale', $helps));
		printTextarea($layer, 'minscale', 'textareaSmall', 'Minskala:', in_array('minscale', $helps));
		printTextarea($layer, 'exports', 'textareaMedium', 'Exportlager:', in_array('exports', $helps));
		printTextarea($layer, 'attribution', 'textareaLarge', 'Tillskrivning:', in_array('attribution', $helps));
		echo '<hr class="dashedHr">';
		printUpdateSelect($layer, array('show_meta'=>array("f", "t")), 'miniSelect', 'Visa metadata:', in_array('show_meta', $helps));
		printTextarea($layer, 'abstract', 'textareaLarge', 'Beskrivning:', in_array('abstract', $helps));
		printTextarea($layer, 'keywords', 'textareaLarge', 'Nyckelord:', in_array('keywords', $helps));
		printTextarea($layer, 'resources', 'textareaMedium', 'Resurser:', in_array('resources', $helps));
		printUpdateSelect($layer, array('contact'=>$selectables['contacts']), 'bodySelect', 'Kontakt:', in_array('contact', $helps));
		printUpdateSelect($layer, array('origin'=>$selectables['origins']), 'bodySelect', 'Ursprungskälla:', in_array('origin', $helps));
		printTextarea($layer, 'updated', 'textareaMedium', 'Uppdaterad (åååå-mm-dd):', in_array('updated', $helps));
		printUpdateSelect($layer, array('update'=>$selectables['updates']), 'bodySelect', 'Uppdatering:', in_array('update', $helps));
		printTextarea($layer, 'web', 'textareaMedium', 'Webbsida:', in_array('web', $helps));
		printTextarea($layer, 'history', 'textareaLarge', 'Tillkomsthistorik:', in_array('history', $helps));
		if (isset(current($layer)['tables']) && !empty(trim(current($layer)['tables'], '{}')))
		{
			printTextarea($layer, 'tables', 'textareaMedium', 'Tabeller:', in_array('tables', $helps), true);
		}
		echo '<hr class="dashedHr">';
		printTextarea($layer, 'info', 'textareaLarge', 'Info:', in_array('info', $helps));
		printHiddenInputs($inheritPosts);
		echo '<hr class="dashedHr">';
		echo '<div class="buttonDiv">';
		printUpdateButton('layer');
		$layer['layer']=$layer['layer']['layer_id'];
		printInfoButton($layer);
		printConfigPreviewButton('preview', null, $layer['layer']);
		$deleteConfirmStr="Är du säker att du vill radera lagret ".$layer['layer']."? Referenser till lagret hanteras separat.";
		printDeleteButton($layer, $deleteConfirmStr, $inheritPosts);
		echo '</div></form></div></div><div class="addRemoveDiv">';
		printAddOperation($layer, array('maps'=>array_column($operationTables['maps'], 'map_id')), 'Lägg till i karta', $inheritPosts);
		printRemoveOperation($layer, array('maps'=>$operationTables['maps']), 'Ta bort från karta', $inheritPosts);
		printAddOperation($layer, array('groups'=>array_column($operationTables['groups'], 'group_id')), 'Lägg till i grupp', $inheritPosts);
		printRemoveOperation($layer, array('groups'=>$operationTables['groups']),'Ta bort från grupp', $inheritPosts);
		echo '</div>';
	}

?>
