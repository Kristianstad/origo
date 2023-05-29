<?php

	require_once("./functions/manage/printTextarea.php");
	require_once("./functions/manage/printSourceList.php");
	require_once("./functions/manage/printUpdateSelect.php");
	require_once("./functions/manage/printHiddenInputs.php");
	require_once("./functions/manage/printUpdateButton.php");
	require_once("./functions/manage/printInfoButton.php");
	require_once("./functions/manage/printDeleteButton.php");
	require_once("./functions/manage/printAddOperation.php");
	require_once("./functions/manage/printRemoveOperation.php");

	function printLayerForm($layer, $selectables, $operationTables, $sources, $inheritPosts)
	{
		echo '<div><div class="printXFormDiv"><form method="post">';
		printTextarea($layer, 'layer_id', 'textareaMedium', 'Id:');
		printTextarea($layer, 'title', 'textareaMedium', 'Titel:');
		printSourceList($layer, $sources);
		printUpdateSelect($layer, array('type'=>array("WMS", "WFS", "OSM", "GEOJSON", "GROUP", "WMTS")), 'miniSelect', 'Typ:');
		if (isset($layer['layer']['type']))
		{
			if ($layer['layer']['type'] == 'WFS')
			{
				printUpdateSelect($layer, array('editable'=>array("f", "t")), 'miniSelect', 'Redigerbar:');
				if (current($layer)['editable'] == "t")
				{
					printTextarea($layer, 'allowededitoperations', 'textareaMedium', 'Redigeringsalt.:');
					printTextarea($layer, 'geometryname', 'textareaMedium', 'Geometrinamn:');
					printTextarea($layer, 'geometrytype', 'textareaMedium', 'Geometrityp:');
				}
			}
			elseif ($layer['layer']['type'] == 'WMS')
			{
				printUpdateSelect($layer, array('tiled'=>array("f", "t")), 'miniSelect', 'Tiled:');
			}
		}
		printUpdateSelect($layer, array('queryable'=>array("f", "t")), 'miniSelect', 'Klickbar:');
		printUpdateSelect($layer, array('visible'=>array("f", "t")), 'miniSelect', 'Synlig:');
		printTextarea($layer, 'opacity', 'textareaSmall', 'Opacitet:');
		if (isset($layer['layer']['service']) && $layer['layer']['service'] == 'restricted')
		{
			echo "<span><img class='yellowLock' src='../img/png/lock_yellow.png' alt='Skyddat lager' title='Skyddat lager'>";
			printTextarea($layer, 'adusers', 'textareaLarge', 'Användare:');
			echo "</span><wbr><span><img class='yellowLock' src='../img/png/lock_yellow.png' alt='Skyddat lager' title='Skyddat lager'>";
			printTextarea($layer, 'adgroups', 'textareaLarge', 'Grupper:');
			echo "</span><wbr>";
		}
		printTextarea($layer, 'icon', 'textareaLarge', 'Ikon:');
		printTextarea($layer, 'icon_extended', 'textareaLarge', 'Utfälld ikon:');
		printUpdateSelect($layer, array('swiper'=>array("f", "t", "under")), 'miniSelect', 'Swiper-lager:');
		if (isset($layer['layer']['type']) && $layer['layer']['type'] == 'WMS')
		{
			printTextarea($layer, 'format', 'textareaMedium', 'Format:');
			printTextarea($layer, 'featureinfolayer', 'textareaMedium', 'FeatureInfo-lager:');
		}
		printTextarea($layer, 'attributes', 'textareaLarge', 'Attribut:');
		printTextarea($layer, 'style_config', 'textareaLarge', 'Stilkonfiguration:');
		printTextarea($layer, 'style_filter', 'textareaLarge', 'Stilfilter:');
		printTextarea($layer, 'style_layer', 'textareaMedium', 'Stillager:');
		printTextarea($layer, 'maxscale', 'textareaSmall', 'Maxskala:');
		printTextarea($layer, 'minscale', 'textareaSmall', 'Minskala:');
		printTextarea($layer, 'exports', 'textareaMedium', 'Exportlager:');
		printTextarea($layer, 'attribution', 'textareaLarge', 'Tillskrivning:');
		echo '<hr class="dashedHr">';
		printUpdateSelect($layer, array('show_meta'=>array("f", "t")), 'miniSelect', 'Visa metadata:');
		printTextarea($layer, 'abstract', 'textareaLarge', 'Beskrivning:');
		printTextarea($layer, 'resources', 'textareaMedium', 'Resurser:');
		printUpdateSelect($layer, array('contact'=>$selectables['contacts']), 'bodySelect', 'Kontakt:');
		printUpdateSelect($layer, array('origin'=>$selectables['origins']), 'bodySelect', 'Ursprungskälla:');
		printTextarea($layer, 'updated', 'textareaMedium', 'Uppdaterad (åååå-mm-dd):');
		printUpdateSelect($layer, array('update'=>$selectables['updates']), 'bodySelect', 'Uppdatering:');
		printTextarea($layer, 'web', 'textareaMedium', 'Webbsida:');
		printTextarea($layer, 'history', 'textareaLarge', 'Tillkomsthistorik:');
		if (isset(current($layer)['tables']) && !empty(trim(current($layer)['tables'], '{}')))
		{
			printTextarea($layer, 'tables', 'textareaMedium', 'Tabeller:', 'yes');
		}
		echo '<hr class="dashedHr">';
		printTextarea($layer, 'categories', 'textareaLarge', 'Kategorier:');
		printTextarea($layer, 'info', 'textareaLarge', 'Info:');
		if (isset($layer['layer']['type']))
		{
			if ($layer['layer']['type'] == 'GROUP')
			{
				printTextarea($layer, 'layers', 'textareaMedium', 'Lager:');
			}
			elseif ($layer['layer']['type'] == 'WFS')
			{
				printTextarea($layer, 'layertype', 'textareaMedium', 'WFS-typ:');
				if (isset($layer['layer']['layertype']) && $layer['layer']['layertype'] == 'cluster')
				{
					printTextarea($layer, 'clusterstyle', 'textareaLarge', 'Klusterstil:');
					printTextarea($layer, 'clusteroptions', 'textareaLarge', 'Klusteralternativ:');
				}
			}
		}
		printHiddenInputs($inheritPosts);
		echo '<hr class="dashedHr">';
		echo '<div class="buttonDiv">';
		printUpdateButton('layer');
		$layer['layer']=$layer['layer']['layer_id'];
		printInfoButton($layer);
		echo '</div></form></div>';
		$deleteConfirmStr="Är du säker att du vill radera lagret ".$layer['layer']."? Referenser till lagret hanteras separat.";
		printDeleteButton($layer, $deleteConfirmStr, $inheritPosts);
		echo '</div><div class="addRemoveDiv">';
		printAddOperation($layer, array('maps'=>array_column($operationTables['maps'], 'map_id')), 'Lägg till i karta', $inheritPosts);
		printRemoveOperation($layer, array('maps'=>$operationTables['maps']), 'Ta bort från karta', $inheritPosts);
		printAddOperation($layer, array('groups'=>array_column($operationTables['groups'], 'group_id')), 'Lägg till i grupp', $inheritPosts);
		printRemoveOperation($layer, array('groups'=>$operationTables['groups']),'Ta bort från grupp', $inheritPosts);
		echo '</div>';
	}
	
?>

