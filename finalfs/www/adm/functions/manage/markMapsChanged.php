<?php

	// Takes a database handle and an array of map-ids. Sets column "changed" to "t" in the database for the given maps.
	function markMapsChanged(&$dbh, $mapIds)
	{
		require("./constants/configSchema.php");
		$sql='';
		foreach ($mapIds as $mapId)
		{
			$sql=$sql."UPDATE $configSchema.maps SET changed = 't' WHERE map_id = '$mapId'; ";
		}
		$result=pg_query($dbh, $sql);
		if (!$result)
		{
			die("Error in SQL query: " . pg_last_error());
		}
	}

?>
