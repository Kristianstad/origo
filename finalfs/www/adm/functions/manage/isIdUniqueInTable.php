<?php

	require_once("./functions/pkColumnOfTable.php");

	function isIdUniqueInTable($id, $table)
	{
		return !in_array($id, array_column($table, pkColumnOfTable($table)));
	}

?>
