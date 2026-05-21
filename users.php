<?php
include "config/koneksi.php";
session_start();

// Keamanan: Cek apakah sudah login
if (!isset($_SESSION['username'])) {
  header("Location: index.php");
  exit;
}

// Keamanan: Hanya Admin yang boleh mengakses halaman ini
if (($_SESSION['role'] ?? '') !== 'Admin') {
  header("Location: dashboard.php");
  exit;
}

// Handle Tambah User
if (isset($_POST['tambah_user'])) {
  $username = mysqli_real_escape_string($conn, $_POST['username']);
  $password = $_POST['password'];
  $role = mysqli_real_escape_string($conn, $_POST['role']);

  // Cek apakah username sudah ada
  $cek = mysqli_query($conn, "SELECT id FROM users WHERE username = '$username'");
  if (mysqli_num_rows($cek) > 0) {
    header("Location: users.php?error=exists");
    exit;
  }

  $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
  mysqli_query($conn, "INSERT INTO users (username, pass, role) VALUES ('$username', '$hashedPassword', '$role')");
  header("Location: users.php?success=add");
  exit;
}

// Handle Hapus User
if (isset($_GET['hapus'])) {
  $id = (int)$_GET['hapus'];
  // Jangan biarkan admin menghapus dirinya sendiri
  if ($id == $_SESSION['id']) {
    header("Location: users.php?error=self");
    exit;
  }
  mysqli_query($conn, "DELETE FROM users WHERE id = $id");
  header("Location: users.php?success=delete");
  exit;
}

$listUsers = mysqli_query($conn, "SELECT * FROM users WHERE role != 'superadmin' ORDER BY username ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manajemen User - Sistem Sarana Prasarana</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    :root {
      --blue: #0d6efd;
      --blue-dark: #0a58ca;
      --blue-light: #e7f1ff;
    }
    body { background: #f8fafc; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
    .navbar { background: linear-gradient(135deg, var(--blue-dark), var(--blue-light)); }
    .card { border: none; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
    .table thead { background: #f1f5f9; }
    .btn-primary { background: var(--blue-dark); border: none; }
    .btn-primary:hover { background: var(--blue); }
  </style>

    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#1e3a8a">
    <link rel="apple-touch-icon" href="assets/images/cba.png">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
</head>
<body>
  <nav class="navbar navbar-dark fixed-top py-3 shadow">
    <div class="container-fluid d-flex justify-content-between align-items-center">
      <div class="d-flex align-items-center gap-2 text-white" style="cursor:pointer" onclick="window.location.href='dashboard.php'">
        <img src="assets/images/cba.png" alt="Logo CBA" style="height:40px; background:white; padding:4px; border-radius:8px;">
        <span class="fw-bold fs-5" style="letter-spacing:1px">MANAJEMEN USER</span>
      </div>
      <a href="dashboard.php" class="btn btn-sm btn-outline-light rounded-pill px-3">
        <i class="fa-solid fa-arrow-left me-1"></i>Kembali
      </a>
    </div>
  </nav>

  <div class="container" style="margin-top:100px; padding-bottom:40px;">
    <div class="row">
      <!-- Form Tambah User -->
      <div class="col-lg-4 mb-4">
        <div class="card p-4">
          <h5 class="fw-bold mb-3 text-primary"><i class="fa-solid fa-user-plus me-2"></i>Tambah User Baru</h5>
          <hr>
          <form method="POST">
            <div class="mb-3">
              <label class="form-label fw-semibold">Username</label>
              <input type="text" name="username" class="form-control" placeholder="Masukkan username" required>
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold">Password</label>
              <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
            </div>
            <div class="mb-4">
              <label class="form-label fw-semibold">Role / Hak Akses</label>
              <select name="role" class="form-select" required>
                <option value="User">User (Input Saja)</option>
                <option value="Admin">Admin (Full Akses)</option>
              </select>
            </div>
            <button type="submit" name="tambah_user" class="btn btn-primary w-100 py-2 fw-bold shadow-sm">
              <i class="fa-solid fa-save me-1"></i>Simpan User
            </button>
          </form>
        </div>
      </div>

      <!-- Daftar User -->
      <div class="col-lg-8">
        <div class="card p-4">
          <h5 class="fw-bold mb-3 text-primary"><i class="fa-solid fa-users me-2"></i>Daftar Pengguna Sistem</h5>
          <div class="table-responsive">
            <table class="table table-hover align-middle">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Username</th>
                  <th>Role</th>
                  <th class="text-center">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php $no = 1; while ($u = mysqli_fetch_assoc($listUsers)): ?>
                <tr>
                  <td><?= $no++ ?></td>
                  <td class="fw-bold"><?= htmlspecialchars($u['username']) ?></td>
                  <td>
                    <span class="badge <?= $u['role'] == 'Admin' ? 'bg-primary' : 'bg-secondary' ?> rounded-pill px-3">
                      <?= $u['role'] ?>
                    </span>
                  </td>
                  <td class="text-center">
                    <?php if ($u['id'] != $_SESSION['id']): ?>
                    <button class="btn btn-sm btn-outline-danger border-0" onclick="confirmDelete(<?= $u['id'] ?>)">
                      <i class="fa-solid fa-trash"></i>
                    </button>
                    <?php else: ?>
                    <small class="text-muted italic">Akun Anda</small>
                    <?php endif; ?>
                  </td>
                </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    function confirmDelete(id) {
      Swal.fire({
        title: 'Hapus User?',
        text: "User ini tidak akan bisa login lagi ke sistem!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
      }).then((result) => {
        if (result.isConfirmed) {
          window.location.href = 'users.php?hapus=' + id;
        }
      })
    }

    // Notifikasi SweetAlert
    <?php if(isset($_GET['success'])): ?>
      Swal.fire('Berhasil!', 'Operasi berhasil dilakukan.', 'success');
    <?php endif; ?>
    <?php if(isset($_GET['error'])): ?>
      Swal.fire('Error!', '<?= $_GET['error'] == 'exists' ? "Username sudah terdaftar!" : "Terjadi kesalahan!" ?>', 'error');
    <?php endif; ?>
  </script>

    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('sw.js').then(reg => {
                    console.log('ServiceWorker registration successful');
                }).catch(err => {
                    console.log('ServiceWorker registration failed: ', err);
                });
            });
        }
    </script>
</body>
</html>

