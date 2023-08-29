<?php

	require_once("./functions/pkColumnOfTable.php");
	require_once("./functions/manage/printMultiselectButton.php");

	function printTextarea($target, $column, $class, $label, $readonly='no')
	{
		require("./constants/multiselectables.php");
		$targetId=current($target)[pkColumnOfTable(key($target).'s')];
		$columnValue=current($target)[$column];
		if (preg_match('/^\{([^"\{\[]*("[^:])?)*\}$/', $columnValue))
		{ 
			$columnValue=trim($columnValue, '{}');
		}
		$ucColumn=ucfirst($column);
		if ($readonly == 'yes')
		{
			$readonly='readonly ';
		}
		else
		{
			$readonly='';
		}
		echo <<<HERE
			<span>
				<label for="{$targetId}{$ucColumn}">{$label}</label>
				<textarea {$readonly}rows="1" class="{$class}" id="{$targetId}{$ucColumn}" name="update{$ucColumn}">{$columnValue}</textarea>
		HERE;
		if (in_array($column, $multiselectables))
		{
			printMultiselectButton($column, trim(current($target)[$column], '{}'), '+', 'margin-left:-0.5em');
		}
		echo '</span><wbr>';
	}

?>
