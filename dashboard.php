<?php
session_start();
if (!isset($_SESSION['username'])) {
  header("Location: index.php");
  exit;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard - Sistem Sarana Prasarana</title>
  <link rel="icon" type="image/png" href="assets/images/cba.png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://kit.fontawesome.com/a404219d80.js" crossorigin="anonymous"></script>
  <style>
    :root {
      --blue: #2563eb;
      --blue-dark: #1e3a8a;
      --blue-light: #3b82f6;
    }

    body {
      background: #f8fafc;
      font-family: 'Segoe UI', sans-serif;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    .navbar-dashboard {
      background: linear-gradient(135deg, var(--blue-dark), var(--blue-light));
    }

    .menu-card {
      background: #fff;
      border: none;
      border-radius: 20px;
      padding: 40px 20px;
      text-align: center;
      transition: all 0.3s ease;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
      cursor: pointer;
      text-decoration: none;
      color: inherit;
      display: block;
      height: 100%;
    }

    .menu-card:hover {
      transform: translateY(-10px);
      box-shadow: 0 20px 40px rgba(37, 99, 235, 0.15);
      border-color: var(--blue-light);
    }

    .menu-icon {
      font-size: 60px;
      color: var(--blue);
      margin-bottom: 20px;
      transition: all 0.3s ease;
    }

    .menu-card:hover .menu-icon {
      transform: scale(1.1);
      color: var(--blue-dark);
    }

    .menu-title {
      font-size: 1.25rem;
      font-weight: 700;
      color: #1e293b;
      margin-bottom: 10px;
    }

    .menu-desc {
      color: #64748b;
      font-size: 0.9rem;
    }

    footer {
      background: linear-gradient(135deg, var(--blue-dark), var(--blue-light));
      color: #fff;
      margin-top: auto;
    }
  </style>

  <link rel="manifest" href="manifest.json">
  <meta name="theme-color" content="#1e3a8a">
  <link rel="apple-touch-icon" href="assets/images/cba.png">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
</head>

<body>
  <nav class="navbar navbar-dashboard fixed-top py-3 shadow">
    <div class="container-fluid d-flex justify-content-between align-items-center">
      <div class="d-flex align-items-center gap-2 text-white">
        <img src="assets/images/cba.png" alt="Logo CBA"
          style="height:40px; background:white; padding:4px; border-radius:8px;">
        <span class="fw-bold fs-5 d-none d-sm-inline" style="letter-spacing:1px">SISTEM SARANA PRASARANA</span>
      </div>
      <div class="d-flex align-items-center gap-2 gap-md-3">
        <?php if (($_SESSION['role'] ?? '') === 'Admin'): ?>
          <a href="master_item.php"
            class="btn btn-sm btn-light text-primary rounded-pill px-3 fw-bold d-none d-sm-inline">
            <i class="fa-solid fa-list-check me-1"></i>Master Pengecekan
          </a>
          <a href="master_item.php" class="btn btn-sm btn-light text-primary rounded-pill d-inline d-sm-none"
            title="Master Pengecekan">
            <i class="fa-solid fa-list-check"></i>
          </a>
          <a href="users.php" class="btn btn-sm btn-light text-primary rounded-pill px-3 fw-bold d-none d-sm-inline">
            <i class="fa-solid fa-users-gear me-1"></i>Manajemen User
          </a>
          <a href="users.php" class="btn btn-sm btn-light text-primary rounded-pill d-inline d-sm-none"
            title="Manajemen User">
            <i class="fa-solid fa-users-gear"></i>
          </a>
        <?php endif; ?>
        <span class="text-white small d-none d-md-inline"><i
            class="fa-solid fa-user me-1"></i><?= htmlspecialchars($_SESSION['username']) ?></span>
        <a href="logout.php" class="btn btn-sm btn-outline-light rounded-pill px-2 px-md-3" title="Logout">
          <i class="fa-solid fa-right-from-bracket d-inline d-sm-none"></i>
          <span class="d-none d-sm-inline"><i class="fa-solid fa-right-from-bracket me-1"></i>Logout</span>
        </a>
      </div>
    </div>
  </nav>

  <div class="container" style="margin-top:100px; padding-bottom:40px;">
    <div class="text-center mb-4 mb-md-5 px-2">
      <p class="text-secondary small mb-1" style="letter-spacing:1px;text-transform:uppercase;font-weight:600">
        <i class="fa-regular fa-calendar me-1"></i><?= date('l, d F Y') ?>
      </p>
      <h3 class="fw-bold text-dark mb-1">
        Selamat Datang, <?= htmlspecialchars($_SESSION['username']) ?> 👋
      </h3>
      <p class="text-secondary small d-none d-sm-block mb-0">
        Sistem Kartu Riwayat Sarana Prasarana</p>
    </div>

    <div class="row g-4 justify-content-center mx-auto" style="max-width: 900px;">
      <!-- Modul APAR -->
      <div class="col-12 col-md-6">
        <a href="apar_home.php" class="menu-card text-decoration-none">
          <i class="fa-solid fa-fire-extinguisher menu-icon"></i>
          <div class="menu-title">Perawatan APAR</div>
          <div class="menu-desc">Kelola data pemadam api ringan, jadwal pemeriksaan rutin, dan cetak kartu riwayat APAR.
          </div>
        </a>
      </div>

      <!-- Modul Gedung -->
      <div class="col-12 col-md-6">
        <a href="gedung_home.php" class="menu-card text-decoration-none">
          <i class="fa-solid fa-building-circle-check menu-icon"></i>
          <div class="menu-title">Perawatan Gedung</div>
          <div class="menu-desc">Kelola data kondisi bangunan, atap, lantai, pintu/kaca, dan cetak kartu riwayat gedung.
          </div>
        </a>
      </div>

      <!-- Modul Hydrant -->
      <div class="col-12 col-md-6">
        <a href="hydrant_home.php" class="menu-card text-decoration-none">
          <i class="fa-solid fa-fire menu-icon"></i>
          <div class="menu-title">Perawatan Hydrant</div>
          <div class="menu-desc">Kelola data instalasi hydrant, pemeliharaan valve, nozzle, hose, dan cetak kartu
            riwayat hydrant.</div>
        </a>
      </div>

      <!-- Modul Grease Trap -->
      <div class="col-12 col-md-6">
        <a href="grease_trap_home.php" class="menu-card text-decoration-none">
          <i class="fa-solid fa-sink menu-icon"></i>
          <div class="menu-title">Perawatan Grease Trap</div>
          <div class="menu-desc">Kelola data penyaring lemak, pembersihan internal, saluran in/out, dan cetak kartu
            riwayat grease trap.</div>
        </a>
      </div>

      <!-- Modul Toilet -->
      <div class="col-12 col-md-6">
        <a href="toilet_home.php" class="menu-card text-decoration-none">
          <i class="fa-solid fa-restroom menu-icon"></i>
          <div class="menu-title">Perawatan Toilet</div>
          <div class="menu-desc">Kelola checklist perawatan toilet per hari, lihat riwayat, dan cetak kartu perawatan.</div>
        </a>
      </div>


    </div>
  </div>

  <footer class="py-3 text-center">
    &copy; <?= date('Y') ?> - Sistem Sarana Prasarana | Team IT Pabrik
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

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