<?php

function markNewMapsChanged($dbh, string $configSchema, array $existingMapIds): void
{
    $result=pg_query($dbh, "SELECT map_id FROM {$configSchema}.maps");
    if ($result === false)
    {
        throw new RuntimeException(pg_last_error($dbh));
    }

    while ($row=pg_fetch_assoc($result))
    {
        $mapId=$row['map_id'];
        if (!isset($existingMapIds[$mapId]))
        {
            $updateResult=pg_query_params($dbh, "UPDATE {$configSchema}.maps SET changed = TRUE WHERE map_id = $1", array($mapId));
            if ($updateResult === false)
            {
                throw new RuntimeException(pg_last_error($dbh));
            }
        }
    }
}