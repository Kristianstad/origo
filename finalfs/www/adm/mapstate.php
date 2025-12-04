<?php
// www/adm/mapstate.php
// Main dispatcher – completely stateless

require_once __DIR__ . '/functions/includeDirectory.php';
includeDirectory(__DIR__ . '/functions/common');
includeDirectory(__DIR__ . '/functions/mapstate');

require_once __DIR__ . '/constants/mapstateMaxUnused.php';

$dbh = dbh();

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Cache-Control: no-cache, no-store, must-revalidate, max-age=0, s-maxage=0');
header('Pragma: no-cache');
header('Expires: 0');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

if (!$dbh) {
    http_response_code(500);
    echo json_encode(['error' => 'Databasanslutning saknas']);
    exit;
}

cleanupOldMapStates($dbh, $mapstateMaxUnused);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    createMapState($dbh);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    retrieveMapState($dbh);
    exit;
}

http_response_code(405);
echo json_encode(['error' => 'Metod ej tillåten']);
