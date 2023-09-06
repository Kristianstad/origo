<?php

	require_once("./functions/manage/isTarget.php");

	function isFullTarget($target)
	{
		return (isTarget($target) && is_array(current($target)));
	}

?>
