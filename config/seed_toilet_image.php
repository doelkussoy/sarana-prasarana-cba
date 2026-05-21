<?php
include "koneksi.php";

$locations = [
    "Toilet kantor utama lantai 1",
    "Toilet kantor utama lantai 2",
    "Toilet kantor Lab Lantai 1",
    "Toilet kantor Lab Lantai 2",
    "Toilet Pos Satpam 1",
    "Toilet Pos Satpam 2",
    "Toilet Kantor MTC lantai 1",
    "Toilet Kantor MTC lantai 2",
    "Toilet Timbangan",
    "Toilet Musholah Atas",
    "Toilet Musholah Bawah",
    "Toilet Assembling"
];

$nama = "Toilet";
$inserted = 0;

foreach ($locations as $lok) {
    // Check if exists
    $lok = mysqli_real_escape_string($conn, $lok);
    $cekLokasi = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id FROM toilet_unit WHERE lokasi='$lok'"));
    
    if (!$cekLokasi) {
        // Auto Increment Kode (TLT-1, TLT-2, ...)
        $kodeRes = mysqli_query($conn, "SELECT MAX(CAST(SUBSTRING(no_kode,5) AS UNSIGNED)) AS max_num FROM toilet_unit");
        $kodeRow = mysqli_fetch_assoc($kodeRes);
        $nextNum = ($kodeRow['max_num'] ?? 0) + 1;
        $kode = 'TLT-' . $nextNum;

        mysqli_query($conn, "INSERT INTO toilet_unit (no_kode, nama_sarana, lokasi) VALUES ('$kode','$nama','$lok')");
        $inserted++;
        echo "Inserted $lok with kode $kode\n";
    } else {
        echo "Skipped $lok (already exists)\n";
    }
}

echo "Total inserted: $inserted\n";
?>
