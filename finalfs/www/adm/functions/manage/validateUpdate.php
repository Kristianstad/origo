<?php

	require_once("./functions/pkColumnOfTable.php");
	require_once("./functions/toSwedish.php");

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
