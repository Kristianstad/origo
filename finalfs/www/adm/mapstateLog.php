<?php
	// Tell browsers to not cache response
	header("Cache-Control: must-revalidate, max-age=0, s-maxage=0, no-cache, no-store");
	
	// Expose specific functions
	require_once("./functions/includeDirectory.php");
	
	// Expose all functions in given folders
	includeDirectory("./functions/common");

	if (!empty($_POST['mapStateId']))
	{
		$mapstateId=$_POST['mapStateId'];
		$dbh=dbh();
		require("./constants/configSchema.php");
		$mapstates=all_from_table($dbh, $configSchema, 'mapstates');
		if (isIdUniqueInTable($mapstateId, 'mapstate_id', $mapstates))
		{
			$sql=insertIdSql($mapstateId, 'mapstates').';';
		}
		else
		{
			$sql='';
		}
		$sql=$sql."UPDATE $configSchema.mapstates SET lastuse = now() WHERE mapstate_id = '$mapstateId';";
		$result=pg_query($dbh, $sql);
		if (!$result)
		{
			die("Error in SQL query: " . pg_last_error());
		}
		unset($result);
		echo "Use of mapstate $mapstateId was logged!";
	}
	else
	{
		echo "No mapstate to log!";
	}
?>
