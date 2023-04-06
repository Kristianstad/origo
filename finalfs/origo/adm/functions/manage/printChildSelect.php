<?php

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
					<div style="display:flex">
						<form id="{$formId}" class="headForm" method="post">
							<select onchange='this.form.submit();' class='headSelect' id='{$targetId}{$ucColumn}' name='{$sName}'>
			HERE;
			printSelectOptions($options, $selectedValue);
			echo           "</select>&nbsp;";
			printHiddenInputs($hiddenInputs);
			echo <<<HERE
							<button type="submit" class="headButton" name="{$columnType}Button" value="get" style="margin-left:-0.5em">
								Hämta
							</button>
						</form>
					</div>
				</th>
			HERE;
			$thClass='thNext';
		}
	}
	
?>
