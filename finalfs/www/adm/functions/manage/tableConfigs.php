<?php

	// Uses common functions: all_from_table

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
