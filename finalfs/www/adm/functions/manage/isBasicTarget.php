<?php

	require_once("./functions/manage/isTarget.php");

	function isBasicTarget($target)
	{
		return (isTarget($target) && is_string(current($target)));
	}

?>
