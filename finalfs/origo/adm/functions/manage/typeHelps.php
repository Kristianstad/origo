<?php

	function typeHelps($type, $helps)
	{
		$typeHelps=array();
		foreach ($helps as $helpId)
		{
			$idParts=explode(':', $helpId, 2);
			if ($idParts[0] == $type)
			{
				$typeHelps[]=$idParts[1];
			}
		}
		return $typeHelps;
	}

?>
