<?php
include "config/koneksi.php";

session_start();

// Proses login
if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password']; // Tidak di-escape karena akan di-verify hash-nya

    $result = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'");

    if (mysqli_num_rows($result) > 0) {
        $userData = mysqli_fetch_assoc($result);

        // Verifikasi password (Mendukung hash baru)
        if (password_verify($password, $userData["pass"])) {
            $_SESSION["username"] = $username;
            $_SESSION["id"] = $userData["id"];
            $_SESSION["role"] = $userData["role"];
            header('Location: dashboard.php');
            exit;
        } else {
            // Fallback untuk password lama (plain text) - HAPUS SETELAH SEMUA USER RESET PASSWORD
            if ($password === $userData["pass"]) {
                $_SESSION["username"] = $username;
                $_SESSION["id"] = $userData["id"];
                $_SESSION["role"] = $userData["role"];
                header('Location: dashboard.php');
                exit;
            }
            header('Location: index.php?error=login_failed');
            exit;
        }
    } else {
        header('Location: index.php?error=login_failed');
        exit;
    }
}
?>