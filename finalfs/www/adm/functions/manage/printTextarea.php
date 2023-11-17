<?php

	require_once("./functions/manage/isFullTarget.php");
	require_once("./functions/manage/targetConfigParam.php");
	require_once("./functions/manage/targetType.php");
	require_once("./functions/manage/targetId.php");
	require_once("./functions/manage/printMultiselectButton.php");
	require_once("./functions/manage/printHelpButton.php");

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
			<span>
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
