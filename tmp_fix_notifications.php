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
$updated = 0;
while ($row = $res->fetch_assoc()) {
    if (preg_match('/BDRS #(\d+)/', $row['message'], $m)) {
        $prodId = (int) $m[1];
        $prodRes = $db->query('SELECT nama FROM produsen WHERE id_produsen=' . $prodId . ' LIMIT 1');
        if ($prodRes && ($prod = $prodRes->fetch_assoc())) {
            $produsenName = $prod['nama'];
            $newMessage = preg_replace('/BDRS #' . $prodId . '/', 'BDRS ' . $produsenName, $row['message']);
            if ($newMessage !== $row['message']) {
                $safe = $db->real_escape_string($newMessage);
                $updateSql = 'UPDATE notifications SET message="' . $safe . '" WHERE id=' . (int) $row['id'];
                $db->query($updateSql);
                if (!$db->error) {
                    $updated++;
                } else {
                    echo 'UPDATE_ERR: ' . $db->error . "\n";
                    echo 'SQL: ' . $updateSql . "\n";
                }
            }
        }
    }
}
$res->close();
$db->close();
echo "Updated: $updated\n";
?>
