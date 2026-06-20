<?php
$db = new mysqli('127.0.0.1', 'root', '', 'pmi_stok_darah');
if ($db->connect_errno) {
    echo 'DBERR:' . $db->connect_error . "\n";
    exit(1);
}
$query = "SELECT id, user_id, title, message, created_at FROM notifications WHERE message LIKE '%BDRS #%' OR message LIKE '%BDRS %' LIMIT 50";
$res = $db->query($query);
if (!$res) {
    echo 'QERR:' . $db->error . "\n";
    exit(1);
}
while ($row = $res->fetch_assoc()) {
    echo json_encode($row, JSON_UNESCAPED_UNICODE) . "\n";
}
$res->close();
$db->close();
?>
