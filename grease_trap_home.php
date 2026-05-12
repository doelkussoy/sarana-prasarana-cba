<?php
include "config/koneksi.php";
session_start();
if (!isset($_SESSION['username'])) {
  header("Location: index.php");
  exit;
}

// Setup tabel jika belum ada
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS `grease_trap` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `no_kode` varchar(50) NOT NULL,
  `nama_sarana` varchar(100) DEFAULT NULL,
  `lokasi` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

mysqli_query($conn, "CREATE TABLE IF NOT EXISTS `checklist_grease_trap` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `grease_trap_id` int(11) NOT NULL,
  `tahun` int(4) NOT NULL,
  `bulan` tinyint(2) NOT NULL,
  `tanggal_cek` date DEFAULT NULL,
  `valve_handle` enum('Ok','Nok') DEFAULT NULL,
  `hose_coupling_conect` enum('Ok','Nok') DEFAULT NULL,
  `baut_valve_handle` enum('Ok','Nok') DEFAULT NULL,
  `fire_hose` enum('Ok','Nok') DEFAULT NULL,
  `slang_grease_trap` enum('Ok','Nok') DEFAULT NULL,
  `nozzle` enum('Ok','Nok') DEFAULT NULL,
  `box_grease_trap` enum('Ok','Nok') DEFAULT NULL,
  `paraf` varchar(100) DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `users_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// Cek apakah ada data Grease Trap, jika tidak insert sample
$cekGreaseTrap = mysqli_query($conn, "SELECT COUNT(*) as total FROM grease_trap");
$rowCek = mysqli_fetch_assoc($cekGreaseTrap);
// if ($rowCek['total'] == 0) {
//   mysqli_query($conn, "INSERT INTO `grease_trap` (`no_kode`, `nama_sarana`, `lokasi`) VALUES
//     ('KNR-1','Grease Trap 9 KG','GREASE TRAP UTAMA LANTAI 1'),
//     ('KNR-2','Grease Trap 6 KG','GREASE TRAP UTAMA LANTAI 2'),
//     ('KNR-3','Grease Trap 3 KG','RUANG SERVER')");
// }

// Handle tambah Grease Trap
if (isset($_POST['tambah_grease_trap'])) {
  $lok = mysqli_real_escape_string($conn, $_POST['lokasi']);

  // Auto Increment Kode (GT-01, GT-02, ...)
  $lastData = mysqli_fetch_assoc(mysqli_query($conn, "SELECT no_kode FROM grease_trap ORDER BY id DESC LIMIT 1"));
  if ($lastData) {
    $lastKode = $lastData['no_kode'];
    // Cari angka terakhir di dalam string (misal GT-05 -> 5)
    preg_match('/\d+$/', $lastKode, $matches);
    $num = isset($matches[0]) ? (int) $matches[0] : 0;
    $newNum = str_pad($num + 1, 2, '0', STR_PAD_LEFT);
    $kode = "GT-" . $newNum;
  } else {
    $kode = "GT-01";
  }

  $nama = "Grease Trap";

  mysqli_query($conn, "INSERT INTO grease_trap (no_kode, nama_sarana, lokasi) VALUES ('$kode','$nama','$lok')");
  header("Location: grease_trap_home.php");
  exit;
}

$keyword = isset($_GET['keyword']) ? mysqli_real_escape_string($conn, $_GET['keyword']) : '';
$where = "";
if ($keyword != '') {
  $where = "WHERE no_kode LIKE '%$keyword%' OR nama_sarana LIKE '%$keyword%' OR lokasi LIKE '%$keyword%'";
}

$listGreaseTrap = mysqli_query($conn, "SELECT * FROM grease_trap $where ORDER BY no_kode ASC");
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sarana Prasarana - Manajemen Grease Trap</title>
  <link rel="icon" type="image/png" href="assets/images/cba.png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://kit.fontawesome.com/a404219d80.js" crossorigin="anonymous"></script>
  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    :root {
      --blue: #2563eb;
      --blue-dark: #1e3a8a;
      --blue-light: #3b82f6;
    }

    body {
      background: #f4f6f9;
      font-family: 'Segoe UI', sans-serif;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    .navbar-grease_trap {
      background: linear-gradient(135deg, var(--blue-dark), var(--blue-light));
    }

    .card-grease_trap {
      border: none;
      border-radius: 16px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, .1);
    }

    .card-grease_trap-header {
      background: #fff;
      color: #333;
      border-radius: 16px 16px 0 0;
      padding: 20px 24px;
    }

    .btn-grease_trap {
      background: var(--blue);
      border: none;
      color: #fff;
    }

    .btn-grease_trap:hover {
      background: var(--blue-dark);
      color: #fff;
    }

    .grease_trap-badge {
      background: rgba(37, 99, 235, 0.1);
      color: var(--blue);
      font-weight: 600;
      padding: 4px 10px;
      border-radius: 6px;
      font-size: 13px;
    }

    .table thead th {
      background: var(--blue);
      color: #fff;
      border: none;
      white-space: nowrap;
    }

    .table tbody td {
      white-space: nowrap;
    }

    .btn-kartu {
      background: var(--blue);
      color: #fff;
      border: none;
      border-radius: 8px;
    }

    .btn-kartu:hover {
      background: var(--blue-dark);
      color: #fff;
    }

    .modal-overlay {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(0, 0, 0, .5);
      z-index: 1050;
      align-items: center;
      justify-content: center;
    }

    .modal-overlay.active {
      display: flex;
    }

    .modal-box {
      background: #fff;
      border-radius: 16px;
      padding: 24px;
      width: 95%;
      max-width: 500px;
    }

    @media (min-width: 576px) {
      .modal-box {
        padding: 32px;
      }
    }

    footer {
      background: linear-gradient(135deg, var(--blue-dark), var(--blue-light));
      color: #fff;
      margin-top: 40px;
    }

    /* Table Responsive Stack for Mobile */
    @media (max-width: 768px) {
      .table-responsive {
        border: none;
      }

      .table-hover thead {
        display: none;
      }

      .table-hover tbody tr {
        display: block;
        margin-bottom: 1.25rem;
        background: #fff;
        border: 1px solid #eef0f3;
        border-radius: 12px;
        padding: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
      }

      .table-hover tbody td {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 4px !important;
        border: none;
        border-bottom: 1px solid #f8f9fa;
        text-align: right;
      }

      /* Sembunyikan baris Nomor di mobile */
      .table-hover tbody td[data-label="No"] {
        display: none;
      }

      .table-hover tbody td:last-child {
        border-bottom: none;
        padding-top: 15px !important;
        justify-content: center;
        gap: 8px;
      }

      /* Sembunyikan label AKSI di mobile */
      .table-hover tbody td[data-label="Aksi"]::before {
        display: none;
      }

      .table-hover tbody td::before {
        content: attr(data-label);
        font-weight: 700;
        color: #888;
        text-align: left;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
      }

      .grease_trap-badge {
        margin: 0;
      }
    }
  </style>

    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#1e3a8a">
    <link rel="apple-touch-icon" href="assets/images/cba.png">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
</head>

<body>
  <nav class="navbar navbar-grease_trap fixed-top py-3 shadow">
    <div class="container-fluid d-flex justify-content-between align-items-center">
      <div class="d-flex align-items-center gap-2 text-white" style="cursor:pointer"
        onclick="window.location.href='dashboard.php'">
        <img src="assets/images/cba.png" alt="Logo CBA"
          style="height:40px; background:white; padding:4px; border-radius:8px;">
        <span class="fw-bold fs-5 d-none d-sm-inline" style="letter-spacing:1px">PERAWATAN GREASE TRAP</span>
      </div>
      <div class="d-flex align-items-center gap-2 gap-md-3">
        <span class="text-white small d-none d-md-inline"><i
            class="fa-solid fa-user me-1"></i><?= $_SESSION['username'] ?></span>
        <a href="logout.php" class="btn btn-sm btn-outline-light rounded-pill px-2 px-md-3" title="Logout">
          <i class="fa-solid fa-right-from-bracket d-inline d-sm-none"></i>
          <span class="d-none d-sm-inline"><i class="fa-solid fa-right-from-bracket me-1"></i>Logout</span>
        </a>
      </div>
    </div>
  </nav>

  <div class="container" style="margin-top:100px; padding-bottom:40px; flex:1;">
    <div class="card card-grease_trap">
      <div class="card-grease_trap-header d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div class="d-flex align-items-center gap-3">
          <a href="dashboard.php" class="btn btn-sm btn-outline-secondary rounded-pill" title="Kembali ke Dashboard">
            <i class="fa-solid fa-arrow-left me-1"></i><span class="d-none d-sm-inline"></span>
          </a>
          <div>
            <h5 class="fw-bold mb-0 text-primary"><i class="fa-solid fa-sink me-2"></i>Daftar Unit Grease Trap</h5>
            <small class="opacity-75">Kartu Riwayat Pengecekan</small>
          </div>
        </div>

        <div class="d-flex flex-column flex-sm-row align-items-stretch align-items-sm-center gap-2 mt-3 mt-sm-0">
          <form method="GET" class="d-flex m-0">
            <div class="input-group input-group-sm w-100">
              <input type="text" name="keyword" class="form-control" placeholder="Cari Kode/Lokasi..."
                value="<?= htmlspecialchars($keyword) ?>">
              <button type="submit" class="btn btn-light text-primary border"><i
                  class="fa-solid fa-search"></i></button>
              <?php if ($keyword != ''): ?>
                <a href="grease_trap_home.php" class="btn btn-secondary border"><i class="fa-solid fa-times"></i></a>
              <?php endif; ?>
            </div>
          </form>

          <?php if (($_SESSION['role'] ?? '') === 'Admin'): ?>
          <button class="btn btn-primary btn-sm px-3 rounded-pill fw-bold shadow-sm text-nowrap"
            onclick="document.getElementById('modalTambah').classList.add('active')">
            <i class="fa-solid fa-plus me-1"></i>Tambah Grease Trap
          </button>
          <?php endif; ?>
        </div>
      </div>
      <div class="card-body p-4">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead>
              <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Nama Sarana</th>
                <th>Lokasi</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php $no = 1;
              while ($row = mysqli_fetch_assoc($listGreaseTrap)): ?>
                <tr>
                  <td data-label="No"><?= $no++ ?></td>
                  <td data-label="Kode"><span class="grease_trap-badge"><?= htmlspecialchars($row['no_kode']) ?></span>
                  </td>
                  <td data-label="Nama Sarana"><?= htmlspecialchars($row['nama_sarana']) ?></td>
                  <td data-label="Lokasi">
                    <span><i
                        class="fa-solid fa-location-dot text-danger me-1"></i><?= htmlspecialchars($row['lokasi']) ?></span>
                  </td>
                  <td data-label="Aksi">
                    <a href="grease_trap_kartu.php?grease_trap_id=<?= $row['id'] ?>"
                      class="btn btn-kartu btn-sm flex-grow-1">
                      <i class="fa-solid fa-table me-1"></i><span class="d-inline">Kartu Riwayat</span>
                    </a>
                    <?php if (($_SESSION['role'] ?? '') === 'Admin'): ?>
                    <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete(<?= $row['id'] ?>)">
                      <i class="fa-solid fa-trash"></i>
                    </button>
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

  <!-- Modal Tambah Grease Trap -->
  <div class="modal-overlay" id="modalTambah">
    <div class="modal-box">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0 text-primary"><i class="fa-solid fa-sink me-2"></i>Tambah Unit Grease Trap</h5>
        <button class="btn-close" onclick="document.getElementById('modalTambah').classList.remove('active')"></button>
      </div>
      <hr>
      <form method="POST">
        <div class="mb-4">
          <label class="form-label fw-semibold">Lokasi Unit</label>
          <input type="text" class="form-control form-control-lg" name="lokasi" placeholder="Masukkan Lokasi..."
            required autofocus>
          <small class="text-secondary">Kode dan nama sarana akan dibuat otomatis.</small>
        </div>
        <div class="d-flex gap-2">
          <button type="submit" name="tambah_grease_trap" class="btn btn-primary w-100 py-2 fw-bold">
            <i class="fa-solid fa-save me-1"></i>Simpan
          </button>
          <button type="button" class="btn btn-secondary px-4"
            onclick="document.getElementById('modalTambah').classList.remove('active')">Batal</button>
        </div>
      </form>
    </div>
  </div>

  <footer class="py-3 text-center">
    &copy; <?= date('Y') ?> - Sistem Perawatan Grease Trap | Team IT Pabrik
  </footer>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    function confirmDelete(id) {
      Swal.fire({
        title: 'Hapus Data Grease Trap?',
        text: "Semua riwayat perawatan untuk Grease Trap ini akan ikut terhapus secara permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fa-solid fa-trash me-1"></i> Ya, Hapus',
        cancelButtonText: 'Batal',
        reverseButtons: true,
        customClass: {
          confirmButton: 'btn btn-danger mx-2',
          cancelButton: 'btn btn-secondary mx-2'
        },
        buttonsStyling: false
      }).then((result) => {
        if (result.isConfirmed) {
          window.location.href = 'hapus.php?hapus_grease_trap=1&id=' + id;
        }
      });
    }

    <?php if (isset($_GET['error']) && $_GET['error'] == 'duplikat'): ?>
      Swal.fire({
        icon: 'error',
        title: 'Kode Sudah Digunakan!',
        html: 'Kode <strong><?= htmlspecialchars($_GET['kode'] ?? '') ?></strong> sudah terdaftar.<br>Gunakan kode yang berbeda.',
        confirmButtonColor: '#2563eb',
        confirmButtonText: 'Oke'
      });
    <?php endif; ?>
  </script>

  <?php if (isset($_GET['pesan']) && $_GET['pesan'] == 'hapus_sukses'): ?>
    <script>
      Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: 'Data Grease Trap beserta perawatan telah dihapus.',
        showConfirmButton: false,
        timer: 2500,
        toast: true,
        position: 'top-end'
      });
    </script>
  <?php endif; ?>

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
