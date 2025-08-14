<?php

	// Takes a database handle and an map-id. Sets column "changed" to "f" in the database for the given map.
	function markMapUnchanged(&$dbh, $mapId)
	{
		require("./constants/configSchema.php");
		$sql="UPDATE $configSchema.maps SET changed = 'f' WHERE map_id = '$mapId'; ";
		$result=pg_query($dbh, $sql);
		if (!$result)
		{
			die("Error in SQL query: " . pg_last_error());
		}
	}

?>
