<?php
// www/adm/functions/mapstate/getMapStatesTable.php

function getMapStatesTable(): string
{
    static $table = null;
    if ($table === null) {
        require __DIR__ . '/../../constants/configSchema.php';
        $schema = $configSchema ?? 'public';
        $table  = pg_escape_identifier($schema) . '.' . pg_escape_identifier('mapstates');
    }
    return $table;
}
