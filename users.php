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
  <link rel="icon" type="image/png" href="assets/images/cba.png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    :root {
      --blue: #2563eb;
      --blue-dark: #1e3a8a;
      --blue-light: #3b82f6;
    }
    body { background: #f0f2f5; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
    .navbar { background: linear-gradient(135deg, var(--blue-dark), var(--blue-light)); }
    .card { border: none; border-radius: 16px; box-shadow: 0 4px 24px rgba(0,0,0,0.06); }
    .table thead { background: #f8fafc; }
    .table th { font-weight: 600; text-transform: uppercase; font-size: 11px; letter-spacing: 0.5px; color: #64748b; padding: 12px 16px; }
    .table td { padding: 16px; }
    .btn-primary { background: var(--blue-dark); border: none; transition: all 0.2s; }
    .btn-primary:hover { background: var(--blue); transform: translateY(-1px); box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2); }
    .btn-outline-secondary { border-color: #cbd5e1; color: #475569; }
    .btn-outline-secondary:hover { background-color: #f1f5f9; color: #1e293b; border-color: #94a3b8; }
    
    @media (max-width: 576px) {
      .navbar-brand span { font-size: 16px !important; }
      .card { padding: 20px !important; }
      .container { margin-top: 90px !important; }
    }
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
      <div class="d-flex align-items-center gap-2">
        <span class="text-white small d-none d-sm-inline"><i class="fa-solid fa-user me-1"></i><?= htmlspecialchars($_SESSION['username']) ?></span>
      </div>
    </div>
  </nav>

  <div class="container" style="margin-top:110px; padding-bottom:40px;">
    <!-- Row Header dengan tombol Kembali -->
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
      <a href="dashboard.php" class="btn btn-sm btn-outline-secondary rounded-pill bg-white px-3 shadow-sm d-flex align-items-center gap-2">
        <i class="fa-solid fa-arrow-left"></i> <span>Kembali ke Dashboard</span>
      </a>
    </div>

    <div class="row">
      <!-- Form Tambah User -->
      <div class="col-lg-4 mb-4">
        <div class="card p-4">
          <h5 class="fw-bold mb-3 text-primary d-flex align-items-center gap-2">
            <i class="fa-solid fa-user-plus"></i> Tambah User Baru
          </h5>
          <hr class="text-muted opacity-25">
          <form method="POST">
            <div class="mb-3">
              <label class="form-label fw-semibold text-secondary small">Username</label>
              <input type="text" name="username" class="form-control form-control-md rounded-3" placeholder="Masukkan username" required>
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold text-secondary small">Password</label>
              <input type="password" name="password" class="form-control form-control-md rounded-3" placeholder="Masukkan password" required>
            </div>
            <div class="mb-4">
              <label class="form-label fw-semibold text-secondary small">Role / Hak Akses</label>
              <select name="role" class="form-select form-select-md rounded-3" required>
                <option value="User">User (Input Saja)</option>
                <option value="Admin">Admin (Full Akses)</option>
              </select>
            </div>
            <button type="submit" name="tambah_user" class="btn btn-primary w-100 py-2.5 fw-bold shadow-sm rounded-3">
              <i class="fa-solid fa-save me-1"></i>Simpan User
            </button>
          </form>
        </div>
      </div>

      <!-- Daftar User -->
      <div class="col-lg-8 mb-4">
        <div class="card p-4">
          <h5 class="fw-bold mb-3 text-primary d-flex align-items-center gap-2">
            <i class="fa-solid fa-users"></i> Daftar Pengguna Sistem
          </h5>
          <hr class="text-muted opacity-25">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead>
                <tr>
                  <th style="width: 60px;">No</th>
                  <th>Username</th>
                  <th>Role</th>
                  <th class="text-center" style="width: 120px;">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php $no = 1; while ($u = mysqli_fetch_assoc($listUsers)): ?>
                <tr>
                  <td><?= $no++ ?></td>
                  <td class="fw-bold text-dark"><?= htmlspecialchars($u['username']) ?></td>
                  <td>
                    <span class="badge <?= $u['role'] == 'Admin' ? 'bg-primary' : 'bg-secondary' ?> rounded-pill px-3 py-1.5">
                      <?= $u['role'] ?>
                    </span>
                  </td>
                  <td class="text-center">
                    <?php if ($u['id'] != $_SESSION['id']): ?>
                    <button class="btn btn-sm btn-outline-danger border-0 rounded-circle" style="width: 32px; height: 32px; padding: 0;" onclick="confirmDelete(<?= $u['id'] ?>)" title="Hapus User">
                      <i class="fa-solid fa-trash"></i>
                    </button>
                    <?php else: ?>
                    <span class="badge bg-light text-muted border px-3 py-1.5">Akun Anda</span>
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

