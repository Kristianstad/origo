<?php

	function tablesFromQgsXml($qgsXml=null, $layerName=null, $tables=array(), $subtree=null)
	{
		if (isset($subtree))
		{
			$xml = $subtree;
		}
		elseif (isset($qgsXml))
		{
			$xml = $qgsXml;
		}
		if (isset($xml))
		{
			foreach ( $xml->{'layer-tree-layer'} as $layer)
    		{
    			if ((!isset($layerName) && $layer['providerKey'] == "postgres") || (isset($layerName) && $layer['name'] == $layerName))
    			{
    				$source=explode(' ', $layer['source']);
    				$sourceParamArray=array();
    				foreach ($source as $sourceParam)
    				{
    					$tmp=explode('=', $sourceParam);
    					$sourceParamArray[$tmp[0]]=$tmp[1];
    				}
    				if (!empty($sourceParamArray['dbname']) && !empty($sourceParamArray['table']) && strpos($sourceParamArray['table'], '(') === false)
    				{
    					$table=trim($sourceParamArray['dbname'],"'").'.'.str_replace('"', "", $sourceParamArray['table']);
    					$tables[]=$table;
    					if (isset($layerName))
    					{
    						return array_unique($tables);
    					}
    				}
    			}
    		}
    		foreach ($xml->{'layer-tree-group'} as $group)
    		{
    			if (isset($layerName) && $group['name'] == $layerName)
    			{
    				$tables=tablesFromQgsXml($null, null, $tables, $group);
    				return array_unique($tables);
    			}
    			else
    			{
    				$tables=tablesFromQgsXml($null, $layerName, $tables, $group);
    				if (isset($layerName) && !empty($tables))
    				{
    					return array_unique($tables);
    				}
    			}
    		}
    	}
    	return array_unique($tables);
	}

?>
