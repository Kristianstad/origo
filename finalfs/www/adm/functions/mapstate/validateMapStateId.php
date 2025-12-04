<?php
// www/adm/functions/mapstate/validateMapStateId.php
function validateMapStateId(string $id): bool
{
    return preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $id) === 1;
}
