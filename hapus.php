<?php
session_start();
include_once "config/koneksi.php";

// Keamanan: Cek apakah sudah login
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}

// Keamanan: Hanya Admin yang boleh menghapus data
if (($_SESSION['role'] ?? '') !== 'Admin') {
    echo "<script>
        alert('Akses Ditolak! Hanya Admin yang memiliki otoritas untuk menghapus data.');
        window.history.back();
    </script>";
    exit;
}

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    if (isset($_GET['hapus_apar'])) {
        mysqli_query($conn, "DELETE FROM apar WHERE id = $id");
        mysqli_query($conn, "DELETE FROM checklist_apar WHERE apar_id = $id");
        header("Location: apar_home.php?pesan=hapus_sukses");
        exit;
    } 
    elseif (isset($_GET['hapus_gedung'])) {
        mysqli_query($conn, "DELETE FROM gedung WHERE id = $id");
        mysqli_query($conn, "DELETE FROM checklist_gedung WHERE gedung_id = $id");
        header("Location: gedung_home.php?pesan=hapus_sukses");
        exit;
    } 
    elseif (isset($_GET['hapus_hydrant'])) {
        mysqli_query($conn, "DELETE FROM hydrant WHERE id = $id");
        mysqli_query($conn, "DELETE FROM checklist_hydrant WHERE hydrant_id = $id");
        header("Location: hydrant_home.php?pesan=hapus_sukses");
        exit;
    } 
    elseif (isset($_GET['hapus_grease_trap'])) {
        mysqli_query($conn, "DELETE FROM grease_trap WHERE id = $id");
        mysqli_query($conn, "DELETE FROM checklist_grease_trap WHERE grease_trap_id = $id");
        header("Location: grease_trap_home.php?pesan=hapus_sukses");
        exit;
    }
    elseif (isset($_GET['hapus_toilet'])) {
        mysqli_query($conn, "DELETE FROM toilet_unit WHERE id = $id");
        mysqli_query($conn, "DELETE FROM checklist_toilet WHERE toilet_id = $id");
        header("Location: toilet_home.php?pesan=hapus_sukses");
        exit;
    }
}

$conn->close();
header("Location: dashboard.php");
exit;
?>