<?php
header("Cache-Control: must-revalidate, max-age=0, s-maxage=0, no-cache, no-store");

require_once("./functions/includeDirectory.php");
includeDirectory("./functions/common");
includeDirectory("./functions/news");

require './constants/authMethod.php';

readAndCloseSession();
$dbh = null;

if ($authMethod === 'ldap') {
    $dbh = dbh();
    initUserLdap($dbh);
}

$action = $_GET['action'] ?? '';
$newId  = $_GET['newId']  ?? '';
$return = isset($_GET['return']) ? explode(',', $_GET['return']) : [];

$isLoggedIn = isset($_SESSION['user']) && $_SESSION['user'] !== false;

// === Början av sidan ===
echo <<<HTML
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
HTML;

require "./styles/news.css";

echo <<<HTML
</style>
</head>
<body>
HTML;

// === Innehåll ===
if ($isLoggedIn) {
    ignore_user_abort(true);

    $username = $_SESSION['user']['id'];

    if ($authMethod !== 'ldap') {
        $dbh = dbh();
    }

    $pgNewsArray = pgNewsArray($dbh);
    $userNews    = userNews($username, $pgNewsArray);

    $selectedNew = null;
    if ($newId !== '') {
        $selectedNew = selectNew($userNews, $newId);
    }

    if ($action === 'list') {
        printNewsList($userNews);
    }
    elseif ($action === 'load' && !empty($selectedNew)) {
        printNews($username, $selectedNew, $return);
    }
    elseif (
        ($action === 'delete' || $action === 'read') &&
        !empty($selectedNew) &&
        !in_array($username, $selectedNew[$action . 's'] ?? [], true)
    ) {
        readDelete($username, $selectedNew, $action);
    }
    elseif ($action === 'subjects') {
        printNewsSubjects($username, $userNews);
    }
    elseif ($action === 'unread') {
        testUnread($username, $userNews);
    }

    ignore_user_abort(false);
} else {
    echo '<b style="color:#000000">Ej inloggad!</b>';
}

if (isset($dbh) && $dbh) {
    pg_close($dbh);
}

// === Slut på sidan ===
echo <<<HTML
</body>
</html>
HTML;
