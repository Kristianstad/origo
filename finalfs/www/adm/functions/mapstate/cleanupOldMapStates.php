<?php
// www/adm/functions/mapstate/cleanupOldMapStates.php

function cleanupOldMapStates($dbh, int $days): void
{
    $cutoff = "NOW() - INTERVAL '$days days'";
    $table  = getMapStatesTable();
    $sql = "
        DELETE FROM $table
        WHERE 
            (lastuse IS NOT NULL AND lastuse < $cutoff AND NOT preserve)
            OR
            (created < NOW() - INTERVAL '30 days' AND lastuse IS NULL AND NOT preserve)
    ";
    $result = pg_query($dbh, $sql);
    if ($result === false) {
        error_log("Cleanup av gamla map states misslyckades: " . pg_last_error($dbh));
    }
}
