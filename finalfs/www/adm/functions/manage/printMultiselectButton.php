<?php

	function printMultiselectButton($configParam, $value=null, $buttonText='Flervalsverktyg', $buttonClass='largeMultiselectButton')
	{
		$buttonValue=$configParam;
		if (!empty($value))
		{
			$buttonValue=$buttonValue.":$value";
		}
		echo <<<HERE
			<button form="multiselectForm" onclick="toggleTopFrame('{$buttonValue}');" type="submit" name="table" value="{$buttonValue}" class="{$buttonClass}">
				{$buttonText}
			</button>
		HERE;
	}

?>
