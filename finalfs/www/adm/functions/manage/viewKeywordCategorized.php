<?php

	function viewKeywordCategorized($view)
	{
		require("./constants/views.php");
		require("./constants/keywordCategorized.php");
		if (empty($view) || $view == 'Allt')
		{
			return $keywordCategorized;
		}
		else
		{
			return array_intersect($keywordCategorized, $views[$view]);
		}
	}

?>
