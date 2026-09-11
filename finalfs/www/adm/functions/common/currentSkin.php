<?php

function currentSkin(array $skins): array
{
	$skinId=$_COOKIE['origo_admin_skin'] ?? null;
	if (is_string($skinId))
	{
		$skin=array_column_search($skinId, 'skin_id', $skins);
		if (!empty($skin))
		{
			return $skin;
		}
	}
	return array();
}

?>