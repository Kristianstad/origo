<?php

	// Uses manage functions: isFullTarget, sizePosts, printTextarea, printHiddenInputs, printUpdateButton, printInfoButton, printDeleteButton, printAddOperation, 
	// printRemoveOperation, targetId

	// Takes a full plugin target (array), maps (array), inheritPosts (array), and helps (array).
	// Prints form fields and buttons that are used to view and edit the configuration for the given plugin.
	function printPluginForm($plugin, $maps, $inheritPosts, $helps=array())
	{
		if (!isFullTarget($plugin))
		{
			die("printPluginForm($plugin, $maps, $inheritPosts, $helps=array()) failed!");
		}
		$sizePosts=sizePosts($inheritPosts);
		echo '<div><div class="printXFormDiv"><form method="post">';
		printTextarea($plugin, 'plugin_id', 'textareaMedium', 'Id:', in_array('plugin_id', $helps), $sizePosts);
		printTextarea($plugin, 'abstract', 'textareaLarge', 'Beskrivning:', in_array('abstract', $helps), $sizePosts);
		printTextarea($plugin, 'css_files', 'textareaLarge', 'CSS-filer:', in_array('css_files', $helps), $sizePosts);
		printTextarea($plugin, 'css', 'textareaLarge', 'CSS:', in_array('css', $helps), $sizePosts);
		printTextarea($plugin, 'js_files', 'textareaLarge', 'JS-filer:', in_array('js_files', $helps), $sizePosts);
		printTextarea($plugin, 'js', 'textareaLarge', 'JS:', in_array('js', $helps), $sizePosts);
		printTextarea($plugin, 'onload', 'textareaLarge', 'Origo.on(load)-JS:', in_array('onload', $helps), $sizePosts);
		printTextarea($plugin, 'info', 'textareaLarge', 'Info:', in_array('info', $helps), $sizePosts);
		printHiddenInputs($inheritPosts);
		echo '<hr class="dashedHr">';
		echo '<div class="buttonDiv">';
		printUpdateButton('plugin');
		printCopyButton('plugin');
		$plugin=makeTargetBasic($plugin);
		printInfoButton($plugin);
		$deleteConfirmStr="Är du säker att du vill radera pluginen ".targetId($plugin)."? Referenser till pluginen hanteras separat.";
		printDeleteButton($plugin, $deleteConfirmStr, $inheritPosts);
		echo '</div></form></div></div><div class="addRemoveDiv">';
		printAddOperation($plugin, array('maps'=>array_column($maps['maps'], 'map_id')), 'Lägg till i karta', $inheritPosts);
		printRemoveOperation($plugin, $maps, 'Ta bort från karta', $inheritPosts);
		echo '</div>';
	}

?>
