<?php

	require_once("./functions/pkColumnOfTable.php");
	require_once("./functions/manage/updatedTarget.php");
	require_once("./functions/manage/appendUpdatedColumnsToSql.php");

	function sqlForUpdate($target, $updatePosts)
	{
		require("./constants/configSchema.php");
		$targetTable=key($target).'s';
		$targetPkColumn=pkColumnOfTable($targetTable);
		$targetPk=current($target)[$targetPkColumn];
		$target=updatedTarget($target, $updatePosts);
		$sql="UPDATE $configSchema.$targetTable SET";
		$sql=appendUpdatedColumnsToSql(current($target), $sql);
		$sql=$sql." WHERE $targetPkColumn = '".$targetPk."'";
		return $sql;
	}
	
?>
