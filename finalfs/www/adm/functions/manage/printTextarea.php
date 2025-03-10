<?php

	require_once("./functions/manage/isFullTarget.php");
	require_once("./functions/manage/targetConfigParam.php");
	require_once("./functions/manage/targetType.php");
	require_once("./functions/manage/targetId.php");
	require_once("./functions/manage/printMultiselectButton.php");
	require_once("./functions/manage/printHelpButton.php");

	// Takes a full target array, a config parameter name (string), a textarea css class name (string), a label (string), help available (boolean), is readonly (optional, boolean).
	// Prints a textarea containing the configuration parameter for the given target. Class name and label for the textarea are taken from the parameter three and four. 
	// The textarea is set to readonly if parameter six is set to true. A help button is printed if a help target exists, and a multiselect button is printed if the config
	// parameter name exists in the multiselectables.php constant.
	function printTextarea($fullTarget, $configParam, $class, $label, $help=false, $readonly=false)
	{
		if (!isFullTarget($fullTarget))
		{
			die("printTextarea($fullTarget, $configParam, $class, $label, $help=false, $readonly=false) failed!");
		}
		require("./constants/multiselectables.php");
		$configParamValue=targetConfigParam($fullTarget, $configParam);
		if (preg_match('/^\{(("[[:alnum:]åäöÅÄÖ=\-\+#_\:\.\/\?\&]+([[:space:]][[:alnum:]åäöÅÄÖ=\-\+#_\:\.\/\?\&]+)*"|[[:alnum:]åäöÅÄÖ=\-\+#_\:\.\/\?\&]*),?)*\}$/', $configParamValue))
		{
			$configParamValue=str_replace('"', '', trim($configParamValue, '{}'));
		}
		$configParamValue=str_replace('&center=', '&amp;center=', $configParamValue);
		$ucConfigParam=ucfirst($configParam);
		if ($readonly)
		{
			$ro='readonly ';
		}
		else
		{
			$ro='';
		}
		$targetId=targetId($fullTarget);
		$targetType=targetType($fullTarget);
		echo <<<HERE
			<span class="optionSpan">
				<label title="{$targetType}:{$configParam}" for="{$targetId}{$ucConfigParam}">{$label}</label>
				<textarea {$ro}rows="1" class="{$class}" id="{$targetId}{$ucConfigParam}" name="update{$ucConfigParam}">{$configParamValue}</textarea>
		HERE;
		if (in_array($configParam, $multiselectables))
		{
			printMultiselectButton($configParam, $configParamValue, '+', 'smallMultiselectButton');
		}
		if ($help)
		{
			printHelpButton($targetType, $configParam);
		}
		echo '</span><wbr>';
	}

?>
