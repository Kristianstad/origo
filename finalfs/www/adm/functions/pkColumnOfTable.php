<?php

	// Takes the name of an Origo configuration table as parameter and returns the name of the primary key- (id-) column
	function pkColumnOfTable($table)
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

?>
