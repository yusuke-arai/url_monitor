<?php
require_once(__DIR__ . '/../const.php');

$db = new SQLite3(DB_FILE, SQLITE3_OPEN_READWRITE);

if (!empty($_REQUEST['id'])) {
    $db->exec('BEGIN EXCLUSIVE TRANSACTION;');

    $stmt = $db->prepare("DELETE FROM logs WHERE url_id = :id;");
    $stmt->bindValue(':id', $_REQUEST['id']);
    $stmt->execute();

    $stmt = $db->prepare("DELETE FROM urls WHERE id = :id;");
    $stmt->bindValue(':id', $_REQUEST['id']);
    $stmt->execute();

    $db->exec('COMMIT TRANSACTION;');
}

header('Location: .');
