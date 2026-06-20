<?php
$db = new mysqli('127.0.0.1', 'root', '', 'pmi_stok_darah');
if ($db->connect_errno) {
    echo 'DBERR:' . $db->connect_error . "\n";
    exit(1);
}
$query = "SELECT COUNT(*) AS cnt FROM notifications WHERE message LIKE '%BDRS #%';";
$res = $db->query($query);
if (!$res) {
    echo 'QERR:' . $db->error . "\n";
    exit(1);
}
$row = $res->fetch_assoc();
echo 'COUNT: ' . $row['cnt'] . "\n";
$res->close();
$db->close();
?>