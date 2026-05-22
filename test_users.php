<?php
$conn = mysqli_connect('localhost', 'root', '', 'saranaprasarana');

// Step 1: Add both Auditor and Monitoring to enum temporarily
$res1 = mysqli_query($conn, "ALTER TABLE users MODIFY COLUMN role enum('Admin','User','superadmin','Auditor','Monitoring')");
echo $res1 ? "Step 1 OK: enum expanded\n" : "Step 1 FAIL: " . mysqli_error($conn) . "\n";

// Step 2: Update any 'Auditor' or empty role for monitoring user to 'Monitoring'
$res2 = mysqli_query($conn, "UPDATE users SET role='Monitoring' WHERE role='Auditor' OR (role='' AND username='monitoring')");
echo $res2 ? "Step 2 OK: updated " . mysqli_affected_rows($conn) . " rows\n" : "Step 2 FAIL: " . mysqli_error($conn) . "\n";

// Step 3: Remove 'Auditor' from enum, keep only Monitoring
$res3 = mysqli_query($conn, "ALTER TABLE users MODIFY COLUMN role enum('Admin','User','superadmin','Monitoring')");
echo $res3 ? "Step 3 OK: enum finalized\n" : "Step 3 FAIL: " . mysqli_error($conn) . "\n";

// Verify
$res4 = mysqli_query($conn, "SELECT id, username, role FROM users ORDER BY id");
echo "\nCurrent users:\n";
while ($r = mysqli_fetch_assoc($res4)) {
    echo "ID:{$r['id']} | User:{$r['username']} | Role:'{$r['role']}'\n";
}
?>
