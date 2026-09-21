<?php

	function isFullTarget($target)
	{
		return (isTarget($target) && is_array(current($target)));
	}