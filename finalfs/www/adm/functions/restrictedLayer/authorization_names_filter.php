<?php

	function authorization_names_filter($layerNames)
	{
		return array_column(authorization_filter($layerNames), 'name');
	}

?>
