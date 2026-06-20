<?php
$db = new mysqli('127.0.0.1', 'root', '', 'pmi_stok_darah');
if ($db->connect_errno) {
    echo 'DBERR:' . $db->connect_error . "\n";
    exit(1);
}
$query = "SELECT id, message FROM notifications WHERE message LIKE '%BDRS #%';";
$res = $db->query($query);
if (!$res) {
    echo 'QERR:' . $db->error . "\n";
    exit(1);
}
while ($row = $res->fetch_assoc()) {
    $m = [];
    $matched = preg_match('/BDRS #(\d+)/', $row['message'], $m);
    echo "ID=" . $row['id'] . " MATCH=" . ($matched ? 'yes' : 'no') . " MSG=" . $row['message'] . "\n";
    if ($matched) {
        echo "  PRODID=" . $m[1] . "\n";
    }
}
$res->close();
$db->close();
