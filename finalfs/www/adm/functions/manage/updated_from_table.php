<?php

	function updated_from_table($dbh, $tableWithSchema)
	{
		$result=pg_query($dbh, "SELECT pg_xact_commit_timestamp(xmin) FROM $tableWithSchema ORDER BY 1 DESC NULLS LAST");
		if (!$result)
		{
			die("Error in SQL query: " . pg_last_error());
		}
		return pg_fetch_row($result);
	}

?>
