<?php

	function selectOptions($tablename, $setSelected=false)
	{
		GLOBAL $maps, $controls, $groups, $layers, $sources, $services, $footers, $mapId, $controlId, $groupId, $layerId, $sourceId, $serviceId, $footerId;
		eval("\$table=\$$tablename;");
		$type=rtrim($tablename, 's');
		$column=$type."_id";
		if ($setSelected)
		{
			eval("\$id=\$$type".'Id;');
			printSelectOptions(array_column($table, $column), $id);
		}
		else
		{
			printSelectOptions(array_column($table, $column));
		}
	}

?>