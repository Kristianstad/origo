<?php

	// Uses manage functions: hasStringKeys

	function printSelectOptions($optionValues, $selectedValue=null)
	{
		$isAssociativeArray=hasStringKeys($optionValues);
		if ($isAssociativeArray)
		{
			asort($optionValues);
		}
		foreach ($optionValues as $value => $label)
		{
			if (!$isAssociativeArray)
			{
				$value=$label;
			}
			$selectOption="<option value='$value'";
			if (isset($selectedValue))
			{
				if ($value == $selectedValue)
				{
					$selectOption="$selectOption selected";
				}
			}
			$selectOption="$selectOption>".ltrim(substr($label, strrpos($label,',')), ',')."</option>";
			echo $selectOption;
		}
	}

?>
