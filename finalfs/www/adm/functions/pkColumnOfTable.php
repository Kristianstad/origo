<?php

	function pkColumnOfTable($table)
	{
		if (is_array($table))
		{
			return array_key_first($table[0]);
		}
		else
		{
			if ($table == 'proj4defs')
			{
				return 'code';
			}
			else
			{
				return rtrim($table, 's').'_id';
			}
		}
	}

?>
