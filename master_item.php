<?php
include "config/koneksi.php";
session_start();
if (!isset($_SESSION['username'])) {
  header("Location: index.php");
  exit;
}
if (($_SESSION['role'] ?? '') !== 'Admin') {
  header("Location: dashboard.php");
  exit;
}

// 1. Setup tabel master_item jika belum ada (ditambah kolom is_active untuk soft delete)
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS `master_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `modul` varchar(50) NOT NULL,
  `kolom` varchar(100) NOT NULL,
  `label` varchar(200) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// 2. Seeding data awal jika tabel master_item masih kosong
$cekData = mysqli_query($conn, "SELECT COUNT(*) as total FROM master_item");
$rowCek = mysqli_fetch_assoc($cekData);
if ($rowCek['total'] == 0) {
  // Data default
  $defaultItems = [
    'apar' => [
      'label_pengisian' => 'Label pengisian ulang',
      'tekanan_pressure' => 'Tekanan (pressure) Amper',
      'safety_pin' => 'Safety pin',
      'handle' => 'Handle',
      'selang_nozzle' => 'Selang (Nozzle)',
      'dry_chemical' => 'Dry Chemical',
      'tablulan' => 'Tablulan',
      'bambu_petunjuk' => 'Bambu & petunjuk penggunaan'
    ],
    'hydrant' => [
      'valve_handle' => 'Valve Handle',
      'hose_coupling_conect' => 'Hose Coupling Conect',
      'baut_valve_handle' => 'Baut valve handle',
      'fire_hose' => 'Fire hose',
      'slang_hydrant' => 'Slang hydrant',
      'nozzle' => 'Nozzle',
      'box_hydrant' => 'Box Hydrant'
    ],
    'gedung' => [
      'dinding' => 'Dinding',
      'atap_talang' => 'Atap/Talang',
      'lantai' => 'Lantai',
      'wastafel' => 'Wastafel',
      'pintu_kaca' => 'Pintu/Kaca',
      'toilet' => 'Toilet',
      'lain_lain' => 'Lain-lain'
    ],
    'grease_trap' => [
      'kondisi_fisik' => 'Kondisi fisik grease trap',
      'kebersihan_internal' => 'Kebersihan internal',
      'pemisahan_lemak' => 'Fungsi pemisahan lemak',
      'saluran_in_out' => 'Saluran masuk dan keluar',
      'bau_kontaminasi' => 'Bau atau kontaminasi'
    ],
    'toilet' => [
      'tissue_toilet' => 'Tissue toilet selalu tersedia',
      'lantai_bersih' => 'Lantai bersih (tidak ada sampah)',
      'closet_bersih' => 'Closet bersih & tidak mampet',
      'dinding_bersih' => 'Dinding Toilet bersih',
      'kran_shower' => 'Kran/shower berfungsi dengan baik',
      'sarang_laba' => 'Tidak ada sarang laba-laba',
      'tersedia_pewangi' => 'Tersedia pewangi',
      'lap_sabun' => 'Ada lap tangan dan sabun di washtafel',
      'tempat_sampah' => 'Ada tempat sampah di dalam toilet',
      'matikan_lampu' => 'Matikan lampu toilet saat tidak digunakan'
    ]
  ];

  foreach ($defaultItems as $modul => $items) {
    foreach ($items as $kolom => $label) {
      $k = mysqli_real_escape_string($conn, $kolom);
      $l = mysqli_real_escape_string($conn, $label);
      mysqli_query($conn, "INSERT INTO master_item (modul, kolom, label, is_active) VALUES ('$modul', '$k', '$l', 1)");
    }
  }
}

// 3. Handle Tambah Item
if (isset($_POST['tambah_item'])) {
  $modul = mysqli_real_escape_string($conn, $_POST['modul']);
  $label = mysqli_real_escape_string($conn, $_POST['label']);
  // Buat slug untuk nama kolom: lowercase, replace spasi/karakter khusus dengan underscore
  $kolom = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '_', trim($label)));
  // Cek apakah kolom sudah ada di tabel master_item (termasuk yang tidak aktif)
  $cekKolom = mysqli_query($conn, "SELECT id, is_active FROM master_item WHERE modul='$modul' AND kolom='$kolom'");
  if (mysqli_num_rows($cekKolom) > 0) {
    $rowExist = mysqli_fetch_assoc($cekKolom);
    if ($rowExist['is_active'] == 0) {
       // Jika sebelumnya dihapus (soft delete), aktifkan kembali
       mysqli_query($conn, "UPDATE master_item SET is_active=1, label='$label' WHERE id={$rowExist['id']}");
       header("Location: master_item.php?modul=$modul&pesan=tambah_sukses");
       exit;
    } else {
       header("Location: master_item.php?modul=$modul&error=duplikat");
       exit;
    }
  }

  // Insert ke master_item
  mysqli_query($conn, "INSERT INTO master_item (modul, kolom, label, is_active) VALUES ('$modul', '$kolom', '$label', 1)");

  // Tambahkan kolom di tabel checklist bersangkutan
  $tabelTarget = "checklist_" . $modul;
  // Cek dulu apakah kolom sudah ada di tabel DB
  $checkCol = mysqli_query($conn, "SHOW COLUMNS FROM `$tabelTarget` LIKE '$kolom'");
  if (mysqli_num_rows($checkCol) == 0) {
      mysqli_query($conn, "ALTER TABLE `$tabelTarget` ADD COLUMN `$kolom` ENUM('Ok','Nok') DEFAULT NULL");
  }

  header("Location: master_item.php?modul=$modul&pesan=tambah_sukses");
  exit;
}

// 4. Handle Hapus Item (Soft Delete)
if (isset($_GET['hapus'])) {
  $id = (int) $_GET['hapus'];
  $modulHapus = isset($_GET['modul']) ? $_GET['modul'] : 'apar';
  // Soft delete: ubah is_active = 0
  mysqli_query($conn, "UPDATE master_item SET is_active=0 WHERE id=$id");
  // Perhatikan: Kolom di database TIDAK dihapus permanen sesuai permintaan user
  header("Location: master_item.php?modul=$modulHapus&pesan=hapus_sukses");
  exit;
}

// Handle Edit Item
if (isset($_POST['edit_item'])) {
  $id = (int)$_POST['id_item'];
  $label = mysqli_real_escape_string($conn, $_POST['label']);
  $modul = mysqli_real_escape_string($conn, $_POST['modul']);
  
  // check if label matches another item in same modul (excluding itself)
  $kolom = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '_', trim($label)));
  $cekKolom = mysqli_query($conn, "SELECT id FROM master_item WHERE modul='$modul' AND kolom='$kolom' AND id != $id AND is_active=1");
  if (mysqli_num_rows($cekKolom) > 0) {
    header("Location: master_item.php?modul=$modul&error=duplikat");
    exit;
  }
  
  mysqli_query($conn, "UPDATE master_item SET label='$label' WHERE id=$id");
  header("Location: master_item.php?modul=$modul&pesan=edit_sukses");
  exit;
}

// Ambil data
$modulFilter = isset($_GET['modul']) ? mysqli_real_escape_string($conn, $_GET['modul']) : 'apar';
$items = mysqli_query($conn, "SELECT * FROM master_item WHERE modul='$modulFilter' AND is_active=1 ORDER BY id ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sarana Prasarana - Master Item Pengecekan</title>
  <link rel="icon" type="image/png" href="assets/images/cba.png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://kit.fontawesome.com/a404219d80.js" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    :root { --blue: #2563eb; --blue-dark: #1e3a8a; --blue-light: #3b82f6; }
    body { background: #f4f6f9; font-family: 'Segoe UI', sans-serif; min-height: 100vh; display: flex; flex-direction: column; }
    .navbar-custom { background: linear-gradient(135deg, var(--blue-dark), var(--blue-light)); }
    .card-custom { border: none; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,.1); }
    .card-header-custom { background: #fff; color: #333; border-radius: 16px 16px 0 0; padding: 20px 24px; border-bottom: 1px solid #eee; }
    .table thead th { background: var(--blue); color: #fff; border: none; white-space: nowrap; }
    .nav-pills .nav-link { color: #555; border-radius: 8px; margin-right: 5px; margin-bottom: 5px; }
    .nav-pills .nav-link.active { background-color: var(--blue); }
  </style>
</head>
<body>
  <nav class="navbar navbar-custom fixed-top py-3 shadow">
    <div class="container-fluid d-flex justify-content-between align-items-center">
      <div class="d-flex align-items-center gap-2 text-white" style="cursor:pointer" onclick="window.location.href='dashboard.php'">
        <img src="assets/images/cba.png" alt="Logo CBA" style="height:40px; background:white; padding:4px; border-radius:8px;">
        <span class="fw-bold fs-5 d-none d-sm-inline" style="letter-spacing:1px">PENGATURAN ITEM</span>
      </div>
      <div class="d-flex align-items-center gap-2 gap-md-3">
        <span class="text-white small d-none d-md-inline"><i class="fa-solid fa-user me-1"></i><?= $_SESSION['username'] ?></span>
        <a href="logout.php" class="btn btn-sm btn-outline-light rounded-pill px-2 px-md-3" title="Logout">
          <i class="fa-solid fa-right-from-bracket d-inline d-sm-none"></i>
          <span class="d-none d-sm-inline"><i class="fa-solid fa-right-from-bracket me-1"></i>Logout</span>
        </a>
      </div>
    </div>
  </nav>

  <div class="container" style="margin-top:100px; padding-bottom:40px; flex:1;">
    <div class="card card-custom">
      <div class="card-header-custom d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div class="d-flex align-items-center gap-3">
          <a href="dashboard.php" class="btn btn-sm btn-outline-secondary rounded-pill" title="Kembali ke Dashboard">
            <i class="fa-solid fa-arrow-left me-1"></i>
          </a>
          <div>
            <h5 class="fw-bold mb-0 text-primary"><i class="fa-solid fa-list-check me-2"></i>Master Item Pengecekan</h5>
            <small class="opacity-75">Kelola daftar item yang dicek pada setiap sarana</small>
          </div>
        </div>
        <button class="btn btn-primary btn-sm px-3 rounded-pill fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
          <i class="fa-solid fa-plus me-1"></i>Tambah Item
        </button>
      </div>
      
      <div class="card-body p-4">
        <!-- Tab Modul -->
        <ul class="nav nav-pills mb-4">
          <li class="nav-item">
            <a class="nav-link <?= $modulFilter == 'apar' ? 'active' : '' ?>" href="?modul=apar"><i class="fa-solid fa-fire-extinguisher me-1"></i>APAR</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= $modulFilter == 'hydrant' ? 'active' : '' ?>" href="?modul=hydrant"><i class="fa-solid fa-fire me-1"></i>Hydrant</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= $modulFilter == 'gedung' ? 'active' : '' ?>" href="?modul=gedung"><i class="fa-solid fa-building me-1"></i>Gedung</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= $modulFilter == 'grease_trap' ? 'active' : '' ?>" href="?modul=grease_trap"><i class="fa-solid fa-sink me-1"></i>Grease Trap</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= $modulFilter == 'toilet' ? 'active' : '' ?>" href="?modul=toilet"><i class="fa-solid fa-restroom me-1"></i>Toilet</a>
          </li>
        </ul>


        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead>
              <tr>
                <th width="50">No</th>
                <th>Nama Item Pengecekan</th>
                <th width="100">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php 
              $no = 1;
              if (mysqli_num_rows($items) > 0):
                while ($row = mysqli_fetch_assoc($items)): ?>
                <tr>
                  <td><?= $no++ ?></td>
                  <td class="fw-semibold"><?= htmlspecialchars($row['label']) ?></td>
                  <td>
                    <button type="button" class="btn btn-warning btn-sm text-white" onclick="openEditModal(<?= $row['id'] ?>, '<?= htmlspecialchars(addslashes($row['label'])) ?>', '<?= htmlspecialchars($row['modul']) ?>')" title="Edit Nama Item">
                      <i class="fa-solid fa-pen-to-square"></i>
                    </button>
                    <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete(<?= $row['id'] ?>)" title="Hapus (Sembunyikan)">
                      <i class="fa-solid fa-trash"></i>
                    </button>
                  </td>
                </tr>
                <?php endwhile; 
              else: ?>
                <tr>
                  <td colspan="3" class="text-center text-muted py-4">Belum ada item pengecekan untuk modul ini.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Tambah Item -->
  <div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content" style="border-radius:16px; border:none;">
        <div class="modal-header border-bottom-0 pb-0">
          <h5 class="modal-title fw-bold text-primary"><i class="fa-solid fa-plus-circle me-2"></i>Tambah Item Baru</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form method="POST">
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label fw-semibold">Modul/Kategori</label>
              <select name="modul" class="form-select" required>
                <option value="apar" <?= $modulFilter == 'apar' ? 'selected' : '' ?>>APAR</option>
                <option value="hydrant" <?= $modulFilter == 'hydrant' ? 'selected' : '' ?>>Hydrant</option>
                <option value="gedung" <?= $modulFilter == 'gedung' ? 'selected' : '' ?>>Gedung</option>
                <option value="grease_trap" <?= $modulFilter == 'grease_trap' ? 'selected' : '' ?>>Grease Trap</option>
                <option value="toilet" <?= $modulFilter == 'toilet' ? 'selected' : '' ?>>Toilet</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold">Nama Item Pengecekan</label>
              <input type="text" class="form-control" name="label" placeholder="Contoh: Kondisi Selang" required>
            </div>
          </div>
          <div class="modal-footer border-top-0 pt-0">
            <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
            <button type="submit" name="tambah_item" class="btn btn-primary rounded-pill px-4">Simpan</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal Edit Item -->
  <div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content" style="border-radius:16px; border:none;">
        <div class="modal-header border-bottom-0 pb-0">
          <h5 class="modal-title fw-bold text-primary"><i class="fa-solid fa-pen-to-square me-2"></i>Edit Item Pengecekan</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form method="POST">
          <input type="hidden" name="id_item" id="edit_id">
          <input type="hidden" name="modul" id="edit_modul">
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label fw-semibold">Nama Item Pengecekan</label>
              <input type="text" class="form-control" name="label" id="edit_label" required>
            </div>
          </div>
          <div class="modal-footer border-top-0 pt-0">
            <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
            <button type="submit" name="edit_item" class="btn btn-primary rounded-pill px-4">Simpan Perubahan</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    function openEditModal(id, label, modul) {
      document.getElementById('edit_id').value = id;
      document.getElementById('edit_label').value = label;
      document.getElementById('edit_modul').value = modul;
      new bootstrap.Modal(document.getElementById('modalEdit')).show();
    }

    function confirmDelete(id) {
      Swal.fire({
        title: 'Hapus Item?',
        text: "Item ini tidak akan tampil lagi di form pengisian.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
      }).then((result) => {
        if (result.isConfirmed) {
          window.location.href = '?modul=<?= $modulFilter ?>&hapus=' + id;
        }
      });
    }

    <?php if (isset($_GET['pesan']) && $_GET['pesan'] == 'tambah_sukses'): ?>
      Swal.fire({ icon: 'success', title: 'Berhasil', text: 'Item baru ditambahkan', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
    <?php endif; ?>
    <?php if (isset($_GET['pesan']) && $_GET['pesan'] == 'edit_sukses'): ?>
      Swal.fire({ icon: 'success', title: 'Berhasil', text: 'Item berhasil diubah', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
    <?php endif; ?>
    <?php if (isset($_GET['pesan']) && $_GET['pesan'] == 'hapus_sukses'): ?>
      Swal.fire({ icon: 'success', title: 'Terhapus', text: 'Item berhasil terhapus', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
    <?php endif; ?>
    <?php if (isset($_GET['error']) && $_GET['error'] == 'duplikat'): ?>
      Swal.fire({ icon: 'error', title: 'Gagal', text: 'Nama item ini sudah ada.' });
    <?php endif; ?>
  </script>
</body>
</html>
