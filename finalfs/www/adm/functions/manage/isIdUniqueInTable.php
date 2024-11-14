<?php

	function isIdUniqueInTable($id, $tablePkColumn, $table)
	{
		return !in_array($id, array_column($table, $tablePkColumn));
	}

?>
