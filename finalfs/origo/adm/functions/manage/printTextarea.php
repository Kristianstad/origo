<?php

	require_once("./functions/pkColumnOfTable.php");

	function printTextarea($target, $column, $class, $label, $readonly='no')
	{
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
			<label for="{$targetId}{$ucColumn}">{$label}</label>
			<textarea {$readonly}rows="1" class="{$class}" id="{$targetId}{$ucColumn}" name="update{$ucColumn}">{$columnValue}</textarea>&nbsp;
		HERE;
	}
	
?>
