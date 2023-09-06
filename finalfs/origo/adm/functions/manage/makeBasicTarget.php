<?php

	function makeBasicTarget($type, $id)
	{
		if (is_string($type) && !empty($type) && is_string($id) && !empty($id))
		{
			return array($type=>$id);
		}
		else
		{
			die("makeBasicTarget($type, $id) failed!");
		}
	}

?>
