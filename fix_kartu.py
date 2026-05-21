import os

files = ['gedung_kartu.php', 'hydrant_kartu.php', 'grease_trap_kartu.php', 'toilet_kartu.php']

for f in files:
    if not os.path.exists(f):
        print(f"File {f} not found!")
        continue
        
    with open(f, 'r', encoding='utf-8') as file:
        content = file.read()

    # 1. Fix PHP POST check
    content = content.replace(
        "if (($_SESSION['role'] ?? '') !== 'Admin') {\n    $today = date('Y-m-d');\n    if ($tgl < $today) {\n        header(\"Location: ",
        "if (($_SESSION['role'] ?? '') !== 'Admin') {\n    $today = date('Y-m-d');\n    $currentBulan = (int)date('n');\n    $currentTahun = (int)date('Y');\n    if ($tgl !== $today || $bulan !== $currentBulan || $tahun !== $currentTahun) {\n        header(\"Location: "
    )
    content = content.replace('&error=past_date");', '&error=invalid_date");')

    # 2. Fix Isi Perawatan button
    content = content.replace(
        "onclick=\"document.getElementById('modalIsian').classList.add('active');document.getElementById('editBulan').value=''\"",
        "onclick=\"bukaEdit(new Date().getMonth() + 1)\""
    )
    
    # Also fix it for grease trap/toilet which might pass minggu as well if we don't have it explicitly, wait.
    # Actually, bukaEdit in grease trap expects (bulan, minggu). If we pass only (bulan), it will set minggu to 1 by default (minggu = minggu || 1). This is fine.
    
    # 3. Fix !isAdmin JS init
    old_admin_check = """// Restrict ordinary users to today and future dates
    if (!isAdmin) {
      var today = new Date().toISOString().split('T')[0];
      var dateInput = document.getElementById('inputTgl');
      if (dateInput) dateInput.setAttribute('min', today);
    }"""
    
    new_admin_check = """// Restrict ordinary users to today only and visually lock fields
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
      var mingguSelect = document.getElementById('mingguSelect');
      if (mingguSelect) {
        mingguSelect.style.pointerEvents = 'none';
        mingguSelect.style.backgroundColor = '#e9ecef';
      }
    }"""
    content = content.replace(old_admin_check, new_admin_check)
    
    # 4. Fix setFormLocked
    old_locked_else = """} else {
        allInputs.forEach(function (el) { el.disabled = false; });
        btnSimpan.style.display = '';"""
    
    new_locked_else = """} else {
        allInputs.forEach(function (el) { 
          if (!isAdmin && (el.id === 'bulanSelect' || el.id === 'mingguSelect')) {
            el.disabled = true;
          } else {
            el.disabled = false; 
          }
        });
        btnSimpan.style.display = '';"""
    content = content.replace(old_locked_else, new_locked_else)
    
    # 5. Set default date in JS (two occurrences usually, one in onchange, one in bukaEdit)
    content = content.replace(
        "document.getElementById('inputTgl').value = '';",
        "document.getElementById('inputTgl').value = new Date().toISOString().split('T')[0];"
    )
    
    # 6. Fix bukaEdit duplicated block.
    # We will just replace the bad part. 
    # For apar, gedung, hydrant, toilet:
    bad_buka_edit_1 = """    function bukaEdit(bulan) {
  // Prevent normal users from editing past months
  if (!isAdmin) {
    var selectedDate = new Date(tahun, bulan - 1, 1);
    var today = new Date();
    today.setDate(1);
    today.setHours(0,0,0,0);
    if (selectedDate < today) {
      Swal.fire({
        icon: 'warning',
        title: 'Akses Dibatasi',
        text: 'User biasa hanya dapat mengisi/mengubah data untuk bulan ini dan kedepan.',
        confirmButtonColor: '#2563eb'
      });
      return;
    }
  }
  var modal = document.getElementById('modalIsian');
  modal.classList.add('active');
  document.getElementById('bulanSelect').value = bulan;
  document.getElementById('editBulan').value = bulan;
}
      // Prevent normal users from editing past dates
      if (!isAdmin) {
        var selectedDate = new Date(tahun, bulan - 1, bulan);
        var today = new Date();
        today.setHours(0,0,0,0);
        if (selectedDate < today) {
          Swal.fire({
            icon: 'warning',
            title: 'Akses Dibatasi',
            text: 'User biasa hanya dapat mengisi/mengubah data untuk hari ini dan kedepan.',
            confirmButtonColor: '#2563eb'
          });
          return;
        }
      }"""
      
    good_buka_edit_1 = """    function bukaEdit(bulan) {
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
      }"""
    content = content.replace(bad_buka_edit_1, good_buka_edit_1)

    # For grease trap:
    bad_buka_edit_2 = """    function bukaEdit(bulan, minggu) {
  minggu = minggu || 1;
  // Prevent normal users from editing past months
  if (!isAdmin) {
    var selectedDate = new Date(tahun, bulan - 1, 1);
    var today = new Date();
    today.setDate(1);
    today.setHours(0,0,0,0);
    if (selectedDate < today) {
      Swal.fire({
        icon: 'warning',
        title: 'Akses Dibatasi',
        text: 'User biasa hanya dapat mengisi/mengubah data untuk bulan ini dan kedepan.',
        confirmButtonColor: '#2563eb'
      });
      return;
    }
  }
  var modal = document.getElementById('modalIsian');
  modal.classList.add('active');
  document.getElementById('bulanSelect').value = bulan;
  document.getElementById('mingguSelect').value = minggu;
}"""
    good_buka_edit_2 = """    function bukaEdit(bulan, minggu) {
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
      }"""
    content = content.replace(bad_buka_edit_2, good_buka_edit_2)
    
    with open(f, 'w', encoding='utf-8') as file:
        file.write(content)
        
    print(f"Processed {f}")
