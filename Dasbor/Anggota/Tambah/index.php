<?php
require_once '../../../databaseconfig.php';
require_once '../../../sessionconfig.php';

// Mulai session
startSession();
$asal = 'Dasbor/Anggota/Tambah';
if (!isLoggedIn()) {
    header('Location: /Login/?redirect=' . urlencode($asal));
    exit();
}
if (!isHasAccess()) {
    header('Location: /Dasbor/Anggota/');
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

// Ambil data anggota terakhir untuk ID
$anggotaQuery = "SELECT AmbilIdCustomTerbaru() AS hasil";
$stmt = mysqli_prepare($conn, $anggotaQuery);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $id_custom);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

// ketika tombol simpan ditekan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_custom = $_POST['id-custom'];
    $nama = $_POST['nama'];
    $gender = $_POST['gender'];
    $email = $_POST['email'];
    $nomer = $_POST['nomer'];
    $alamat = $_POST['alamat'];
    $id_koperasi = $_SESSION['id_koperasi'];

    // Validasi cek apakah ID sudah ada
    $checkQuery = "SELECT CekIdCustomSudahAda(?) AS count";
    $checkStmt = mysqli_prepare($conn, $checkQuery);
    mysqli_stmt_bind_param($checkStmt, 's', $id_custom);
    mysqli_stmt_execute($checkStmt);
    mysqli_stmt_bind_result($checkStmt, $count);
    mysqli_stmt_fetch($checkStmt);
    mysqli_stmt_close($checkStmt);
    if ($count > 0) {
        $error= "ID Anggota sudah ada. Silakan gunakan ID yang berbeda.";
    } else {
        // Insert data anggota baru
        $insertQuery = "INSERT INTO anggota (id_custom, nama, no_wa, jenis_kelamin, email, alamat, id_koperasi, aktif) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $insertStmt = mysqli_prepare($conn, $insertQuery);
        $aktif = 'Y'; // Status aktif
        mysqli_stmt_bind_param($insertStmt, 'ssssssis', $id_custom, $nama, $nomer, $gender, $email, $alamat, $id_koperasi, $aktif);
        if (mysqli_stmt_execute($insertStmt)) {
            $sukses = "Anggota berhasil ditambahkan.";
            // Redirect ke halaman anggota
            header("Location: /Dasbor/Anggota/");
            exit();
        } else {
            $error = "Gagal menambahkan anggota: " . mysqli_error($conn); 
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
                <h1 class="jdl w-auto">Tambah Anggota</h1>
                <div class="navatas">
                    <a class="linkatasfirst" href="/Dasbor/">Dasbor</a>
                    <a class="linkatas" href="/Dasbor/">
                        <span id="namakop">
                            <?php echo htmlspecialchars($nama_koperasi) ;?>
                        </span>
                    </a>
                    <a class="linkatas" href="/Dasbor/Anggota/">Anggota</a>
                    <p class="thissite">Tambah</p>
                </div>
            </div>
            <div class="dropdown">
                <a class="d-flex align-items-center profile dropdown-toggle" data-bs-toggle="dropdown">
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
                <h3 class="text-center header-form p-2 rounded-4">Form Anggota</h3>
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php elseif (isset($sukses)): ?>
                    <div class="alert alert-success" role="alert">
                        <?php echo htmlspecialchars($sukses); ?>
                    </div>
                <?php endif; ?>
                <form method="POST">
                    <div class="form-group">
                        <input class="form-control" type="text" id="id-custom" name="id-custom" placeholder="ID Anggota">
                        <div class="text-end">
                            <span class="profile">id anggota terakhir: <?php echo htmlspecialchars($id_custom);?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <input class="form-control" type="text" id="nama" name="nama" placeholder="Nama Anggota" required>
                    </div>
                    <div class="form-group">
                        <select class="form-select" name="gender" required>
                            <option value="" selected disabled>--Pilih Gender--</option>
                            <option value="L">Laki-Laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <input class="form-control" type="email" id="email" name="email" placeholder="Email">
                    </div>
                    <div class="form-group">
                        <input class="form-control" type="tel" id="nomer" name="nomer" placeholder="No WA 08***** / 8*****" required>
                    </div>
                    <div class="form-group">
                        <textarea class="form-control" id="alamat" name="alamat" placeholder="Alamat Rumah" required></textarea>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-primary btn-success" type="submit" name="simpan">💾 Simpan</button>
                        <a class="btn btn-primary btn-warning ms-2" type="button" href="/Dasbor/Anggota/">❌ Batal</a>
                    </div>
                </form>
            </div>
        </section>
    </main>
    <script src="/assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="/assets/js/script.js"></script>
</body>

</html>