<?php

	require_once("./functions/manage/printTextarea.php");
	require_once("./functions/manage/printHiddenInputs.php");
	require_once("./functions/manage/printUpdateButton.php");
	require_once("./functions/manage/printInfoButton.php");
	require_once("./functions/manage/printReadSchemaTablesButton.php");
	require_once("./functions/manage/printDeleteButton.php");

	function printSchemaForm($schema, $selectables, $inheritPosts)
	{
		echo '<div><div class="printXFormDiv"><form method="post">';
		printTextarea($schema, 'schema_id', 'textareaMedium', 'Id:');
		printTextarea($schema, 'abstract', 'textareaLarge', 'Beskrivning:');
		printTextarea($schema, 'keywords', 'textareaLarge', 'Nyckelord:');
		printUpdateSelect($schema, array('contact'=>$selectables['contacts']), 'bodySelect', 'Kontakt:');
		printUpdateSelect($schema, array('origin'=>$selectables['origins']), 'bodySelect', 'Ursprungskälla:');
		printTextarea($schema, 'updated', 'textareaMedium', 'Uppdaterad (åååå-mm-dd):');
		printUpdateSelect($schema, array('update'=>$selectables['updates']), 'bodySelect', 'Uppdatering:');
		printTextarea($schema, 'info', 'textareaLarge', 'Info:');
		printHiddenInputs($inheritPosts);
		echo '<div class="buttonDiv">';
		printUpdateButton('schema');
		$schema['schema']=$schema['schema']['schema_id'];
		printInfoButton($schema);
		printReadSchemaTablesButton($schema['schema']);
		echo '</div></form></div>';
		$deleteConfirmStr="Är du säker att du vill radera all metadata för schemat ".$schema['schema']."? Metadata för ingående tabeller hanteras separat.";
		printDeleteButton($schema, $deleteConfirmStr, $inheritPosts);
		echo '</div>';
	}

?>
