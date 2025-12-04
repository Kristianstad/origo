<?php
// www/adm/functions/mapstate/cleanupOldMapStates.php

function cleanupOldMapStates($dbh, int $days): void
{
    $cutoff = "NOW() - INTERVAL '$days days'";
    $table  = getMapStatesTable();

    $sql = "
        DELETE FROM $table
        WHERE lastuse < $cutoff
          AND NOT preserve
    ";

    pg_query($dbh, $sql);
}
