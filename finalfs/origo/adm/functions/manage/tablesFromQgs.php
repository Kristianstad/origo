<?php

	function tablesFromQgs($qgsFile=null, $tables=array(), $subtree=null)
	{
		if (isset($subtree))
		{
			$xml = $subtree;
		}
		elseif (isset($qgsFile))
		{
			$xml = simplexml_load_file($qgsFile);
		}
		if (isset($xml))
		{
			foreach ( $xml->{'layer-tree-layer'} as $layer)
    		{
    			if ($layer['providerKey'] == "postgres")
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
    					$tables[]=trim($sourceParamArray['dbname'],"'").'.'.str_replace('"', "", $sourceParamArray['table']);
    				}
    			}
    		}
    		foreach ($xml->{'layer-tree-group'} as $group)
    		{
    			$tables=tablesFromQgs($null, $tables, $group);
    		}
    	}
    	return array_unique($tables);
	}

?>
