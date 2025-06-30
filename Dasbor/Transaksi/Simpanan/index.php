<?php
require_once '../../../databaseconfig.php';
require_once '../../../sessionconfig.php';

// Mulai session
startSession();
$asal = 'Dasbor/Transaksi/Simpanan';
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
?>
<?php
// Proses form simpanan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_anggota = $_POST['id_anggota'];
    $jenisSimpanan = $_POST['jenisSimpanan'];
    $jumlah = isset($_POST['Jumlah']) ? $_POST['Jumlah'] : '';
    $tanggal = $_POST['nomer'];
    $keterangan = $_POST['keterangan'];

    //cek sudah diisi atau belum
    if (empty($id_anggota)) {
        $errors = "ID Anggota harus diisi.";
    } else if (empty($jenisSimpanan)) {
        $errors = "Jenis Simpanan harus dipilih.";
    } else if (empty($tanggal)) {
        $errors = "Tanggal harus diisi.";
    } else if (empty($keterangan)) {
        $errors = "Keterangan harus diisi.";
    }

    // ambil id_anggota dari database
    $anggotaQuery = "SELECT id_anggota FROM anggota WHERE id_custom = ? AND id_koperasi = ?";
    $stmt = mysqli_prepare($conn, $anggotaQuery);
    mysqli_stmt_bind_param($stmt, 'si', $id_anggota, $_SESSION['id_koperasi']);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $id_anggota);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
    if (empty($id_anggota)) {
        $errors = "ID Anggota tidak ditemukan.";
    }
    // Validasi jumlah untuk Simpanan Sukarela dan Wajib

    if ($jenisSimpanan == '1' && empty($jumlah)) {
        $errors = "Jumlah untuk Simpanan Sukarela Harus di isi diisi.";
    } else if ($jenisSimpanan == '2') {
        // ambil data simpanan wajib dari database
        $simpananWajibQuery = "SELECT simpanan_wajib FROM koperasi WHERE id_koperasi = ?";
        $stmt = mysqli_prepare($conn, $simpananWajibQuery);
        mysqli_stmt_bind_param($stmt, 'i', $_SESSION['id_koperasi']);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $jumlah);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);

    if (!is_numeric($jumlah) || $jumlah <= 0) {
        $errors = "Jumlah harus berupa angka positif.";
    }
    
    }
    if (empty($errors)) {
        // Insert data simpanan ke database
        $insertQuery = "INSERT INTO simpanan (id_koperasi, id_anggota, id_jenis, nilai, tanggal, keterangan) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $insertQuery);
        $id_koperasi = $_SESSION['id_koperasi'];
        mysqli_stmt_bind_param($stmt, 'iiiiss', $id_koperasi, $id_anggota, $jenisSimpanan, $jumlah, $tanggal, $keterangan);
        if (mysqli_stmt_execute($stmt)) {
            // Redirect ke halaman simpanan setelah berhasil
            header('Refresh: 5; URL=/Dasbor/Transaksi/Simpanan/');
            $sukses = "Data simpanan berhasil disimpan.";
        } else {
            $errors = "Gagal menyimpan data simpanan: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
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
                <h1 class="jdl w-auto">Transaksi Simpanan</h1>
                <div class="navatas">
                    <a class="linkatasfirst" href="/Dasbor/">Dasbor</a>
                    <a class="linkatas" href="/Dasbor/">
                        <span id="namakop">
                            <?php echo htmlspecialchars($nama_koperasi) ;?>
                        </span>
                    </a>
                    <a class="linkatas" href="/Dasbor/">Transaksi</a>
                    <p class="thissite">Simpanan</p>
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
                <h3 class="text-center header-form p-2 rounded-4">Form Simpanan</h3>
                <form method='post'>
                    <div class="form-group"><input class="form-control" type="text" id="id_anggota" name="id_anggota" placeholder="ID Anggota" list="anggota" value="<?php echo isset($_POST['id_anggota']) ? htmlspecialchars($_POST['id_anggota']) : ''; ?>" required>
                        <datalist id="anggota">
                            <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                                <option value="<?php echo htmlspecialchars($row['id_custom']); ?>">
                                    <?php echo htmlspecialchars($row['nama']); ?>
                                </option>
                            <?php endwhile; ?>
                        </datalist>
                    </div>
                    <div class="d-flex form-group">
                        <div class="form-check me-3">
                            <input class="form-check-input" type="radio" id="formCheck-1" name="jenisSimpanan" value='2'>
                            <label class="form-check-label" for="formCheck-1">Simpanan Wajib</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" id="formCheck-2" name="jenisSimpanan" value='1'>
                            <label class="form-check-label" for="formCheck-2">Simpanan Sukarela</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <input class="form-control" type='number' id="jumlah" name="Jumlah" placeholder="Jumlah">
                    </div>
                    <div class="form-group">
                        <input class="form-control" id="nomer" name="nomer" placeholder="Tanggal" type="date" value="<?php echo isset($_POST['nomer']) ? htmlspecialchars($_POST['nomer']) : date('Y-m-d'); ?>" required>
                    </div>
                    <div class="form-group">
                        <textarea class="form-control" id="keterangan" name="keterangan" placeholder="Keteragan"></textarea>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-primary btn-success" type="submit">💾 Simpan</button>
                        <a class="btn btn-primary btn-warning ms-2" type="button" href="/Dasbor/">❌ Batal</a>
                    </div>
                </form>
                <?php if (isset($errors)) : ?>
                    <div class="alert alert-danger mt-3" role="alert">
                        <?php echo htmlspecialchars($errors); ?>
                    </div>
                <?php elseif (isset($sukses)) : ?>
                    <div class='alert alert-success mt-3' role='alert'>
                        <?php echo htmlspecialchars($sukses); ?>
                        <a href="/Dasbor/Laporan/Simpanan/">Lihat Laporan</a>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </main>
    <script>
        const check1 = document.getElementById("formCheck-1");
        const check2 = document.getElementById("formCheck-2");
        const tambahan = document.getElementById("jumlah");

        check1.addEventListener("change", () => {
        if (check1.checked) tambahan.style.display = "none";
        });

        check2.addEventListener("change", () => {
        if (check2.checked) tambahan.style.display = "block";
        });
    </script>
    <script src="/assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="/assets/js/script.js"></script>
</body>

</html>