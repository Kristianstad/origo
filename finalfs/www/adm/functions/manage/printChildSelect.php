<?php

	// takes a full target of a type that can have children (array), a child table name (string), a css class name for th-elements (string),
	// a heading (string), inheritPosts (array), groupLevel (optional, integer), and selectedValue (optional, string).
	// Prints a child selection form for the given target. The type of the child depends on the second parameter. Style and heading is 
	// controlled by the third and fourth parameter. GroupLevel and selectedValue is only used when looping through a group tree 
	// (groups within groups) to keep track of the current depth level and parent.
	require_once("./functions/pkColumnOfTable.php");
	require_once("./functions/manage/printSelectOptions.php");
	require_once("./functions/manage/printHiddenInputs.php");

	function printChildSelect($target, $column, &$thClass, $heading, $inheritPosts, $groupLevel=1, $selectedValue=null)
	{
		$targetColumnValue=trim(current($target)[$column], '{}');
		if (!empty($targetColumnValue))
		{
			$groupIdsArray=array();
			$columnType=rtrim($column, 's');
			$sName=$columnType.'Id';
			$columnArr=explode(',', $targetColumnValue);
			if (key($target) == 'group' && ($column == 'groups' || $column == 'layers'))
			{
				if (isset($inheritPosts['groupIds']))
				{
					$groupIdsArray=explode(',', $inheritPosts['groupIds']);
				}
				if ($column == 'groups')
				{
					$sName='groupIds';
					if (!isset($selectedValue) && isset($groupIdsArray[$groupLevel]))
					{
						$selectedValue=$groupIdsArray[$groupLevel];
					}
				}
				$groupLevels=count($groupIdsArray);
				if ($groupLevels > 1)
				{
					$groupIdsArray=array_slice($groupIdsArray, 0, $groupLevel);
				}
				if ($column == 'groups' && !empty($groupIdsArray))
				{
					$columnArr=array_map(function($val) use ($groupIdsArray) { return implode(',', $groupIdsArray).','.$val; } , $columnArr);
				}
			}
			if (!isset($selectedValue) && isset($inheritPosts[$columnType.'Id']))
			{
				$selectedValue=$inheritPosts[$columnType.'Id'];
			}
			$options=array_merge(array(""), $columnArr);
			if (isset($selectedValue) && in_array($selectedValue, $options))
			{
				$edith3Class='h3Black';
			}
			else
			{
				$edith3Class='h3Lightgray';
			}
			$targetId=current($target)[pkColumnOfTable(key($target).'s')];
			$ucColumn=ucfirst($column);
			$hiddenInputs=array();
			if ($column == 'schemas')
			{
				$hiddenInputs['databaseId']=$inheritPosts['databaseId'];
				$optionLabels = array_merge(array(""), preg_filter('/^'.current($target)['database_id'].'[.]/', '', $options));
				$options=array_combine($options, $optionLabels);
			}
			elseif ($column == 'tables')
			{
				if (isset($inheritPosts['databaseId']))
				{
					$hiddenInputs['databaseId']=$inheritPosts['databaseId'];
				}
				$hiddenInputs['schemaId']=$inheritPosts['schemaId'];
				$optionLabels = array_merge(array(""), preg_filter('/^'.current($target)['schema_id'].'[.]/', '', $options));
				$options=array_combine($options, $optionLabels);
			}
			else
			{
				if (isset($inheritPosts['mapId']))
				{
					$hiddenInputs['mapId']=$inheritPosts['mapId'];
				}
				if ($column == 'layers')
				{
					if (isset($inheritPosts['groupIds']))
					{
						$hiddenInputs['groupIds']=implode(',', $groupIdsArray);
					}
				}
			}
			$formId=$column.$groupLevel.'HeadForm';
			echo <<<HERE
				<th class="{$thClass}">
					<h3 class="{$edith3Class}">{$heading}</h3>
					<div class="headFormDiv1">
						<form id="{$formId}" class="headForm" method="post">
							<div class="headFormDiv2">
								<select onchange='this.form.submit();' class='headSelect' id='{$targetId}{$ucColumn}' name='{$sName}'>
			HERE;
			printSelectOptions($options, $selectedValue);
			echo				"</select>";
			printHiddenInputs($hiddenInputs);
			echo <<<HERE
								<button type="submit" class="headButton" name="{$columnType}Button" value="get">Hämta</button>
							</div>
						</form>
					</div>
				</th>
			HERE;
			$thClass='thNext';
		}
	}

?>
