<?php

	// Uses common functions: isTarget

	function isFullTarget($target)
	{
		return (isTarget($target) && is_array(current($target)));
	}

?>
