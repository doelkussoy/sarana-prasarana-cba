<?php
include "config/koneksi.php";
include "config/helpers.php";
session_start();
if (!isset($_SESSION['username'])) {
  header("Location: index.php");
  exit;
}

$hydrant_id = isset($_GET['hydrant_id']) ? (int) $_GET['hydrant_id'] : 0;
$tahun = isset($_GET['tahun']) ? (int) $_GET['tahun'] : (int) date('Y');

// Ambil data Hydrant
$hydrantRow = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM hydrant WHERE id=$hydrant_id"));
if (!$hydrantRow) {
  header("Location: hydrant_home.php");
  exit;
}

// Ambil daftar item dari master_item
$items = [];
$fields = [];
$resItems = mysqli_query($conn, "SELECT kolom, label FROM master_item WHERE modul='hydrant' AND is_active=1 ORDER BY id ASC");
if(mysqli_num_rows($resItems) == 0) {
    // Fallback seed (jika master_item belum pernah dibuka sama sekali)
    $defaultHydrant = ['valve_handle' => 'Valve Handle','hose_coupling_conect' => 'Hose Coupling Conect','baut_valve_handle' => 'Baut valve handle','fire_hose' => 'Fire hose','slang_hydrant' => 'Slang hydrant','nozzle' => 'Nozzle','box_hydrant' => 'Box Hydrant'];
    foreach($defaultHydrant as $k => $l) {
        mysqli_query($conn, "INSERT IGNORE INTO master_item (modul, kolom, label, is_active) VALUES ('hydrant', '$k', '$l', 1)");
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
if (($_SESSION['role'] ?? '') === 'Monitoring') {
    header("Location: hydrant_kartu.php?hydrant_id=$hydrant_id&tahun=$tahun");
    exit;
}
  $bulan = (int) $_POST['bulan'];
  $tgl = mysqli_real_escape_string($conn, $_POST['tanggal_cek']);
  $vals = [];
  foreach ($fields as $f) {
    $vals[$f] = isset($_POST[$f]) ? mysqli_real_escape_string($conn, $_POST[$f]) : null;
  }
  $paraf = mysqli_real_escape_string($conn, $_POST['paraf'] ?? '');
  $catatan = mysqli_real_escape_string($conn, $_POST['catatan'] ?? '');
  $uid = (int) $_SESSION['id'];

  // Handle Upload Foto
  $foto_name = null;
  $cekLama = mysqli_fetch_assoc(mysqli_query($conn, "SELECT foto FROM checklist_hydrant WHERE hydrant_id=$hydrant_id AND tahun=$tahun AND bulan=$bulan"));
  if ($cekLama)
    $foto_name = $cekLama['foto'];

  if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
    $target_dir = "uploads/";
    $ext = pathinfo($_FILES["foto"]["name"], PATHINFO_EXTENSION);
    if (empty($ext)) {
      $ext = "jpg";
    }
    $new_name = "HYDRANT_" . $hydrant_id . "_" . $tahun . "_" . $bulan . "_" . time() . "." . $ext;
    $upload_result = compressImage($_FILES["foto"]["tmp_name"], $target_dir . $new_name);
    if ($upload_result) {
      $foto_name = $new_name;
    }
  }

  // Validasi: foto wajib ada (baru atau lama)
  if (!$foto_name) {
    header("Location: hydrant_kartu.php?hydrant_id=$hydrant_id&tahun=$tahun&error=foto_wajib");
    exit;
  }

  // Cek sudah ada?
  $cek = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id FROM checklist_hydrant WHERE hydrant_id=$hydrant_id AND tahun=$tahun AND bulan=$bulan"));
  if ($cek) {
    $setArr = [];
    foreach ($fields as $f) {
      $v = $vals[$f];
      $setArr[] = "`$f`=" . ($v ? "'$v'" : "NULL");
    }
    $setArr[] = "`tanggal_cek`='$tgl'";
    $setArr[] = "`paraf`='$paraf'";
    $setArr[] = "`catatan`='$catatan'";
    $setArr[] = "`foto`=" . ($foto_name ? "'$foto_name'" : "NULL");
    $set = implode(',', $setArr);
    mysqli_query($conn, "UPDATE checklist_hydrant SET $set WHERE id={$cek['id']}");
  } else {
    // Build insert string dinamis berdasarkan fields yang ada di master_item
    $fStr = "hydrant_id,tahun,bulan,tanggal_cek," . implode(',', $fields) . ",paraf,catatan,users_id,foto";
    
    $vArr = [];
    foreach ($fields as $f) {
      $vArr[] = $vals[$f] ? "'{$vals[$f]}'" : 'NULL';
    }
    
    $ft = $foto_name ? "'$foto_name'" : 'NULL';
    $vStr = "$hydrant_id,$tahun,$bulan,'$tgl'," . implode(',', $vArr) . ",'$paraf','$catatan',$uid,$ft";
    
    mysqli_query($conn, "INSERT INTO checklist_hydrant ($fStr) VALUES ($vStr)");
  }
  header("Location: hydrant_kartu.php?hydrant_id=$hydrant_id&tahun=$tahun");
  exit;
}

// Handle hapus baris (hanya Admin)
if (isset($_GET['hapus'])) {
  if (($_SESSION['role'] ?? '') !== 'Admin') {
    header("Location: hydrant_kartu.php?hydrant_id=$hydrant_id&tahun=$tahun");
    exit;
  }
  mysqli_query($conn, "DELETE FROM checklist_hydrant WHERE id=" . (int) $_GET['hapus'] . " AND hydrant_id=$hydrant_id");
  header("Location: hydrant_kartu.php?hydrant_id=$hydrant_id&tahun=$tahun");
  exit;
}

// Ambil semua perawatan tahun ini
$rows = [];
$res = mysqli_query($conn, "SELECT * FROM checklist_hydrant WHERE hydrant_id=$hydrant_id AND tahun=$tahun ORDER BY bulan ASC");
while ($r = mysqli_fetch_assoc($res)) {
  $rows[$r['bulan']] = $r;
}

$bulanNama = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'];
$editBulan = isset($_GET['edit']) ? (int) $_GET['edit'] : 0;
$editData = $editBulan && isset($rows[$editBulan]) ? $rows[$editBulan] : null;
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kartu Riwayat Hydrant - <?= htmlspecialchars($hydrantRow['no_kode']) ?></title>
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

    html,
    body {
      width: 100vw;
      max-width: 100vw;
      overflow-x: hidden !important;
      position: relative;
    }

    body {
      background: #f0f2f5;
      font-family: 'Segoe UI', sans-serif;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    .navbar-hydrant {
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

    .btn-isi {
      background: var(--blue);
      color: #fff;
      border: none;
      border-radius: 6px;
      font-size: 11px;
      padding: 2px 8px;
    }

    .btn-isi:hover {
      background: var(--blue-dark);
      color: #fff;
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

    .form-check-label {
      font-size: 13px;
    }

    .mobile-scroll-hint {
      display: none;
      font-size: 11px;
      color: var(--blue);
      margin-bottom: 8px;
      text-align: right;
      font-style: italic;
      animation: pulse 2s infinite;
    }

    @keyframes pulse {
      0% { opacity: 0.5; }
      50% { opacity: 1; }
      100% { opacity: 0.5; }
    }

    @media (max-width: 768px) {
      .tbl-kartu .th-item {
        min-width: 120px !important;
        white-space: normal !important;
        font-size: 10px !important;
        padding-left: 5px !important;
        line-height: 1.2;
        border-right: 2px solid #1e3a8a !important;
      }
      .tbl-kartu th, .tbl-kartu td {
        padding: 4px 2px !important;
        font-size: 10px !important;
      }
      .tbl-kartu .th-bulan {
        min-width: 22px !important;
        font-size: 10px !important;
        padding: 2px !important;
      }
      .mobile-scroll-hint {
        display: block !important;
      }
      .kartu-header {
        padding: 15px 12px;
      }
      .kartu-body {
        padding: 15px 12px;
      }
      .info-box {
        padding: 10px;
      }
      .info-label {
        font-size: 10px;
      }
      .info-val {
        font-size: 13px;
      }
      .top-controls {
        flex-direction: column;
        align-items: stretch !important;
      }
      .top-controls > a.btn {
        align-self: flex-start;
      }
      .top-controls form {
        width: 100%;
        margin-left: 0 !important;
        justify-content: flex-start;
        flex-wrap: wrap;
      }
      .top-controls > button {
        width: 100%;
        margin-top: 5px;
      }
      .modal-box {
        padding: 20px 15px;
      }
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
        /* Menghilangkan header URL dan footer halaman browser */
      }

      body {
        background: #fff;
        padding: 1.5cm;
        /* Memberikan ruang aman untuk print */
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
        /* Memperjelas garis tabel saat di-print */
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
        margin-bottom: 25px;
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
  <nav class="navbar navbar-hydrant fixed-top py-3 shadow no-print">
    <div class="container-fluid d-flex justify-content-between align-items-center">
      <div class="d-flex align-items-center gap-2 text-white" style="cursor:pointer"
        onclick="window.location.href='dashboard.php'">
        <img src="assets/images/cba.png" alt="Logo CBA"
          style="height:40px; background:white; padding:4px; border-radius:8px;">
        <span class="fw-bold fs-5 d-none d-sm-inline" style="letter-spacing:1px">PERAWATAN HYDRANT</span>
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

    <!-- Print Header Custom (Pengganti bawaan browser) -->
    <div class="print-only-header">
      <div style="width:33%; text-align:left;"><?= date('d/m/Y, H:i') ?></div>
      <div style="width:33%; text-align:center;">Kartu Riwayat Hydrant - <?= htmlspecialchars($hydrantRow['no_kode']) ?>
      </div>
      <div style="width:33%; text-align:right;"></div>
    </div>

    <!-- Kontrol atas -->
    <div class="d-flex flex-wrap gap-2 align-items-center mb-3 no-print">
      <a href="hydrant_home.php" class="btn btn-sm btn-outline-secondary rounded-pill">
        <i class="fa-solid fa-arrow-left me-1"></i>Kembali
      </a>
      <form method="GET" class="d-flex align-items-center gap-2 ms-auto">
        <input type="hidden" name="hydrant_id" value="<?= $hydrant_id ?>">
        <label class="text-secondary fw-semibold small mb-0">Tahun:</label>
        <select name="tahun" class="form-select form-select-sm" style="width:100px" onchange="this.form.submit()">
          <?php for ($y = 2026; $y <= 2030; $y++): ?>
            <option value="<?= $y ?>" <?= $y == $tahun ? 'selected' : '' ?>><?= $y ?></option>
          <?php endfor; ?>
        </select>
      </form>
      <button class="btn btn-sm btn-success rounded-pill no-print" onclick="window.print()">
        <i class="fa-solid fa-print me-1"></i>Cetak
      </button>
      <?php if (($_SESSION['role'] ?? '') !== 'Monitoring'): ?>
      <button class="btn btn-sm rounded-pill no-print" style="background:var(--blue);color:#fff"
        onclick="bukaEdit(new Date().getMonth() + 1)">
        <i class="fa-solid fa-plus me-1"></i>Isi Perawatan
      </button>
      <?php endif; ?>
    </div>

    <!-- Kartu Riwayat -->
    <div style="border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,.12)">
      <div class="kartu-header">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
          <div>
            <div class="fw-bold fs-5 mb-1">KARTU RIWAYAT SARANA PRASARANA</div>
            <small class="opacity-80">CBA/GA/E-003 &nbsp;|&nbsp; Rev: 00</small>
          </div>
          <div class="text-end">
            <div class="info-label text-white opacity-75">Tahun</div>
            <div class="fw-bold fs-4"><?= $tahun ?></div>
          </div>
        </div>
      </div>
      <div class="kartu-body">
        <!-- Info Hydrant -->
        <div class="info-box">
          <div class="row g-2">
            <div class="col-12 col-md-4">
              <div class="info-label">Kode</div>
              <div class="info-val"><span class="badge-kode"><?= htmlspecialchars($hydrantRow['no_kode']) ?></span>
              </div>
            </div>
            <div class="col-12 col-md-4">
              <div class="info-label">Nama Sarana</div>
              <div class="info-val"><?= htmlspecialchars($hydrantRow['nama_sarana']) ?></div>
            </div>
            <div class="col-12 col-md-4">
              <div class="info-label">Lokasi</div>
              <div class="info-val"><i
                  class="fa-solid fa-location-dot text-danger me-1"></i><?= htmlspecialchars($hydrantRow['lokasi']) ?>
              </div>
            </div>
          </div>
        </div>

        <!-- Tabel Perawatan -->
        <div class="mobile-scroll-hint">
          <i class="fa-solid fa-arrows-left-right me-1"></i> Geser tabel ke kanan untuk melihat bulan lainnya
        </div>
        <div class="table-responsive">
          <table class="tbl-kartu">
            <thead>
              <tr>
                <th rowspan="2" style="width:30px">NO</th>
                <th rowspan="2" class="th-item" style="min-width:180px">PENGECEKAN</th>
                <?php for ($b = 1; $b <= 12; $b++): ?>
                  <th colspan="3" class="th-bulan"><?= $bulanNama[$b] ?></th>
                <?php endfor; ?>
              </tr>
              <tr>
                <?php for ($b = 1; $b <= 12; $b++): ?>
                  <th style="background:var(--blue-dark);color:#fff;font-size:10px">Tgl</th>
                  <th style="background:var(--blue-dark);color:#fff;font-size:10px">Ok</th>
                  <th style="background:var(--blue-dark);color:#fff;font-size:10px">Nok</th>
                <?php endfor; ?>
              </tr>
            </thead>
            <tbody>
              <?php $no = 1;
              foreach ($items as $key => $label): ?>
                <tr>
                  <td><?= $no++ ?></td>
                  <td class="th-item"><?= $label ?></td>
                  <?php for ($b = 1; $b <= 12; $b++):
                    $r = $rows[$b] ?? null;
                    $val = $r ? $r[$key] : null;
                    $tgl = $r ? date('d', strtotime($r['tanggal_cek'])) : '';
                    ?>
                    <td style="font-size:10px"><?= $tgl ?: '<span class="empty-cell">-</span>' ?></td>
                    <td class="<?= $val == 'Ok' ? 'ok-cell' : '' ?>"><?= $val == 'Ok' ? '✓' : '' ?></td>
                    <td class="<?= $val == 'Nok' ? 'nok-cell' : '' ?>"><?= $val == 'Nok' ? '✗' : '' ?></td>
                  <?php endfor; ?>
                </tr>
              <?php endforeach; ?>
              <!-- Baris Paraf -->
              <tr>
                <td colspan="2" class="th-item fw-bold">Pemeriksa</td>
                <?php for ($b = 1; $b <= 12; $b++):
                  $r = $rows[$b] ?? null;
                  $paraf = $r ? htmlspecialchars($r['paraf']) : '';
                  ?>
                  <td colspan="3" style="font-size:10px;font-style:italic">
                    <?= $paraf ?: '<span class="empty-cell">Paraf</span>' ?>
                  </td>
                <?php endfor; ?>
              </tr>
              <!-- Baris Foto -->
              <tr class="no-print">
                <td colspan="2" class="th-item fw-bold">Foto Bukti</td>
                <?php for ($b = 1; $b <= 12; $b++):
                  $r = $rows[$b] ?? null;
                  $foto = $r ? $r['foto'] : '';
                  ?>
                  <td colspan="3" class="text-center">
                    <?php if ($foto): ?>
                      <img src="uploads/<?= $foto ?>"
                        style="width:45px; height:30px; object-fit:cover; border-radius:4px; cursor:pointer; border:1px solid #ddd;"
                        title="Lihat Foto" onclick="previewFoto('uploads/<?= $foto ?>')">
                    <?php else: ?>
                      <span class="empty-cell" style="font-size:9px">-</span>
                    <?php endif; ?>
                  </td>
                <?php endfor; ?>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Keterangan/Catatan Tambahan -->
        <div class="mt-4" style="border:1px solid #ddd;border-radius:10px;padding:16px;">
          <div class="fw-bold mb-3" style="color:var(--blue)">Keterangan / Catatan Tambahan :</div>
          <div class="row g-2">
            <?php foreach ($bulanNama as $b => $nm):
              if ($b == 0)
                continue;
              $cat = isset($rows[$b]) ? $rows[$b]['catatan'] : '';
              ?>
              <div class="col-6 col-md-3 col-lg-2">
                <div class="d-flex gap-2 align-items-start">
                  <span class="fw-semibold small" style="min-width:28px"><?= $nm ?> :</span>
                  <span class="small text-secondary" style="border-bottom:1px solid #ccc;flex:1;min-height:20px;word-break:break-word;overflow-wrap:break-word;">
                    <?= $cat ? htmlspecialchars($cat) : '' ?>
                  </span>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>

        <?php if (($_SESSION['role'] ?? '') !== 'Monitoring'): ?>
        <!-- Tombol Aksi per Bulan -->
        <div class="mt-3 no-print">
          <div class="fw-semibold mb-2 text-secondary small">Kelola Perawatan per Bulan:</div>
          <div class="d-flex flex-wrap gap-2">
            <?php for ($b = 1; $b <= 12; $b++): ?>
              <button class="btn btn-sm <?= isset($rows[$b]) ? 'btn-success' : 'btn-outline-secondary' ?> rounded-pill"
                onclick="bukaEdit(<?= $b ?>)"
                title="<?= isset($rows[$b]) ? 'Edit' : 'Isi' ?> Bulan <?= $bulanNama[$b] ?>">
                <?= $bulanNama[$b] ?>   <?= isset($rows[$b]) ? '<i class=\'fa-solid fa-check\'></i>' : '' ?>
              </button>
            <?php endfor; ?>
          </div>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Modal Konfirmasi Hapus -->
  <div class="modal-overlay" id="modalKonfirmasiHapus" style="z-index:1100">
    <div class="modal-box" style="max-width:420px;text-align:center;padding:36px 32px">
      <div
        style="width:70px;height:70px;background:linear-gradient(135deg,#fee2e2,#fecaca);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
        <i class="fa-solid fa-triangle-exclamation" style="font-size:32px;color:#dc2626"></i>
      </div>
      <h5 class="fw-bold mb-2" style="color:#1e293b;font-size:18px">Hapus Data Ini?</h5>
      <p class="text-secondary mb-4" style="font-size:14px;line-height:1.6">
        Data perawatan bulan ini akan <strong>dihapus permanen</strong> dan tidak dapat dikembalikan.
      </p>
      <div class="d-flex gap-3 justify-content-center">
        <button type="button" class="btn btn-secondary px-4"
          onclick="document.getElementById('modalKonfirmasiHapus').classList.remove('active')">
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
          <i class="fa-solid fa-fire me-2"></i>Isi Perawatan Hydrant
        </h5>
        <button class="btn-close" onclick="document.getElementById('modalIsian').classList.remove('active')"></button>
      </div>
      <hr class="mt-1">
      <!-- Banner sudah terisi -->
      <div id="bannerSudahIsi" class="alert alert-warning d-none py-2 px-3 mb-2"
        style="font-size:13px;border-radius:8px">
        <i class="fa-solid fa-lock me-1"></i>
        <strong>Data sudah terisi.</strong> Bulan ini tidak dapat diisi ulang.
        <span id="linkHapus"></span>
      </div>
      <form method="POST" id="formPerawatan" enctype="multipart/form-data">
        <input type="hidden" name="simpan_checklist" value="1">
        <input type="hidden" name="bulan" id="editBulan" value="">

        <div class="row g-3 mb-3">
          <div class="col-6">
            <label class="form-label fw-semibold small">Bulan <span class="text-danger">*</span></label>
            <select class="form-select form-select-sm" name="bulan_select" id="bulanSelect" required
              onchange="document.getElementById('editBulan').value=this.value">
              <option value="">-- Pilih Bulan --</option>
              <?php for ($b = 1; $b <= 12; $b++): ?>
                <option value="<?= $b ?>"><?= $bulanNama[$b] ?></option>
              <?php endfor; ?>
            </select>
          </div>
          <div class="col-6">
            <label class="form-label fw-semibold small">Tanggal Cek <span class="text-danger">*</span></label>
            <input type="date" class="form-control form-control-sm" name="tanggal_cek" id="inputTgl" required>
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
                  <td><?= $no2++ ?>. <?= $label ?></td>
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
            <input type="text" class="form-control form-control-sm" name="paraf" id="inputParaf"
              placeholder="Nama / Paraf">
          </div>
          <div class="col-6">
            <label class="form-label fw-semibold small">Catatan</label>
            <input type="text" class="form-control form-control-sm" name="catatan" id="inputCatatan"
              placeholder="Catatan tambahan">
          </div>
          <div class="col-12 mt-0">
            <label class="form-label fw-semibold small">Foto Bukti <span class="text-danger">*</span></label>
            <input type="file" name="foto" id="inputFoto" class="form-control form-control-sm" accept="image/*">
            <small class="text-secondary" style="font-size:10px">Format: JPG, PNG, WEBP. Maks 2MB.</small>
            <div id="previewContainer" class="mt-2 d-none">
              <div class="d-flex align-items-center gap-2">
                <img id="imagePreview" src="#" alt="Preview"
                  style="max-width: 100px; max-height: 100px; border-radius: 4px; border: 1px solid #ddd;">
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="resetFoto()">
                  <i class="fa-solid fa-times"></i> Hapus
                </button>
              </div>
            </div>
          </div>
        </div>

        <div class="d-flex gap-2">
          <button type="button" id="btnSimpan" class="btn flex-grow-1" style="background:var(--blue);color:#fff"
            onclick="validasiDanSimpan()">
            <i class="fa-solid fa-save me-1"></i>Simpan
          </button>
          <button type="button" class="btn btn-secondary"
            onclick="document.getElementById('modalIsian').classList.remove('active')">Batal</button>
        </div>
      </form>
    </div>
  </div>

  <footer class="py-3 text-center no-print">
    &copy; <?= date('Y') ?> - Sistem Perawatan Hydrant | Team IT Pabrik
  </footer>

  <script>
    var existingData = <?= json_encode($rows) ?>;
    var items = <?= json_encode(array_keys($items)) ?>;
    var isAdmin = <?= ($_SESSION['role'] ?? '') === 'Admin' ? 'true' : 'false' ?>;
    // Restrict ordinary users to today only and visually lock fields
    if (!isAdmin) {
      var today = new Date().toISOString().split('T')[0];
      var dateInput = document.getElementById('inputTgl');
      if (dateInput) {
        dateInput.setAttribute('min', today);
        dateInput.setAttribute('max', today);
        dateInput.setAttribute('readonly', 'readonly');
        dateInput.style.pointerEvents = 'none';
        dateInput.style.backgroundColor = '#e9ecef';
      }
      var bulanSelect = document.getElementById('bulanSelect');
      if (bulanSelect) {
        bulanSelect.style.pointerEvents = 'none';
        bulanSelect.style.backgroundColor = '#e9ecef';
      }
    }
    var entityId = <?= $hydrant_id ?>;
    var tahun = <?= $tahun ?>;
    var entityParam = 'hydrant_id';
    var pageFile = 'hydrant_kartu.php';

    function bukaKonfirmasiHapus(hapusId) {
      var url = pageFile + '?' + entityParam + '=' + entityId + '&tahun=' + tahun + '&hapus=' + hapusId;
      document.getElementById('btnKonfirmasiHapusLink').href = url;
      document.getElementById('modalKonfirmasiHapus').classList.add('active');
    }

    function setFormLocked(locked, hapusId) {
      var allInputs = document.querySelectorAll('#formPerawatan input, #formPerawatan select');
      var btnSimpan = document.getElementById('btnSimpan');
      var banner = document.getElementById('bannerSudahIsi');

      if (locked) {
        if (typeof isAdmin !== 'undefined' && isAdmin) {
          allInputs.forEach(function (el) { el.disabled = false; });
          btnSimpan.style.display = '';
          banner.classList.remove('d-none', 'alert-warning');
          banner.classList.add('alert-info');
          var delBtn = hapusId ? ' &nbsp;<button type="button" class="btn btn-sm btn-danger py-0 px-2" style="font-size:12px" onclick="bukaKonfirmasiHapus(' + hapusId + ')"><i class="fa-solid fa-trash me-1"></i>Hapus Data</button>' : '';
          banner.innerHTML = '<i class="fa-solid fa-lock-open me-1"></i><strong>Mode Edit Admin:</strong> Anda dapat mengubah data bulan ini.<span id="linkHapus">' + delBtn + '</span>';
        } else {
          allInputs.forEach(function (el) { el.disabled = true; });
          btnSimpan.style.display = 'none';
          banner.classList.remove('d-none', 'alert-info');
          banner.classList.add('alert-warning');
          banner.innerHTML = '<i class="fa-solid fa-lock me-1"></i><strong>Data sudah terisi.</strong> Bulan ini tidak dapat diisi ulang.<span id="linkHapus"></span>';
        }
      } else {
        allInputs.forEach(function (el) { 
          if (!isAdmin && (el.id === 'bulanSelect' || el.id === 'mingguSelect')) {
            el.disabled = true;
          } else {
            el.disabled = false; 
          }
        });
        btnSimpan.style.display = '';
        banner.classList.add('d-none');
        banner.innerHTML = '<i class="fa-solid fa-lock me-1"></i><strong>Data sudah terisi.</strong> Bulan ini tidak dapat diisi ulang.<span id="linkHapus"></span>';
      }
    }

    function cekSudahIsi(bulan) {
      if (bulan && existingData[bulan]) {
        setFormLocked(true, existingData[bulan].id);
      } else {
        setFormLocked(false, null);
      }
    }

    function bukaEdit(bulan) {
      // Prevent normal users from editing past/future months
      if (!isAdmin) {
        var currentMonth = new Date().getMonth() + 1;
        var currentYear = new Date().getFullYear();
        if (bulan !== currentMonth || tahun !== currentYear) {
          Swal.fire({
            icon: 'warning',
            title: 'Akses Dibatasi',
            text: 'User biasa hanya dapat mengisi data untuk bulan dan tahun saat ini saja.',
            confirmButtonColor: '#2563eb'
          });
          return;
        }
      }
      var modal = document.getElementById('modalIsian');
      modal.classList.add('active');
      document.getElementById('editBulan').value = bulan;
      document.getElementById('bulanSelect').value = bulan;
      resetFoto(); // Reset preview saat buka modal
      resetFoto();
      items.forEach(function (k) {
        var ok = document.getElementById(k + '_ok');
        var nok = document.getElementById(k + '_nok');
        if (ok) ok.checked = false;
        if (nok) nok.checked = false;
      });
      document.getElementById('inputParaf').value = '';
      document.getElementById('inputCatatan').value = '';
      document.getElementById('inputTgl').value = new Date().toISOString().split('T')[0];
      if (existingData[bulan]) {
        var d = existingData[bulan];
        if (d.tanggal_cek) document.getElementById('inputTgl').value = d.tanggal_cek;
        items.forEach(function (k) {
          if (d[k]) { var el = document.getElementById(k + '_' + d[k].toLowerCase()); if (el) el.checked = true; }
        });
        document.getElementById('inputParaf').value = d.paraf || '';
        document.getElementById('inputCatatan').value = d.catatan || '';
      }
      cekSudahIsi(bulan);
    }

    document.getElementById('bulanSelect').addEventListener('change', function () {
      var bulan = this.value;
      document.getElementById('editBulan').value = bulan;
      items.forEach(function (k) {
        var ok = document.getElementById(k + '_ok');
        var nok = document.getElementById(k + '_nok');
        if (ok) ok.checked = false;
        if (nok) nok.checked = false;
      });
      document.getElementById('inputParaf').value = '';
      document.getElementById('inputCatatan').value = '';
      document.getElementById('inputTgl').value = new Date().toISOString().split('T')[0];
      if (bulan && existingData[bulan]) {
        var d = existingData[bulan];
        if (d.tanggal_cek) document.getElementById('inputTgl').value = d.tanggal_cek;
        items.forEach(function (k) {
          if (d[k]) { var el = document.getElementById(k + '_' + d[k].toLowerCase()); if (el) el.checked = true; }
        });
        document.getElementById('inputParaf').value = d.paraf || '';
        document.getElementById('inputCatatan').value = d.catatan || '';
      }
      cekSudahIsi(bulan);
    });
    function previewFoto(url) {
      Swal.fire({
        imageUrl: url,
        imageAlt: 'Foto Bukti',
        showCloseButton: true,
        showConfirmButton: false,
        width: 'auto',
        padding: '1rem',
        imageWidth: 400,
        imageHeight: 400,
        customClass: {
          image: 'rounded'
        },
        didOpen: () => {
          const img = Swal.getImage();
          if (img) {
            img.style.objectFit = 'contain';
            img.style.backgroundColor = '#f8f9fa';
          }
        }
      });
    }

    // Preview Foto
    document.getElementById('inputFoto').addEventListener('change', function () {
      const file = this.files[0];
      const previewContainer = document.getElementById('previewContainer');
      const imagePreview = document.getElementById('imagePreview');

      if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
          imagePreview.src = e.target.result;
          previewContainer.classList.remove('d-none');
        }
        reader.readAsDataURL(file);
      } else {
        resetFoto();
      }
    });

    function resetFoto() {
      document.getElementById('inputFoto').value = '';
      document.getElementById('previewContainer').classList.add('d-none');
      document.getElementById('imagePreview').src = '#';
    }

    function validasiDanSimpan() {
      var bulan = document.getElementById('bulanSelect').value;
      var tanggal = document.getElementById('inputTgl').value;
      var paraf = document.getElementById('inputParaf').value.trim();
      var inputFoto = document.getElementById('inputFoto');
      var adaFotoLama = bulan && existingData[bulan] && existingData[bulan].foto;
      var adaFotoBaru = inputFoto.files && inputFoto.files.length > 0;

      var errors = [];
      if (!bulan) errors.push('Bulan');
      if (!tanggal) errors.push('Tanggal Cek');
      if (!paraf) errors.push('Paraf Pemeriksa');

      if (errors.length > 0) {
        Swal.fire({
          icon: 'warning',
          title: 'Data Belum Lengkap!',
          html: 'Harap isi field berikut:<br><b>' + errors.join(', ') + '</b>',
          confirmButtonColor: '#2563eb',
          confirmButtonText: 'Ok'
        });
        return;
      }

      if (!adaFotoLama && !adaFotoBaru) {
        Swal.fire({
          icon: 'warning',
          title: 'Foto Bukti Wajib!',
          text: 'Harap tambahkan foto bukti pengecekan sebelum menyimpan.',
          confirmButtonColor: '#2563eb',
          confirmButtonText: '<i class="fa-solid fa-camera me-1"></i>Ok, Tambahkan Foto'
        });
        return;
      }
      document.getElementById('formPerawatan').submit();
    }

    // Tampilkan pesan error dari server jika ada
    <?php if (isset($_GET['error']) && $_GET['error'] === 'foto_wajib'): ?>
      Swal.fire({
        icon: 'warning',
        title: 'Foto Bukti Wajib!',
        text: 'Harap tambahkan foto bukti pengecekan sebelum menyimpan.',
        confirmButtonColor: '#2563eb',
        confirmButtonText: 'Ok'
      });
    <?php endif; ?>
  </script>
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