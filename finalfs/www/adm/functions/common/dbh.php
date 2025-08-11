<?php

	// Takes a pg_connect connection string or, if none is given, reads constant from dbhConnectionString.php. Returns database handle.
	function dbh($dbhConnectionString=null)
	{
		if (!isset($dbhConnectionString))
		{
			require_once("./constants/dbhConnectionString.php");
		}
		$dbh = pg_connect($dbhConnectionString);
		if (!$dbh)
		{
			echo '{"save_status":"Error in connection"}';
			die();
		}
		else
		{
			return $dbh;
		}
 	 }
 	 
?>
