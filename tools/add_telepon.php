<?php
$mysqli = new mysqli('127.0.0.1', 'root', '', 'pmi_stok_darah');
if ($mysqli->connect_error) {
    echo "DB_CONNECT_ERROR: " . $mysqli->connect_error . PHP_EOL;
    exit(1);
}
$check = $mysqli->query("SHOW COLUMNS FROM produsen LIKE 'telepon'");
if ($check && $check->num_rows > 0) {
    echo "ALREADY_EXISTS\n";
    exit(0);
}
$sql = "ALTER TABLE produsen ADD COLUMN telepon VARCHAR(50) NULL AFTER alamat";
if ($mysqli->query($sql) === TRUE) {
    echo "ADDED\n";
} else {
    echo "ERROR: " . $mysqli->error . PHP_EOL;
}
$mysqli->close();
