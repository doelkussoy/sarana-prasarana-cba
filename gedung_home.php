<?php
include "config/koneksi.php";
session_start();
if (!isset($_SESSION['username'])) {
  header("Location: index.php");
  exit;
}

// Setup tabel jika belum ada
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS `gedung` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `no_kode` varchar(50) NOT NULL,
  `nama_sarana` varchar(100) DEFAULT NULL,
  `lokasi` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

mysqli_query($conn, "CREATE TABLE IF NOT EXISTS `checklist_gedung` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gedung_id` int(11) NOT NULL,
  `tahun` int(4) NOT NULL,
  `bulan` tinyint(2) NOT NULL,
  `tanggal_cek` date DEFAULT NULL,
  `label_pengisian` enum('Ok','Nok') DEFAULT NULL,
  `tekanan_pressure` enum('Ok','Nok') DEFAULT NULL,
  `safety_pin` enum('Ok','Nok') DEFAULT NULL,
  `handle` enum('Ok','Nok') DEFAULT NULL,
  `selang_nozzle` enum('Ok','Nok') DEFAULT NULL,
  `dry_chemical` enum('Ok','Nok') DEFAULT NULL,
  `tablulan` enum('Ok','Nok') DEFAULT NULL,
  `bambu_petunjuk` enum('Ok','Nok') DEFAULT NULL,
  `paraf` varchar(100) DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `users_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// Cek apakah ada data Gedung, jika tidak insert sample
$cekApar = mysqli_query($conn, "SELECT COUNT(*) as total FROM gedung");
$rowCek = mysqli_fetch_assoc($cekApar);
// if ($rowCek['total'] == 0) {
//   mysqli_query($conn, "INSERT INTO `gedung` (`no_kode`, `nama_sarana`, `lokasi`) VALUES
//     ('KNR-1','Gedung 9 KG','GEDUNG UTAMA LANTAI 1'),
//     ('KNR-2','Gedung 6 KG','GEDUNG UTAMA LANTAI 2'),
//     ('KNR-3','Gedung 3 KG','RUANG SERVER')");
// }

// Handle tambah Gedung
if (isset($_POST['tambah_gedung'])) {
  $lok = mysqli_real_escape_string($conn, $_POST['lokasi']);

  // Check duplicate lokasi
  $cekLokasi = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id FROM gedung WHERE lokasi='$lok'"));
  if ($cekLokasi) {
      header("Location: gedung_home.php?error=lokasi_duplikat&lokasi=" . urlencode($lok));
      exit;
  }

  $kodeRes = mysqli_query($conn, "SELECT MAX(CAST(SUBSTRING(no_kode,5) AS UNSIGNED)) AS max_num FROM gedung");
  $kodeRow = mysqli_fetch_assoc($kodeRes);
  $nextNum = ($kodeRow['max_num'] ?? 0) + 1;
  $newNum = str_pad($nextNum, 2, '0', STR_PAD_LEFT);
  $kode = "GDG-" . $newNum;

  $nama = "Gedung";

  mysqli_query($conn, "INSERT INTO gedung (no_kode, nama_sarana, lokasi) VALUES ('$kode','$nama','$lok')");
  header("Location: gedung_home.php");
  exit;
}

// Handle edit Gedung
if (isset($_POST['edit_gedung'])) {
  $id = (int)$_POST['id_gedung'];
  $lokasi_baru = mysqli_real_escape_string($conn, $_POST['lokasi_baru']);

  // Check duplicate lokasi exclude self
  $cekLokasi = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id FROM gedung WHERE lokasi='$lokasi_baru' AND id != $id"));
  if ($cekLokasi) {
      header("Location: gedung_home.php?error=lokasi_duplikat&lokasi=" . urlencode($lokasi_baru));
      exit;
  }

  mysqli_query($conn, "UPDATE gedung SET lokasi='$lokasi_baru' WHERE id=$id");
  header("Location: gedung_home.php?pesan=edit_sukses");
  exit;
}

$keyword = isset($_GET['keyword']) ? mysqli_real_escape_string($conn, $_GET['keyword']) : '';
$where = "";
if ($keyword != '') {
  $where = "WHERE no_kode LIKE '%$keyword%' OR nama_sarana LIKE '%$keyword%' OR lokasi LIKE '%$keyword%'";
}

$listGedung = mysqli_query($conn, "SELECT * FROM gedung $where ORDER BY no_kode ASC");
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sarana Prasarana - Manajemen Gedung</title>
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

    .navbar-gedung {
      background: linear-gradient(135deg, var(--blue-dark), var(--blue-light));
    }

    .card-gedung {
      border: none;
      border-radius: 16px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, .1);
    }

    .card-gedung-header {
      background: #fff;
      color: #333;
      border-radius: 16px 16px 0 0;
      padding: 20px 24px;
    }

    .btn-gedung {
      background: var(--blue);
      border: none;
      color: #fff;
    }

    .btn-gedung:hover {
      background: var(--blue-dark);
      color: #fff;
    }

    .gedung-badge {
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

      .gedung-badge {
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
  <nav class="navbar navbar-gedung fixed-top py-3 shadow">
    <div class="container-fluid d-flex justify-content-between align-items-center">
      <div class="d-flex align-items-center gap-2 text-white" style="cursor:pointer"
        onclick="window.location.href='dashboard.php'">
        <img src="assets/images/cba.png" alt="Logo CBA"
          style="height:40px; background:white; padding:4px; border-radius:8px;">
        <span class="fw-bold fs-5 d-none d-sm-inline" style="letter-spacing:1px">PERAWATAN GEDUNG</span>
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
    <div class="card card-gedung">
      <div class="card-gedung-header d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div class="d-flex align-items-center gap-3">
          <a href="dashboard.php" class="btn btn-sm btn-outline-secondary rounded-pill" title="Kembali ke Dashboard">
            <i class="fa-solid fa-arrow-left me-1"></i><span class="d-none d-sm-inline"></span>
          </a>
          <div>
            <h5 class="fw-bold mb-0 text-primary"><i class="fa-solid fa-building-circle-check me-2"></i>Daftar Unit
              Gedung</h5>
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
                <a href="gedung_home.php" class="btn btn-secondary border"><i class="fa-solid fa-times"></i></a>
              <?php endif; ?>
            </div>
          </form>

          <?php if (($_SESSION['role'] ?? '') === 'Admin'): ?>
          <button class="btn btn-primary btn-sm px-3 rounded-pill fw-bold shadow-sm text-nowrap"
            onclick="document.getElementById('modalTambah').classList.add('active')">
            <i class="fa-solid fa-plus me-1"></i>Tambah Gedung
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
              while ($row = mysqli_fetch_assoc($listGedung)): ?>
                <tr>
                  <td data-label="No"><?= $no++ ?></td>
                  <td data-label="Kode"><span class="gedung-badge"><?= htmlspecialchars($row['no_kode']) ?></span></td>
                  <td data-label="Nama Sarana"><?= htmlspecialchars($row['nama_sarana']) ?></td>
                  <td data-label="Lokasi">
                    <span><i
                        class="fa-solid fa-location-dot text-danger me-1"></i><?= htmlspecialchars($row['lokasi']) ?></span>
                  </td>
                  <td data-label="Aksi">
                    <a href="gedung_kartu.php?gedung_id=<?= $row['id'] ?>" class="btn btn-kartu btn-sm flex-grow-1">
                      <i class="fa-solid fa-table me-1"></i><span class="d-inline">Kartu Riwayat</span>
                    </a>
                    <?php if (($_SESSION['role'] ?? '') === 'Admin'): ?>
                    <button type="button" class="btn btn-warning btn-sm text-white" onclick="openEditModal(<?= $row['id'] ?>, '<?= htmlspecialchars(addslashes($row['lokasi'])) ?>')" title="Edit Lokasi">
                      <i class="fa-solid fa-pen-to-square"></i>
                    </button>
                    <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete(<?= $row['id'] ?>)" title="Hapus">
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

  <!-- Modal Tambah Gedung -->
  <div class="modal-overlay" id="modalTambah">
    <div class="modal-box">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0 text-primary"><i class="fa-solid fa-building-circle-check me-2"></i>Tambah Unit Gedung
        </h5>
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
          <button type="submit" name="tambah_gedung" class="btn btn-primary w-100 py-2 fw-bold">
            <i class="fa-solid fa-save me-1"></i>Simpan
          </button>
          <button type="button" class="btn btn-secondary px-4"
            onclick="document.getElementById('modalTambah').classList.remove('active')">Batal</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Modal Edit Gedung -->
  <div class="modal-overlay" id="modalEdit">
    <div class="modal-box">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0 text-primary"><i class="fa-solid fa-pen-to-square me-2"></i>Edit Lokasi Gedung</h5>
        <button class="btn-close" onclick="document.getElementById('modalEdit').classList.remove('active')"></button>
      </div>
      <hr>
      <form method="POST">
        <input type="hidden" name="id_gedung" id="edit_id">
        <div class="mb-4">
          <label class="form-label fw-semibold">Lokasi Unit Baru</label>
          <input type="text" class="form-control form-control-lg" name="lokasi_baru" id="edit_lokasi" placeholder="Masukkan Lokasi Baru..."
            required autofocus>
        </div>
        <div class="d-flex gap-2">
          <button type="submit" name="edit_gedung" class="btn btn-primary w-100 py-2 fw-bold">
            <i class="fa-solid fa-save me-1"></i>Simpan Perubahan
          </button>
          <button type="button" class="btn btn-secondary px-4"
            onclick="document.getElementById('modalEdit').classList.remove('active')">Batal</button>
        </div>
      </form>
    </div>
  </div>

  <footer class="py-3 text-center">
    &copy; <?= date('Y') ?> - Sistem Perawatan Gedung | Team IT Pabrik
  </footer>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    function openEditModal(id, lokasi) {
      document.getElementById('edit_id').value = id;
      document.getElementById('edit_lokasi').value = lokasi;
      document.getElementById('modalEdit').classList.add('active');
    }

    function confirmDelete(id) {
      Swal.fire({
        title: 'Hapus Data Gedung?',
        text: "Semua riwayat perawatan untuk Gedung ini akan ikut terhapus secara permanen!",
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
          window.location.href = 'hapus.php?hapus_gedung=1&id=' + id;
        }
      });
    }

    <?php if (isset($_GET['error'])): ?>
      <?php if ($_GET['error'] == 'duplikat'): ?>
        Swal.fire({
          icon: 'error',
          title: 'Kode Sudah Digunakan!',
          html: 'Kode <strong><?= htmlspecialchars($_GET['kode'] ?? '') ?></strong> sudah terdaftar.<br>Gunakan kode yang berbeda.',
          confirmButtonColor: '#2563eb',
          confirmButtonText: 'Oke'
        });
      <?php elseif ($_GET['error'] == 'lokasi_duplikat'): ?>
        Swal.fire({
          icon: 'error',
          title: 'Lokasi Sudah Digunakan!',
          html: 'Lokasi <strong><?php echo htmlspecialchars($_GET['lokasi'] ?? '') ?></strong> sudah terdaftar untuk sarana lain.<br>Gunakan lokasi yang berbeda.',
          confirmButtonColor: '#2563eb',
          confirmButtonText: 'Oke'
        });
      <?php endif; ?>
    <?php endif; ?>
  </script>

  <?php if (isset($_GET['pesan'])): ?>
    <?php if ($_GET['pesan'] == 'hapus_sukses'): ?>
      <script>
        Swal.fire({
          icon: 'success',
          title: 'Berhasil!',
          text: 'Data Gedung beserta perawatan telah dihapus.',
          showConfirmButton: false,
          timer: 2500,
          toast: true,
          position: 'top-end'
        });
      </script>
    <?php elseif ($_GET['pesan'] == 'edit_sukses'): ?>
      <script>
        Swal.fire({
          icon: 'success',
          title: 'Berhasil!',
          text: 'Lokasi telah diperbarui.',
          showConfirmButton: false,
          timer: 2500,
          toast: true,
          position: 'top-end'
        });
      </script>
    <?php endif; ?>
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
