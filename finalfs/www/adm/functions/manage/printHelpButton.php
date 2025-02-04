<?php

	function printHelpButton($type, $configParam=null, $buttonText='?', $buttonClass='smallHelpButton')
	{
		$buttonValue=$type;
		if (!empty($configParam))
		{
			$buttonValue=$buttonValue.":$configParam";
		}
		echo <<<HERE
			<button title="Visa hjälptext" form="helpForm" onclick="toggleTopFrame('{$buttonValue}');" type="submit" name="id" value="{$buttonValue}" class="{$buttonClass}">
				{$buttonText}
			</button>
		HERE;
	}

?>
