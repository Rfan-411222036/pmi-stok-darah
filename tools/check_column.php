<?php
$mysqli = new mysqli('127.0.0.1', 'root', '', 'pmi_stok_darah');
if ($mysqli->connect_error) {
    echo "DB_CONNECT_ERROR: " . $mysqli->connect_error . PHP_EOL;
    exit(1);
}
$res = $mysqli->query("SHOW COLUMNS FROM produsen LIKE 'telepon'");
if ($res && $res->num_rows > 0) {
    echo "FOUND" . PHP_EOL;
} else {
    echo "MISSING" . PHP_EOL;
}
$mysqli->close();
