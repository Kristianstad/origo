<?php

	function printMultiselectButton($table, $value=null, $buttonText='Flervalsverktyg', $buttonStyle='')
	{
		$buttonValue=$table;
		if (!empty($value))
		{
			$buttonValue=$buttonValue.":$value";
		}
		echo <<<HERE
			<button form="multiselectForm" onclick="toggleTopFrame('{$table}');" type="submit" name="table" value="{$buttonValue}" style="{$buttonStyle}">
				{$buttonText}
			</button>
		HERE;
	}

?>
