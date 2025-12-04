<?php
// www/adm/functions/mapstate/updateLastUse.php

function updateLastUse($dbh, string $id): void
{
    $table = getMapStatesTable();
    pg_query($dbh, "UPDATE $table SET lastuse = NOW() WHERE mapstate_id = '$id'");
}
