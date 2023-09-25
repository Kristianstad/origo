<?php

	require_once("./functions/manage/isFullTarget.php");
	require_once("./functions/manage/targetId.php");
	require_once("./functions/manage/printSelectOptions.php");
	require_once("./functions/manage/targetType.php");

	function printUpdateSelect($fullTarget, $configParamValues, $class, $label, $help=false, $options=null)
	{
		if (!isFullTarget($fullTarget))
		{
			die("printUpdateSelect($fullTarget, $configParamValues, $class, $label, $options=null) failed!");
		}
		$targetId=targetId($fullTarget);
		$targetType=targetType($fullTarget);
		$configParam=key($configParamValues);
		$ucConfigParam=ucfirst($configParam);
		$sName='update'.$ucConfigParam;
		$selected=current($fullTarget)[rtrim(key($configParamValues), 's')];
		echo <<<HERE
			<span>
				<label title="{$targetType}:{$configParam}" for='{$targetId}{$ucConfigParam}'>{$label}</label>
				<select class='{$class}' id='{$targetId}{$ucConfigParam}' name='{$sName}'>
		HERE;
		if (!isset($options))
		{
				$options=array_merge(array(""), current($configParamValues));
		}
		printSelectOptions($options, $selected);
		echo '</select>';
		if ($help)
		{
			printHelpButton($targetType, $configParam);
		}
		echo '</span><wbr>';
	}

?>
