<?php
$db = new mysqli('127.0.0.1', 'root', '', 'pmi_stok_darah');
if ($db->connect_errno) {
    echo 'DBERR:' . $db->connect_error . "\n";
    exit(1);
}
$query = "SELECT id, message FROM notifications WHERE message REGEXP 'BDRS #[0-9]+' LIMIT 50;";
$res = $db->query($query);
if (!$res) {
    echo 'QERR:' . $db->error . "\n";
    exit(1);
}
$count = 0;
while ($row = $res->fetch_assoc()) {
    $count++;
    echo json_encode($row, JSON_UNESCAPED_UNICODE) . "\n";
}
if ($count === 0) {
    echo "NONE\n";
}
$res->close();
$db->close();
