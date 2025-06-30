<?php
require_once '../../../databaseconfig.php';
require_once '../../../sessionconfig.php';

// Mulai session
startSession();
$asal = 'Dasbor/Transaksi/Pinjaman';
if (!isLoggedIn()) {
    header('Location: /Login/?redirect=' . urlencode($asal));
    exit();
}
if (!isHasAccess()) {
    header('Location: /Dasbor/');
    exit();
}
?>

<?php
// Ambil data profil dari database
$query = "SELECT nama_depan, nama_belakang FROM akun WHERE id_akun = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 'i', $_SESSION['id_akun']);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $nama_depan, $nama_belakang);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

//Ambil nama koperasi
$kopQuery = "SELECT nama_koperasi FROM koperasi WHERE id_koperasi = ?";
$stmt = mysqli_prepare($conn, $kopQuery);
mysqli_stmt_bind_param($stmt, 'i', $_SESSION['id_koperasi']);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $nama_koperasi);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

// Ambil data anggota dari database
$anggotaQuery = "SELECT id_custom, nama FROM anggota WHERE id_koperasi = ?";
$stmt = mysqli_prepare($conn, $anggotaQuery);
mysqli_stmt_bind_param($stmt, 'i', $_SESSION['id_koperasi']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
mysqli_stmt_close($stmt);

// Ambil jasa pinjaman
$jasaQuery = "SELECT id_jasa, persen FROM jasa WHERE id_koperasi = ? AND id_jasa=(SELECT MAX(id_jasa) FROM jasa WHERE id_koperasi = ?)";
$stmt = mysqli_prepare($conn, $jasaQuery);
mysqli_stmt_bind_param($stmt, 'ii', $_SESSION['id_koperasi'], $_SESSION['id_koperasi']);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $idJasa, $jasa_pinjaman);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

// ambil data id pinjaman terakhir
$idPinjamanQuery = "SELECT id_pinjaman_custom FROM pinjaman WHERE id_koperasi = ? ORDER BY id_pinjaman DESC LIMIT 1";
$stmt = mysqli_prepare($conn, $idPinjamanQuery);
mysqli_stmt_bind_param($stmt, 'i', $_SESSION['id_koperasi']);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $id_pinjaman_custom);
if (mysqli_stmt_fetch($stmt)) {
    // id_pinjaman_custom ditemukan
} else {
    // Jika tidak ada pinjaman sebelumnya, set ke 0
    $id_pinjaman_custom = 0;
}
mysqli_stmt_close($stmt);

// Proses form pinjaman
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_pinjaman_custom=$_POST['id_pinjaman'];
    $id_anggota = $_POST['namaanggota'];
    $jumlah = isset($_POST['Jumlah']) ? $_POST['Jumlah'] : '';
    $angsuran = isset($_POST['angsuran']) ? $_POST['angsuran'] : '';
    $tanggal = $_POST['tanggal'];

    // Cek apakah semua field diisi
    if (empty($id_anggota)) {
        $errors = "ID Anggota harus diisi.";
    } else if (empty($jumlah)) {
        $errors = "Jumlah Pinjaman harus diisi.";
    } else if (empty($angsuran)) {
        $errors = "Jumlah Angsuran harus diisi.";
    } else if (empty($tanggal)) {
        $errors = "Tanggal Pengajuan harus diisi.";
    }

    // ambil id anggota dari id_custom
    $anggotaQuery = "SELECT id_anggota FROM anggota WHERE id_custom = ? AND id_koperasi = ?";
    $stmt = mysqli_prepare($conn, $anggotaQuery);
    mysqli_stmt_bind_param($stmt, 'ii', $id_anggota, $_SESSION['id_koperasi']);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $id_anggota);
    if (mysqli_stmt_fetch($stmt)) {
        // id_anggota ditemukan
    } else {
        $errors = "ID Anggota tidak ditemukan.";
    }
    mysqli_stmt_close($stmt);

    //cek apakah id_pinjaman_custom sudah ada
        $checkQuery = "SELECT id_pinjaman_custom FROM pinjaman WHERE id_pinjaman_custom = ? AND id_koperasi = ?";
        $stmt = mysqli_prepare($conn, $checkQuery);
        mysqli_stmt_bind_param($stmt, 'si', $id_pinjaman_custom, $_SESSION['id_koperasi']);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $errors = "ID Pinjaman sudah ada. Silakan gunakan ID yang berbeda.";
        }
        mysqli_stmt_close($stmt);
    
    if (empty($errors)) {
        // Hitung total pinjaman dan angsuran
        $jumlah = floatval($jumlah);
        $ratio = floatval($jasa_pinjaman);
        $bunga = $jumlah * ($ratio / 100) * intval($angsuran) / 12; // Bunga per bulan
        $total_pinjaman = $jumlah + $bunga;
        $angsuran_bulanan = $total_pinjaman / intval($angsuran);

        // Simpan data pinjaman ke database
        $insertQuery = "INSERT INTO pinjaman (id_pinjaman_custom, id_koperasi, id_anggota, id_jasa, nilai_pinjaman, angsuran, nilai_pokok_angsuran, tgl_cair, sisa) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $insertQuery);
        $sisa = $total_pinjaman; // Sisa pinjaman awal sama dengan total pinjaman
        mysqli_stmt_bind_param($stmt, 'siiidddss', $id_pinjaman_custom, $_SESSION['id_koperasi'], $id_anggota, $idJasa, $jumlah, $angsuran, $angsuran_bulanan, $tanggal, $sisa);
        if (mysqli_stmt_execute($stmt)) {
            $sukses = "Data pinjaman berhasil disimpan.";
            header('refresh: 5; URL=/Dasbor/Transaksi/Pinjaman/');
        } else {
            $errors = "Gagal menyimpan data pinjaman: " . mysqli_error($conn); 
        }
    }
}
?>

<!DOCTYPE html>
<html data-bs-theme="light" lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Trisula</title>
    <link rel="stylesheet" href="/assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/swiper-icons.css">
    <link rel="stylesheet" href="/assets/fonts/fontawesome-all.min.css">
    <link rel="stylesheet" href="/assets/fonts/font-awesome.min.css">
    <link rel="stylesheet" href="/assets/fonts/fontawesome5-overrides.min.css">
    <link rel="stylesheet" href="/assets/css/styles.css">
    <link rel="icon" href="/assets/img/Trisula.png">
</head>

<body class="mt-0">
    <?php require_once '../../../Dasbor/aside.php'; ?>
    <main>
        <section class="d-flex justify-content-between">
            <div class="w-auto">
                <h1 class="jdl w-auto">Transaksi Pinjaman</h1>
                <div class="navatas">
                    <a class="linkatasfirst" href="/Dasbor/">Dasbor</a>
                    <a class="linkatas" href="/Dasbor/">
                        <span id="namakop">
                            <?php echo htmlspecialchars($nama_koperasi) ;?>
                        </span>
                    </a>
                    <a class="linkatas" href="/Dasbor/">Transaksi</a>
                    <p class="thissite">Pinjaman</p>
                </div>
            </div>
            <div class="dropdown d-none d-md-block">
              <a class="d-flex align-items-center profile " data-bs-toggle="dropdown">
                    <i class="far fa-user-circle icon-profile"></i>
                    <span id="namaakun" class="ms-2 me-2 namaakun">
                        <?php echo htmlspecialchars($nama_depan) . " " . htmlspecialchars($nama_belakang)?>
                    </span>
                </a>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="/Dasbor/Profil/">Profil</a>
                    <a class="dropdown-item" href="/Setings/">Ganti Password</a>
                    <a class="dropdown-item" href="/Logout/">Keluar</a></div>
            </div>
        </section>
        <section class="mt-5">
            <div class="container container-ang">
                <h3 class="text-center header-form p-2 rounded-4">Form Pengajuan Pinjaman</h3>
                <form method='post'>
                    <div class="form-group"><input class="form-control" type="text" id="nama" name="namaanggota" placeholder="ID Anggota" list="anggota">
                        <datalist id="anggota">
                            <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                                <option value="<?php echo htmlspecialchars($row['id_custom']); ?>">
                                    <?php echo htmlspecialchars($row['nama']); ?>
                                </option>
                            <?php endwhile; ?>
                        </datalist>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" name="id_pinjaman" placeholder="ID Pinjaman">
                        <div class="text-end">
                            <span class="profile">id pinjaman terakhir: <?php echo htmlspecialchars($id_pinjaman_custom);?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <input class="form-control" type="number" name="Jumlah" placeholder="Jumlah" value="<?php echo isset($_POST['Jumlah']) ? htmlspecialchars($_POST['Jumlah']) : ''; ?>" required>
                    </div>
                    <div class="form-group">
                        <input class="form-control" type="number" name="angsuran" placeholder="Jumlah Angsuran (bulan)" value="<?php echo isset($_POST['angsuran']) ? htmlspecialchars($_POST['angsuran']) : ''; ?>" required>
                    </div>
                    <div class="form-group">
                        <input class="form-control" id="tanggal" name="tanggal" type="date" placeholder="Tanggal Pengajuan" value="<?php echo isset($_POST['tanggal']) ? htmlspecialchars($_POST['tanggal']) : date('Y-m-d'); ?>" required>
                    </div>
                    <div class="form-group">
                        <div class='row'>
                            <p class="text-start col-5">Bunga Pinjaman</p>
                            <p class="col-1">:<//p>
                            <p class="text-start col-6" id="bunga"><?php echo htmlspecialchars($jasa_pinjaman)?> %</p>
                        </div>
                        <div class="row">
                            <p class="text-start col-5">Pinjaman + Bunga</p>
                            <p class="col-1">:</p>
                            <p class="text-start col-6" id="totalpinjamana"></p>
                        </div>
                        <div class="row">
                            <p class="text-start col-5">Angsuran Perbulan</p>
                            <p class="col-1">:</p>
                            <p class="text-start col-6" id='Angsuran'></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-primary btn-success" type="submit">💾 Simpan</button>
                        <a class="btn btn-primary btn-warning ms-2" type="button" href="/Dasbor/">❌ Batal</a>
                    </div>
                </form>
                <?php if (isset($errors)) : ?>
                    <div class="alert alert-danger mt-3">
                        <?php echo htmlspecialchars($errors); ?>
                    </div>
                <?php elseif (isset($sukses)) : ?>
                    <div class='alert alert-success mt-3'>
                        <?php echo htmlspecialchars($sukses); ?>
                        <a href="/Dasbor/Laporan/Pinjaman/">Lihat laporan</a>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </main>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const jumlahInput = document.querySelector('input[name="Jumlah"]');
            const angsuranInput = document.querySelector('input[name="angsuran"]');
            const bungaElement = document.getElementById('bunga');
            const totalPinjamanElement = document.getElementById('totalpinjamana');
            const angsuranElement = document.getElementById('Angsuran');

            function calculatePinjaman() {
                const jumlah = parseFloat(jumlahInput.value) || 0;
                const angsuran = parseInt(angsuranInput.value) || 0;
                const ratio = parseFloat(bungaElement.textContent) || 0;
                const bunga = jumlah*(ratio / 100)*angsuran/12;

                if (jumlah > 0 && angsuran > 0 && bunga > 0) {
                    let totalPinjaman = parseFloat((jumlah + bunga).toFixed(2));
                    totalPinjamanElement.textContent = "Rp. " + totalPinjaman.toLocaleString('id-ID');
                    let angsuranBulanan = parseFloat((totalPinjaman / angsuran).toFixed(2));
                    angsuranElement.textContent = "Rp. " + angsuranBulanan.toLocaleString('id-ID');
                } else {
                    totalPinjamanElement.textContent = '';
                    angsuranElement.textContent = '';
                }
            }

            jumlahInput.addEventListener('input', calculatePinjaman);
            angsuranInput.addEventListener('input', calculatePinjaman);
        });
    </script>
    <script src="/assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="/assets/js/script.js"></script>
</body>

</html>