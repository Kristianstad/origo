<?php

	require_once("./functions/manage/isFullTarget.php");
	require_once("./functions/manage/printTextarea.php");
	require_once("./functions/manage/printHiddenInputs.php");
	require_once("./functions/manage/printUpdateButton.php");
	require_once("./functions/manage/printInfoButton.php");
	require_once("./functions/manage/printReadSchemaTablesButton.php");
	require_once("./functions/manage/printDeleteButton.php");

	// Takes a full schema target (array), schema selectables (array), inheritPosts (array), and helps (array).
	// Prints form fields and buttons that are used to view and edit the configuration for the given schema.
	function printSchemaForm($schema, $selectables, $inheritPosts, $helps=array())
	{
		if (!isFullTarget($schema))
		{
			die("printSchemaForm($schema, $selectables, $inheritPosts, $helps=array()) failed!");
		}
		echo '<div><div class="printXFormDiv"><form method="post">';
		printTextarea($schema, 'schema_id', 'textareaMedium', 'Id:', in_array('schema_id', $helps));
		printTextarea($schema, 'abstract', 'textareaLarge', 'Beskrivning:', in_array('abstract', $helps));
		printTextarea($schema, 'keywords', 'textareaLarge', 'Nyckelord:', in_array('keywords', $helps));
		printUpdateSelect($schema, array('contact'=>$selectables['contacts']), 'bodySelect', 'Kontakt:', in_array('contact', $helps));
		printUpdateSelect($schema, array('origin'=>$selectables['origins']), 'bodySelect', 'Ursprungskälla:', in_array('origin', $helps));
		printTextarea($schema, 'updated', 'textareaMedium', 'Uppdaterad (åååå-mm-dd):', in_array('updated', $helps));
		printUpdateSelect($schema, array('update'=>$selectables['updates']), 'bodySelect', 'Uppdatering:', in_array('update', $helps));
		printTextarea($schema, 'info', 'textareaLarge', 'Info:', in_array('info', $helps));
		printHiddenInputs($inheritPosts);
		echo '<div class="buttonDiv">';
		printUpdateButton('schema');
		$schema=makeTargetBasic($schema);
		printInfoButton($schema);
		printReadSchemaTablesButton($schema['schema']);
		$deleteConfirmStr="Är du säker att du vill radera all metadata för schemat ".$schema['schema']."? Metadata för ingående tabeller hanteras separat.";
		printDeleteButton($schema, $deleteConfirmStr, $inheritPosts);
		echo '</div></form></div></div>';
	}

?>
