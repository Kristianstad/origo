<?php

	require_once("./functions/manage/isFullTarget.php");
	require_once("./functions/manage/targetId.php");
	require_once("./functions/manage/targetConfigParam.php");
	require_once("./functions/manage/printSelectOptions.php");
	require_once("./functions/manage/printHelpButton.php");

	function printSourceList($layer, $sources, $help=false)
	{
		if (!isFullTarget($layer))
		{
			die("printSourceList($layer, $sources) failed!");
		}
		$layerId=targetId($layer);
		$layerSource=targetConfigParam($layer, 'source');
		echo <<<HERE
			<span>
				<label title="layer:source" for="{$layerId}Source">Källa:</label>
				<input type="text" list="sourcelist" class="bodySelect" id="{$layerId}Source" name="updateSource" value="{$layerSource}" onfocus="this.value='';" />
				<datalist id="sourcelist">
		HERE;
		printSelectOptions(array_merge(array(""), $sources), $layerSource);
		echo '</datalist>';
		if ($help)
		{
			printHelpButton('layer', 'source');
		}
		echo '</span><wbr>';
	}

?>
