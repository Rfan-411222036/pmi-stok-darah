<?php
$mysqli = new mysqli('127.0.0.1', 'root', '', 'pmi_stok_darah');
if ($mysqli->connect_error) {
    echo "DB_CONNECT_ERROR: " . $mysqli->connect_error . PHP_EOL;
    exit(1);
}
$check = $mysqli->query("SHOW COLUMNS FROM notifications LIKE 'is_read'");
if ($check && $check->num_rows > 0) {
    echo "ALREADY_EXISTS\n";
    exit(0);
}
$sql = "ALTER TABLE notifications ADD COLUMN is_read TINYINT(1) NOT NULL DEFAULT 0 AFTER message";
if ($mysqli->query($sql) === TRUE) {
    echo "ADDED\n";
} else {
    echo "ERROR: " . $mysqli->error . PHP_EOL;
}
$mysqli->close();
