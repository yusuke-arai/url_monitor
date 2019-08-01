<?php
require_once(__DIR__ . '/../const.php');

$db = new SQLite3(DB_FILE, SQLITE3_OPEN_READWRITE);

if (!empty($_REQUEST)) {
    $db->exec('BEGIN EXCLUSIVE TRANSACTION;');
    foreach ($_REQUEST["desc"] as $index => $desc) {
        if (empty($_REQUEST["desc"][$index]) || empty($_REQUEST["url"][$index]) ) continue;

        if (!empty($_REQUEST["id"][$index])) {
            $stmt = $db->prepare("UPDATE urls SET desc = :desc, url = :url, timeout = :timeout, retry = :retry, alert_on_errors_continue = :alert_on_errors_continue WHERE id = :id");
            $stmt->bindValue(':id', $_REQUEST["id"][$index], SQLITE3_INTEGER);
            $stmt->bindValue(':desc', $_REQUEST["desc"][$index], SQLITE3_TEXT);
            $stmt->bindValue(':url', $_REQUEST["url"][$index], SQLITE3_TEXT);
            $stmt->bindValue(':timeout', intval($_REQUEST["timeout"][$index]), SQLITE3_INTEGER);
            $stmt->bindValue(':retry', intval($_REQUEST["retry"][$index]), SQLITE3_INTEGER);
            $stmt->bindValue(':alert_on_errors_continue', isset($_REQUEST["alert_on_errors_continue"][$index]), SQLITE3_INTEGER);
            $stmt->execute();
            $stmt->close();
            unset($stmt);
        } else {
            $stmt = $db->prepare("INSERT INTO urls (desc, url, timeout, retry, alert_on_errors_continue, status_code, errors_count, message) VALUES (:desc, :url, :timeout, :retry, :alert_on_errors_continue, -1, 0, '-')");
            $stmt->bindValue(':desc', $_REQUEST["desc"][$index], SQLITE3_TEXT);
            $stmt->bindValue(':url', $_REQUEST["url"][$index], SQLITE3_TEXT);
            $stmt->bindValue(':timeout', intval($_REQUEST["timeout"][$index]), SQLITE3_INTEGER);
            $stmt->bindValue(':retry', intval($_REQUEST["retry"][$index]), SQLITE3_INTEGER);
            $stmt->bindValue(':alert_on_errors_continue', isset($_REQUEST["alert_on_errors_continue"][$index]), SQLITE3_INTEGER);
            $stmt->execute();
            $stmt->close();
            unset($stmt);
        }
    }
    $db->exec('COMMIT TRANSACTION;');
}

header('Location: .');
