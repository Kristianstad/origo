<?php

	function all_from_table($dbh, $schema, $table)
	{
		$tableWithSchema=$schema.'.'.$table;
		$result=pg_query($dbh, "SELECT * FROM $tableWithSchema ORDER BY 1");
		if (!$result)
		{
			die("Error in SQL query: " . pg_last_error());
		}
		return pg_fetch_all($result);
	}
	
?>
