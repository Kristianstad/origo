<?php

	// Uses manage functions: isFullTarget, sizePosts, printTextarea, printUpdateSelect, printHiddenInputs, printUpdateButton, printInfoButton, 
	// printDeleteButton, targetId

	// Takes a full searchtable target (array), searchtable selectables (array), inheritPosts (array), and helps (array).
	// Prints form fields and buttons that are used to view and edit the configuration for the given searchtable.
	function printSearchtableForm($searchtable, $selectables, $inheritPosts, $helps=array())
	{
		if (!isFullTarget($searchtable))
		{
			die("printSearchtableForm($searchtable, $selectables, $inheritPosts, $helps=array()) failed!");
		}
		$sizePosts=sizePosts($inheritPosts);
		echo '<div><div class="printXFormDiv"><form method="post">';
		printTextarea($searchtable, 'searchtable_id', 'textareaMedium', 'Id:', in_array('searchtable_id', $helps), $sizePosts);
		printTextarea($searchtable, 'title', 'textareaMedium', 'Titel:', in_array('title', $helps), $sizePosts);
		printTextarea($searchtable, 'mode', 'textareaMedium', 'Typ:', in_array('mode', $helps), $sizePosts);
		printTextarea($searchtable, 'ttl', 'textareaSmall', 'Ttl:', in_array('ttl', $helps), $sizePosts);
		printTextarea($searchtable, 'limit', 'textareaSmall', 'Limit:', in_array('limit', $helps), $sizePosts);
		printUpdateSelect($searchtable, array('usecentroid'=>array("f", "t")), 'miniSelect', 'Använd centroid:', in_array('usecentroid', $helps));
		printUpdateSelect($searchtable, array('database'=>$selectables['databases']), 'bodySelect', 'Databaser:', in_array('database', $helps));
		printTextarea($searchtable, 'schema', 'textareaMedium', 'Schema:', in_array('schema', $helps), $sizePosts);
		printTextarea($searchtable, 'table', 'textareaMedium', 'Tabell:', in_array('table', $helps), $sizePosts);
		printTextarea($searchtable, 'searchfield', 'textareaMedium', 'Sökfält:', in_array('searchfield', $helps), $sizePosts);
		printTextarea($searchtable, 'geometryfield', 'textareaMedium', 'Geometrifält:', in_array('geometryfield', $helps), $sizePosts);
		printTextarea($searchtable, 'gidfield', 'textareaMedium', 'Gidfält:', in_array('gidfield', $helps), $sizePosts);
		printTextarea($searchtable, 'abstract', 'textareaLarge', 'Beskrivning:', in_array('abstract', $helps), $sizePosts);
		printTextarea($searchtable, 'info', 'textareaLarge', 'Info:', in_array('info', $helps), $sizePosts);
		printHiddenInputs($inheritPosts);
		echo '<hr class="dashedHr">';
		echo '<div class="buttonDiv">';
		printUpdateButton('searchtable');
		printCopyButton('searchtable');
		$id=targetId($searchtable);
		$searchtable=makeTargetBasic($searchtable);
		printInfoButton($searchtable);
		$deleteConfirmStr="Är du säker på att du vill radera söktabellen $id? Berörda sökmodeller behöver hanteras separat.";
		printDeleteButton($searchtable, $deleteConfirmStr, $inheritPosts);
		echo '</div></form></div></div>';
	}