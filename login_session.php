<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if(!isset($_SESSION['username'])) {
    echo "<script>alert('Anda belum login. Silahkan login terlebih dahulu!');
    window.location='index.php'</script>";
    exit;
}

?>
