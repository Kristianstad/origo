<?php
// www/adm/functions/mapstate/retrieveMapState.php

function retrieveMapState($dbh): never
{
    $id = trim($_GET['mapStateId'] ?? '');

    if (!validateMapStateId($id)) {
        http_response_code(400);
        echo json_encode(['error' => 'Ogiltigt eller saknat id']);
        exit;
    }

    updateLastUse($dbh, $id);

    $table  = getMapStatesTable();
    $sql    = "SELECT state FROM $table WHERE mapstate_id = '$id'";
    $result = pg_query($dbh, $sql);
    $row    = pg_fetch_assoc($result);

    if (!$row) {
        http_response_code(404);
        echo json_encode(['error' => 'Mapstate hittades inte']);
        exit;
    }

	echo $row['state'];
    exit;
}
