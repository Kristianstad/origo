<?php

	require_once("./functions/pkColumnOfTable.php");
	require_once("./functions/manage/updatedColumns.php");
	require_once("./functions/manage/appendUpdatedColumnsToSql.php");

	function sqlForUpdate($target, $updatePosts)
	{
		require("./constants/configSchema.php");
		$targetTable=key($target).'s';
		$targetPkColumn=pkColumnOfTable($targetTable);
		$updatedColumns=updatedColumns($targetTable, $updatePosts);
		$sql="UPDATE $configSchema.$targetTable SET";
		$sql=appendUpdatedColumnsToSql($updatedColumns, $sql);
		$sql=$sql." WHERE $targetPkColumn = '".current($target)."'";
		return $sql;
	}
	
?>
