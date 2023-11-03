<?php

	require_once("./functions/all_from_table.php");

	function tableConfigs($table, $configTablesOrDbh)
	{
		if (is_resource($configTablesOrDbh))
		{
			require("./constants/configSchema.php");
			return all_from_table($configTablesOrDbh, $configSchema, $table);
		}
		elseif (is_array($configTablesOrDbh) && !empty($configTablesOrDbh[$table]))
		{
			return $configTablesOrDbh[$table];
		}
		else
		{
			exit(1);
		}
	}

?>
