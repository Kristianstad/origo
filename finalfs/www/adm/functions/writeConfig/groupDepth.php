<?php

	require_once("./functions/pgArrayToPhp.php");
	require_once("./functions/array_column_search.php");

	function groupDepth($groupIds, $layerIds=array())
	{
		GLOBAL $groups;
		foreach ($groupIds as $groupId)
		{
			$group = array_column_search($groupId, 'group_id', $groups);
			$groupGroups=pgArrayToPhp($group['groups']);
			
			if (!empty($groupGroups))
			{
				$groupArray=groupDepth($groupGroups);
			}
			else
			{
				$groupArray=array();
			}
			$groupLayers=pgArrayToPhp($group['layers']);
			$groupLayers = preg_filter('/^/', $groupId.'>', $groupLayers);
			if (!empty($groupLayers))
			{
				$groupArray=array_merge($groupArray, $groupLayers);
			}
			$layerIds[$groupId]=$groupArray;
		}
		return $layerIds;
	}

?>
