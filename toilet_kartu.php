<?php
include "config/koneksi.php";
include "config/helpers.php";
session_start();
if (!isset($_SESSION['username'])) {
  header("Location: index.php");
  exit;
}

$toilet_id = isset($_GET['toilet_id']) ? (int) $_GET['toilet_id'] : 0;
$tahun = isset($_GET['tahun']) ? (int) $_GET['tahun'] : (int) date('Y');
$bulan = isset($_GET['bulan']) ? (int) $_GET['bulan'] : (int) date('n');

$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
$bulanNama = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

// Ambil data Toilet
$toiletRow = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM toilet_unit WHERE id=$toilet_id"));
if (!$toiletRow) {
  header("Location: toilet_home.php");
  exit;
}

// Ambil daftar item dari master_item
$items = [];
$fields = [];
$resItems = mysqli_query($conn, "SELECT kolom, label FROM master_item WHERE modul='toilet' AND is_active=1 ORDER BY id ASC");
if (mysqli_num_rows($resItems) == 0) {
  // Fallback
  $defaultItems = [
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
  ];
  foreach ($defaultItems as $k => $l) {
    mysqli_query($conn, "INSERT IGNORE INTO master_item (modul, kolom, label, is_active) VALUES ('toilet', '$k', '$l', 1)");
    $items[$k] = $l;
    $fields[] = $k;
  }
} else {
  while ($rItem = mysqli_fetch_assoc($resItems)) {
    $items[$rItem['kolom']] = $rItem['label'];
    $fields[] = $rItem['kolom'];
  }
}

// Handle simpan perawatan
if (isset($_POST['simpan_checklist'])) {
  $tgl = (int) $_POST['tanggal'];
  $fullDate = "$tahun-" . str_pad($bulan, 2, '0', STR_PAD_LEFT) . "-" . str_pad($tgl, 2, '0', STR_PAD_LEFT);
  $vals = [];
  foreach ($fields as $f) {
    $vals[$f] = isset($_POST[$f]) ? mysqli_real_escape_string($conn, $_POST[$f]) : null;
  }
  $paraf = mysqli_real_escape_string($conn, $_POST['paraf'] ?? '');
  $catatan = mysqli_real_escape_string($conn, $_POST['catatan'] ?? '');
  $uid = (int) $_SESSION['id'];

  // Handle Upload Foto
  $foto_name = null;
  $cekLama = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id, foto FROM checklist_toilet WHERE toilet_id=$toilet_id AND tanggal_cek='$fullDate'"));
  if ($cekLama) {
    $foto_name = $cekLama['foto'];
  }

  if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
    $target_dir = "uploads/";
    $ext = pathinfo($_FILES["foto"]["name"], PATHINFO_EXTENSION);
    if (empty($ext)) {
      $ext = "jpg";
    }
    $new_name = "TOILET_" . $toilet_id . "_" . str_replace('-', '', $fullDate) . "_" . time() . "." . $ext;
    if (compressImage($_FILES["foto"]["tmp_name"], $target_dir . $new_name)) {
      $foto_name = $new_name;
    }
  }

  if (!$foto_name) {
    header("Location: toilet_kartu.php?toilet_id=$toilet_id&tahun=$tahun&bulan=$bulan&error=foto_wajib");
    exit;
  }

  if ($cekLama) {
    $setArr = [];
    foreach ($fields as $f) {
      $v = $vals[$f];
      $setArr[] = "`$f`=" . ($v ? "'$v'" : "NULL");
    }
    $setArr[] = "`paraf`='$paraf'";
    $setArr[] = "`catatan`='$catatan'";
    $setArr[] = "`foto`=" . ($foto_name ? "'$foto_name'" : "NULL");
    $set = implode(',', $setArr);
    mysqli_query($conn, "UPDATE checklist_toilet SET $set WHERE id={$cekLama['id']}");
  } else {
    $fStr = "toilet_id,tahun,bulan,tanggal_cek," . implode(',', $fields) . ",paraf,catatan,users_id,foto";
    $vArr = [];
    foreach ($fields as $f) {
      $vArr[] = $vals[$f] ? "'{$vals[$f]}'" : 'NULL';
    }
    $ft = $foto_name ? "'$foto_name'" : 'NULL';
    $vStr = "$toilet_id,$tahun,$bulan,'$fullDate'," . implode(',', $vArr) . ",'$paraf','$catatan',$uid,$ft";
    mysqli_query($conn, "INSERT INTO checklist_toilet ($fStr) VALUES ($vStr)");
  }
  header("Location: toilet_kartu.php?toilet_id=$toilet_id&tahun=$tahun&bulan=$bulan");
  exit;
}

// Handle hapus baris (hanya Admin)
if (isset($_GET['hapus'])) {
  if (($_SESSION['role'] ?? '') !== 'Admin') {
    header("Location: toilet_kartu.php?toilet_id=$toilet_id&tahun=$tahun&bulan=$bulan");
    exit;
  }
  mysqli_query($conn, "DELETE FROM checklist_toilet WHERE id=" . (int) $_GET['hapus'] . " AND toilet_id=$toilet_id");
  header("Location: toilet_kartu.php?toilet_id=$toilet_id&tahun=$tahun&bulan=$bulan");
  exit;
}

// Ambil semua perawatan bulan ini
$rows = [];
$res = mysqli_query($conn, "SELECT * FROM checklist_toilet WHERE toilet_id=$toilet_id AND tahun=$tahun AND bulan=$bulan ORDER BY tanggal_cek ASC");
while ($r = mysqli_fetch_assoc($res)) {
  $d = (int) date('j', strtotime($r['tanggal_cek']));
  $rows[$d] = $r;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kartu Riwayat Toilet - <?= htmlspecialchars($toiletRow['no_kode']) ?></title>
  <link rel="icon" type="image/png" href="assets/images/cba.png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://kit.fontawesome.com/a404219d80.js" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    :root {
      --blue: #2563eb;
      --blue-dark: #1e3a8a;
      --blue-light: #3b82f6;
    }

    body {
      background: #f0f2f5;
      font-family: 'Segoe UI', sans-serif;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    .navbar-toilet {
      background: linear-gradient(135deg, var(--blue-dark), var(--blue-light));
    }

    .kartu-header {
      background: linear-gradient(135deg, var(--blue-dark), var(--blue-light));
      color: #fff;
      border-radius: 16px 16px 0 0;
      padding: 20px 28px;
    }

    .kartu-body {
      background: #fff;
      border-radius: 0 0 16px 16px;
      padding: 24px;
    }

    .info-box {
      background: #fff8f8;
      border: 1px solid #fcc;
      border-radius: 10px;
      padding: 14px 20px;
      margin-bottom: 20px;
    }

    .info-label {
      font-size: 12px;
      color: #888;
      font-weight: 600;
      text-transform: uppercase;
    }

    .info-val {
      font-size: 15px;
      font-weight: 700;
      color: #222;
    }

    .tbl-kartu {
      font-size: 12px;
      border-collapse: collapse;
      width: 100%;
    }

    .tbl-kartu th,
    .tbl-kartu td {
      border: 1px solid #ddd;
      padding: 5px 7px;
      text-align: center;
      vertical-align: middle;
    }

    .tbl-kartu thead th {
      background: var(--blue);
      color: #fff;
    }

    .tbl-kartu .th-item {
      background: var(--blue);
      color: #fff;
      text-align: left;
      padding-left: 10px;
      white-space: nowrap;
      position: sticky;
      left: 0;
      z-index: 2;
    }

    .tbl-kartu .th-bulan {
      background: var(--blue-dark);
      color: #fff;
    }

    .ok-cell {
      background: #d4edda;
      color: #155724;
      font-weight: 700;
    }

    .nok-cell {
      background: #f8d7da;
      color: #721c24;
      font-weight: 700;
    }

    .empty-cell {
      color: #ccc;
    }

    .badge-kode {
      background: var(--blue);
      color: #fff;
      border-radius: 8px;
      padding: 2px 12px;
      font-size: 13px;
    }

    .modal-overlay {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(0, 0, 0, .55);
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
      padding: 28px;
      width: 95%;
      max-width: 580px;
      max-height: 90vh;
      overflow-y: auto;
    }

    footer {
      background: linear-gradient(135deg, var(--blue-dark), var(--blue-light));
      color: #fff;
      margin-top: 40px;
    }

    .print-only-header {
      display: none;
    }

    @media print {
      @page {
        size: landscape;
        margin: 0;
      }

      body {
        background: #fff;
        padding: 1cm;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
      }

      .no-print {
        display: none !important;
      }

      .container-fluid {
        margin-top: 0 !important;
        padding: 0 !important;
        max-width: 100% !important;
      }

      .kartu-body,
      .kartu-header {
        box-shadow: none !important;
        border-radius: 0 !important;
      }

      .kartu-header {
        padding: 15px !important;
      }

      .kartu-body {
        padding: 15px 0 0 0 !important;
      }

      .tbl-kartu th,
      .tbl-kartu td {
        border: 1px solid #666 !important;
      }

      .info-box {
        border: 1px solid #aaa !important;
        background: #fff !important;
        margin-bottom: 15px !important;
      }

      .print-only-header {
        display: flex !important;
        justify-content: space-between;
        font-size: 12px;
        color: #000;
        margin-bottom: 20px;
        font-family: Arial, sans-serif;
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
  <nav class="navbar navbar-toilet fixed-top py-3 shadow no-print">
    <div class="container-fluid d-flex justify-content-between align-items-center">
      <div class="d-flex align-items-center gap-2 text-white" style="cursor:pointer"
        onclick="window.location.href='dashboard.php'">
        <img src="assets/images/cba.png" alt="Logo CBA"
          style="height:40px; background:white; padding:4px; border-radius:8px;">
        <span class="fw-bold fs-5 d-none d-sm-inline" style="letter-spacing:1px">PERAWATAN TOILET</span>
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

  <div class="container-fluid" style="margin-top:90px;padding-bottom:30px;max-width:1400px;flex:1">

    <div class="print-only-header">
      <div style="width:33%; text-align:left;"><?= date('d/m/Y, H:i') ?></div>
      <div style="width:33%; text-align:center;">Kartu Riwayat Toilet - <?= htmlspecialchars($toiletRow['no_kode']) ?></div>
      <div style="width:33%; text-align:right;"></div>
    </div>

    <!-- Kontrol atas -->
    <div class="d-flex flex-wrap gap-2 align-items-center mb-3 no-print">
      <a href="toilet_home.php" class="btn btn-sm btn-outline-secondary rounded-pill">
        <i class="fa-solid fa-arrow-left me-1"></i>Kembali
      </a>
      <form method="GET" class="d-flex align-items-center gap-2 ms-auto">
        <input type="hidden" name="toilet_id" value="<?= $toilet_id ?>">
        <label class="text-secondary fw-semibold small mb-0">Bulan:</label>
        <select name="bulan" class="form-select form-select-sm" style="width:120px" onchange="this.form.submit()">
          <?php for ($b = 1; $b <= 12; $b++): ?>
            <option value="<?= $b ?>" <?= $b == $bulan ? 'selected' : '' ?>><?= $bulanNama[$b] ?></option>
          <?php endfor; ?>
        </select>
        <label class="text-secondary fw-semibold small mb-0">Tahun:</label>
        <select name="tahun" class="form-select form-select-sm" style="width:85px" onchange="this.form.submit()">
          <?php for ($y = 2026; $y <= 2030; $y++): ?>
            <option value="<?= $y ?>" <?= $y == $tahun ? 'selected' : '' ?>><?= $y ?></option>
          <?php endfor; ?>
        </select>
      </form>
      <button class="btn btn-sm btn-success rounded-pill no-print" onclick="window.print()">
        <i class="fa-solid fa-print me-1"></i>Cetak
      </button>
      <button class="btn btn-sm rounded-pill no-print" style="background:var(--blue);color:#fff"
        onclick="bukaEdit(<?= (int)date('j') ?>)">
        <i class="fa-solid fa-plus me-1"></i>Isi Harian
      </button>
    </div>

    <!-- Kartu Riwayat -->
    <div style="border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,.12)">
      <div class="kartu-header">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
          <div>
            <div class="fw-bold fs-5 mb-1">KARTU RIWAYAT PERAWATAN TOILET</div>
            <small class="opacity-80">CBA/GA/E-TLT &nbsp;|&nbsp; Rev: 00</small>
          </div>
          <div class="text-end">
            <div class="info-label text-white opacity-75">Periode</div>
            <div class="fw-bold fs-5"><?= $bulanNama[$bulan] ?> <?= $tahun ?></div>
          </div>
        </div>
      </div>
      <div class="kartu-body">
        <div class="info-box">
          <div class="row g-2">
            <div class="col-12 col-md-4">
              <div class="info-label">Kode</div>
              <div class="info-val"><span class="badge-kode"><?= htmlspecialchars($toiletRow['no_kode']) ?></span></div>
            </div>
            <div class="col-12 col-md-4">
              <div class="info-label">Nama Sarana</div>
              <div class="info-val"><?= htmlspecialchars($toiletRow['nama_sarana']) ?></div>
            </div>
            <div class="col-12 col-md-4">
              <div class="info-label">Lokasi</div>
              <div class="info-val"><i class="fa-solid fa-location-dot text-danger me-1"></i><?= htmlspecialchars($toiletRow['lokasi']) ?></div>
            </div>
          </div>
        </div>

        <div class="table-responsive" style="overflow-x: auto;">
          <table class="tbl-kartu">
            <thead>
              <tr>
                <th rowspan="1" style="width:30px">NO</th>
                <th rowspan="1" class="th-item" style="min-width:200px">PENGECEKAN</th>
                <?php for ($d = 1; $d <= $daysInMonth; $d++): ?>
                  <th class="th-bulan" style="min-width: 28px; font-size:11px; padding:4px;"><?= $d ?></th>
                <?php endfor; ?>
              </tr>
            </thead>
            <tbody>
              <?php $no = 1;
              foreach ($items as $key => $label): ?>
                <tr>
                  <td><?= $no++ ?></td>
                  <td class="th-item"><?= htmlspecialchars($label) ?></td>
                  <?php for ($d = 1; $d <= $daysInMonth; $d++):
                    $r = $rows[$d] ?? null;
                    $val = $r ? $r[$key] : null;
                    ?>
                    <td class="<?= $val == 'Ok' ? 'ok-cell' : ($val == 'Nok' ? 'nok-cell' : '') ?>" style="font-size:12px; padding:2px;">
                      <?= $val == 'Ok' ? '✓' : ($val == 'Nok' ? '✗' : '') ?>
                    </td>
                  <?php endfor; ?>
                </tr>
              <?php endforeach; ?>
              
              <!-- Paraf -->
              <tr>
                <td colspan="2" class="th-item fw-bold">Paraf Pemeriksa</td>
                <?php for ($d = 1; $d <= $daysInMonth; $d++):
                  $r = $rows[$d] ?? null;
                  $paraf = $r ? htmlspecialchars($r['paraf']) : '';
                  ?>
                  <td style="font-size:9px; font-style:italic; padding:2px;">
                    <?= $paraf ?: '<span class="empty-cell">-</span>' ?>
                  </td>
                <?php endfor; ?>
              </tr>
              
              <!-- Foto -->
              <tr class="no-print">
                <td colspan="2" class="th-item fw-bold">Foto Bukti</td>
                <?php for ($d = 1; $d <= $daysInMonth; $d++):
                  $r = $rows[$d] ?? null;
                  $foto = $r ? $r['foto'] : '';
                  ?>
                  <td class="text-center" style="padding: 2px;">
                    <?php if ($foto): ?>
                      <i class="fa-solid fa-image text-primary" style="cursor:pointer;" title="Lihat Foto" onclick="previewFoto('uploads/<?= $foto ?>')"></i>
                    <?php else: ?>
                      <span class="empty-cell" style="font-size:9px">-</span>
                    <?php endif; ?>
                  </td>
                <?php endfor; ?>
              </tr>
              
              <!-- Aksi Edit -->
              <tr class="no-print">
                <td colspan="2" class="th-item fw-bold" style="background:#f8f9fa; color:#333;">Aksi</td>
                <?php for ($d = 1; $d <= $daysInMonth; $d++):
                  $isFilled = isset($rows[$d]);
                  ?>
                  <td class="text-center" style="padding: 2px;">
                    <button class="btn btn-sm <?= $isFilled ? 'btn-success' : 'btn-outline-secondary' ?>" style="padding: 1px 4px; font-size: 10px;" onclick="bukaEdit(<?= $d ?>)" title="<?= $isFilled ? 'Edit' : 'Isi' ?>">
                      <i class="fa-solid <?= $isFilled ? 'fa-pen' : 'fa-plus' ?>"></i>
                    </button>
                  </td>
                <?php endfor; ?>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Catatan Tambahan -->
        <div class="mt-4" style="border:1px solid #ddd;border-radius:10px;padding:16px;">
          <div class="fw-bold mb-3" style="color:var(--blue)">Keterangan / Catatan Tambahan :</div>
          <div class="row g-2">
            <?php for ($d = 1; $d <= $daysInMonth; $d++):
              $cat = isset($rows[$d]) ? $rows[$d]['catatan'] : '';
              if (!$cat) continue;
              ?>
              <div class="col-12 col-md-6 col-lg-4">
                <div class="d-flex gap-2 align-items-start">
                  <span class="fw-semibold small" style="min-width:40px">Tgl <?= $d ?>:</span>
                  <span class="small text-secondary" style="border-bottom:1px solid #ccc;flex:1;min-height:20px">
                    <?= htmlspecialchars($cat) ?>
                  </span>
                </div>
              </div>
            <?php endfor; ?>
          </div>
        </div>

      </div>
    </div>
  </div>

  <!-- Modal Konfirmasi Hapus -->
  <div class="modal-overlay" id="modalKonfirmasiHapus" style="z-index:1100">
    <div class="modal-box" style="max-width:420px;text-align:center;padding:36px 32px">
      <div style="width:70px;height:70px;background:linear-gradient(135deg,#fee2e2,#fecaca);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
        <i class="fa-solid fa-triangle-exclamation" style="font-size:32px;color:#dc2626"></i>
      </div>
      <h5 class="fw-bold mb-2" style="color:#1e293b;font-size:18px">Hapus Data Ini?</h5>
      <p class="text-secondary mb-4" style="font-size:14px;line-height:1.6">
        Data perawatan tanggal ini akan <strong>dihapus permanen</strong> dan tidak dapat dikembalikan.
      </p>
      <div class="d-flex gap-3 justify-content-center">
        <button type="button" class="btn btn-secondary px-4" onclick="document.getElementById('modalKonfirmasiHapus').classList.remove('active')">
          <i class="fa-solid fa-xmark me-1"></i>Batal
        </button>
        <a id="btnKonfirmasiHapusLink" href="#" class="btn btn-danger px-4">
          <i class="fa-solid fa-trash me-1"></i>Ya, Hapus
        </a>
      </div>
    </div>
  </div>

  <!-- Modal Isian Perawatan -->
  <div class="modal-overlay" id="modalIsian">
    <div class="modal-box">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <h5 class="fw-bold mb-0" style="color:var(--blue)">
          <i class="fa-solid fa-restroom me-2"></i>Isi Perawatan Toilet Harian
        </h5>
        <button class="btn-close" onclick="document.getElementById('modalIsian').classList.remove('active')"></button>
      </div>
      <hr class="mt-1">
      
      <div id="bannerSudahIsi" class="alert alert-warning d-none py-2 px-3 mb-2" style="font-size:13px;border-radius:8px">
        <i class="fa-solid fa-lock me-1"></i>
        <strong>Data sudah terisi.</strong> Tanggal ini tidak dapat diisi ulang.
        <span id="linkHapus"></span>
      </div>

      <form method="POST" id="formPerawatan" enctype="multipart/form-data">
        <input type="hidden" name="simpan_checklist" value="1">
        
        <div class="row g-3 mb-3">
          <div class="col-12">
            <label class="form-label fw-semibold small">Tanggal Cek <span class="text-danger">*</span></label>
            <select class="form-select form-select-sm" name="tanggal" id="editTanggal" required onchange="cekSudahIsi(this.value)">
              <option value="">-- Pilih Tanggal --</option>
              <?php for ($d = 1; $d <= $daysInMonth; $d++): ?>
                <option value="<?= $d ?>"><?= $d ?> <?= $bulanNama[$bulan] ?> <?= $tahun ?></option>
              <?php endfor; ?>
            </select>
          </div>
        </div>

        <div class="fw-semibold small mb-2" style="color:var(--blue)">Status Pengecekan (Ok / Nok)</div>
        <div class="table-responsive mb-3">
          <table class="table table-sm table-bordered" style="font-size:13px">
            <thead style="background:var(--blue);color:#fff">
              <tr>
                <th>Item Pengecekan</th>
                <th class="text-center">Ok</th>
                <th class="text-center">Nok</th>
              </tr>
            </thead>
            <tbody>
              <?php $no2 = 1;
              foreach ($items as $key => $label): ?>
                <tr>
                  <td><?= $no2++ ?>. <?= htmlspecialchars($label) ?></td>
                  <td class="text-center">
                    <input class="form-check-input" type="radio" name="<?= $key ?>" id="<?= $key ?>_ok" value="Ok">
                  </td>
                  <td class="text-center">
                    <input class="form-check-input" type="radio" name="<?= $key ?>" id="<?= $key ?>_nok" value="Nok">
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <div class="row g-3 mb-3">
          <div class="col-6">
            <label class="form-label fw-semibold small">Paraf Pemeriksa <span class="text-danger">*</span></label>
            <input type="text" class="form-control form-control-sm" name="paraf" id="inputParaf" placeholder="Nama / Paraf">
          </div>
          <div class="col-6">
            <label class="form-label fw-semibold small">Catatan</label>
            <input type="text" class="form-control form-control-sm" name="catatan" id="inputCatatan" placeholder="Catatan tambahan">
          </div>
          <div class="col-12 mt-0">
            <label class="form-label fw-semibold small">Foto Bukti <span class="text-danger">*</span></label>
            <input type="file" name="foto" id="inputFoto" class="form-control form-control-sm" accept="image/*">
            <small class="text-secondary" style="font-size:10px">Format: JPG, PNG, WEBP. Maks 2MB.</small>
          </div>
        </div>

        <div class="d-flex gap-2">
          <button type="button" id="btnSimpan" class="btn flex-grow-1" style="background:var(--blue);color:#fff" onclick="validasiDanSimpan()">
            <i class="fa-solid fa-save me-1"></i>Simpan
          </button>
          <button type="button" class="btn btn-secondary" onclick="document.getElementById('modalIsian').classList.remove('active')">Batal</button>
        </div>
      </form>
    </div>
  </div>

  <footer class="py-3 text-center no-print">
    &copy; <?= date('Y') ?> - Sistem Perawatan Toilet | Team IT Pabrik
  </footer>

  <script>
    var existingData = <?= json_encode($rows) ?>;
    var items = <?= json_encode(array_keys($items)) ?>;
    var isAdmin = <?= ($_SESSION['role'] ?? '') === 'Admin' ? 'true' : 'false' ?>;
    var entityId = <?= $toilet_id ?>;
    var tahun = <?= $tahun ?>;
    var bulan = <?= $bulan ?>;
    var pageFile = 'toilet_kartu.php';
    // Restrict ordinary users to today only visually
    if (!isAdmin) {
      var editTanggal = document.getElementById('editTanggal');
      if (editTanggal) {
        editTanggal.style.pointerEvents = 'none';
        editTanggal.style.backgroundColor = '#e9ecef';
      }
    }

    function bukaKonfirmasiHapus(hapusId) {
      var url = pageFile + '?toilet_id=' + entityId + '&tahun=' + tahun + '&bulan=' + bulan + '&hapus=' + hapusId;
      document.getElementById('btnKonfirmasiHapusLink').href = url;
      document.getElementById('modalKonfirmasiHapus').classList.add('active');
    }

    function setFormLocked(locked, hapusId) {
      var allInputs = document.querySelectorAll('#formPerawatan input:not([type="hidden"]), #formPerawatan select');
      var btnSimpan = document.getElementById('btnSimpan');
      var banner = document.getElementById('bannerSudahIsi');

      if (locked) {
        if (typeof isAdmin !== 'undefined' && isAdmin) {
          allInputs.forEach(function (el) { el.disabled = false; });
          btnSimpan.style.display = '';
          banner.classList.remove('d-none', 'alert-warning');
          banner.classList.add('alert-info');
          var delBtn = hapusId ? ' &nbsp;<button type="button" class="btn btn-sm btn-danger py-0 px-2" style="font-size:12px" onclick="bukaKonfirmasiHapus(' + hapusId + ')"><i class="fa-solid fa-trash me-1"></i>Hapus Data</button>' : '';
          banner.innerHTML = '<i class="fa-solid fa-lock-open me-1"></i><strong>Mode Edit Admin:</strong> Anda dapat mengubah data tanggal ini.<span id="linkHapus">' + delBtn + '</span>';
        } else {
          allInputs.forEach(function (el) { el.disabled = true; });
          document.getElementById('editTanggal').disabled = false; // Biarkan bisa pilih tgl lain
          btnSimpan.style.display = 'none';
          banner.classList.remove('d-none', 'alert-info');
          banner.classList.add('alert-warning');
          banner.innerHTML = '<i class="fa-solid fa-lock me-1"></i><strong>Data sudah terisi.</strong> Tanggal ini tidak dapat diisi ulang.<span id="linkHapus"></span>';
        }
      } else {
        allInputs.forEach(function (el) { el.disabled = false; });
        if (!isAdmin) {
          var et = document.getElementById('editTanggal');
          if(et) {
            et.style.pointerEvents = 'none';
            et.style.backgroundColor = '#e9ecef';
          }
        }
        btnSimpan.style.display = '';
        banner.classList.add('d-none');
      }
    }

    function cekSudahIsi(tgl) {
      document.getElementById('editTanggal').value = tgl;
      items.forEach(function (k) {
        var ok = document.getElementById(k + '_ok');
        var nok = document.getElementById(k + '_nok');
        if (ok) ok.checked = false;
        if (nok) nok.checked = false;
      });
      document.getElementById('inputParaf').value = '';
      document.getElementById('inputCatatan').value = '';

      if (tgl && existingData[tgl]) {
        var d = existingData[tgl];
        items.forEach(function (k) {
          if (d[k]) {
            var el = document.getElementById(k + '_' + d[k].toLowerCase());
            if (el) el.checked = true;
          }
        });
        document.getElementById('inputParaf').value = d.paraf || '';
        document.getElementById('inputCatatan').value = d.catatan || '';
        setFormLocked(true, d.id);
      } else {
        setFormLocked(false, null);
      }
    }

    function bukaEdit(tgl) {
      if (!isAdmin) {
        var currentMonth = new Date().getMonth() + 1;
        var currentYear = new Date().getFullYear();
        var currentDay = new Date().getDate();
        if (tgl !== currentDay || bulan !== currentMonth || tahun !== currentYear) {
          Swal.fire({
            icon: 'warning',
            title: 'Akses Dibatasi',
            text: 'User biasa hanya dapat mengisi data untuk hari ini saja.',
            confirmButtonColor: '#2563eb'
          });
          return;
        }
      }
      var modal = document.getElementById('modalIsian');
      modal.classList.add('active');
      cekSudahIsi(tgl);
    }

    function validasiDanSimpan() {
      if (!document.getElementById('editTanggal').value) {
        Swal.fire('Error', 'Pilih tanggal cek terlebih dahulu', 'error');
        return;
      }
      var adaKosong = false;
      items.forEach(function(k){
        if(!document.getElementById(k+'_ok').checked && !document.getElementById(k+'_nok').checked) {
          adaKosong = true;
        }
      });
      if(adaKosong) {
         Swal.fire('Error', 'Harap isi semua item pengecekan (Ok/Nok)', 'error');
         return;
      }
      if(!document.getElementById('inputParaf').value) {
         Swal.fire('Error', 'Paraf pemeriksa wajib diisi', 'error');
         return;
      }
      var tgl = document.getElementById('editTanggal').value;
      var file = document.getElementById('inputFoto').files[0];
      if (!file && (!existingData[tgl] || !existingData[tgl].foto)) {
        Swal.fire('Error', 'Foto bukti wajib dilampirkan', 'error');
        return;
      }

      document.getElementById('formPerawatan').submit();
    }

    function previewFoto(url) {
      Swal.fire({
        imageUrl: url,
        imageAlt: 'Foto Bukti',
        showCloseButton: true,
        showConfirmButton: false,
        width: 'auto',
        padding: '1rem',
        customClass: {
          image: 'rounded'
        }
      });
    }

    <?php if (isset($_GET['error']) && $_GET['error'] == 'foto_wajib'): ?>
      Swal.fire({
        icon: 'error',
        title: 'Gagal Menyimpan',
        text: 'Foto bukti wajib dilampirkan.'
      });
    <?php endif; ?>
  </script>
</body>
</html>
