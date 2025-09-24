<?php

	function isTarget($target)
	{
		return (is_array($target) && !empty($target) && is_string(key($target)));
	}

?>
