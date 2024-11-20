<?php

	require_once("./functions/pkColumnOfTable.php");
	require_once("./functions/toSwedish.php");

	// Takes an updatePosts array and an configTables array as input parameters and passes back a third parameter as boolean.
	// The third parameter will be set to true if all updatePosts pass as valid
	function validateUpdate($updatePosts, $configTables, &$updateValid)
	{
		require("./constants/multiselectables.php");
		$updateValid=true;
		foreach ($multiselectables as $table)
		{
			if (!empty($updatePosts['update'.ucfirst($table)]))
			{
				$updateValues=explode(',', $updatePosts['update'.ucfirst($table)]);
				$unknownValues=array_diff($updateValues, array_column($configTables[$table], pkColumnOfTable($table)));
				if (!empty($unknownValues))
				{
					echo '<script>window.onload=function(){alert("Uppdatering misslyckades!\nOkända värden för '.toSwedish($table).': '.implode(', ', $unknownValues).'");}</script>';
					$updateValid=false;
					break;
				}
			}
		}
	}

?>
