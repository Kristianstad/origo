<?php

	// Takes a table name (string) and returns the type (string) of the items stored in the table
	function tableType($table)
	{
		return rtrim($table,'s');
	}

?>
