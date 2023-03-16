<?php

	function toSwedish($engStr)
	{
		$translateArray=array(
			'map'		=>'karta',
			'maps'		=>'kartor',
			'control'	=>'kontroll',
			'controls'	=>'kontroller',
			'group'		=>'grupp',
			'groups'	=>'grupper',
			'layer'		=>'lager',
			'layers'	=>'lager',
			'footer'	=>'sidfot',
			'footers'	=>'sidfötter',
			'source'	=>'källa',
			'sources'	=>'källor',
			'service'	=>'tjänst',
			'services'	=>'tjänster',
			'contact'	=>'kontakt',
			'contacts'	=>'kontakter',
			'export'	=>'exportlager',
			'exports'	=>'exportlager',
			'update'	=>'uppdateringsrutin',
			'updates'	=>'uppdateringar',
			'origin'	=>'ursprungskälla',
			'origins'	=>'ursprungskällor'
		);
		if (isset($translateArray[$engStr]))
		{
			return $translateArray[$engStr];
		}
		else
		{
			return $engStr;
		}
	}
	
?>
