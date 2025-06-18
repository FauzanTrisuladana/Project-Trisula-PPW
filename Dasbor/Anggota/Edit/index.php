<?php
require_once '../../../databaseconfig.php';
require_once '../../../sessionconfig.php';

// Mulai session
startSession();
$asal = 'Dasbor/Anggota/Edit';
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

// Ambil data anggota berdasarkan ID yang diberikan
$anggotaQuery = "SELECT id_custom, nama, jenis_kelamin, email, no_wa, alamat, id_koperasi FROM anggota WHERE id_anggota = ?";
$stmt = mysqli_prepare($conn, $anggotaQuery);
mysqli_stmt_bind_param($stmt, 'i', $_GET['id']);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $id_custom, $nama, $gender, $email, $nomer, $alamat, $id_koperasi);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

if ($id_koperasi !== $_SESSION['id_koperasi']) {
    // Jika anggota tidak milik koperasi ini, redirect ke halaman anggota
    header("Location: /Dasbor/Anggota/");
    exit();
}

// ketika tombol simpan ditekan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_custom = $_POST['id-custom'];
    $nama = $_POST['nama'];
    $gender = $_POST['gender'];
    $email = $_POST['email'];
    $nomer = $_POST['nomer'];
    $alamat = $_POST['alamat'];
    $id_koperasi = $_SESSION['id_koperasi'];

    // Validasi cek apakah ID sudah ada tapi dengan pengecualian untuk ID yang sedang diedit
    $checkQuery = "SELECT CekIdCustomSudahAdaKecuali(?, ?) AS count";
    $checkStmt = mysqli_prepare($conn, $checkQuery);
    mysqli_stmt_bind_param($checkStmt, 'si', $id_custom, $_GET['id']);
    mysqli_stmt_execute($checkStmt);
    mysqli_stmt_bind_result($checkStmt, $count);
    mysqli_stmt_fetch($checkStmt);
    mysqli_stmt_close($checkStmt);
    if ($count > 0) {
        $error= "ID Anggota sudah ada. Silakan gunakan ID yang berbeda.";
    } else {
        // update data anggota
        $update = "UPDATE anggota 
        SET 
            id_custom = ?, 
            nama = ?, 
            no_wa = ?, 
            jenis_kelamin = ?, 
            email = ?, 
            alamat = ?
        WHERE id_anggota = ?;
        ";
        $stmt = mysqli_prepare($conn, $update);
        mysqli_stmt_bind_param($stmt, 'ssssssi', $id_custom, $nama, $nomer, $gender, $email, $alamat, $_GET['id']);
        if (mysqli_stmt_execute($stmt)) {
            $sukses = "Data anggota berhasil diperbarui.";
            // Redirect ke halaman anggota
            header("Location: /Dasbor/Anggota/");
            exit();
        exit();
        } else {
            $error = "Gagal memperbarui data anggota: " . mysqli_error($conn);
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
                <h1 class="jdl w-auto">Edit Anggota</h1>
                <div class="navatas">
                    <a class="linkatasfirst" href="/Dasbor/">Dasbor</a>
                    <a class="linkatas" href="/Dasbor/">
                        <span id="namakop">
                            <?php echo htmlspecialchars($nama_koperasi) ;?>
                        </span>
                    </a>
                    <a class="linkatas" href="/Dasbor/Anggota/">Anggota</a>
                    <p class="thissite">Edit</p>
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
            <div class="container-ang container">
                <h3 class="text-center header-form p-2 rounded-4">Edit Anggota</h3>
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
                        <input class="form-control" type="text" id="id-custom" name="id-custom" placeholder="ID Anggota" value="<?php echo htmlspecialchars($id_custom); ?>" required>
                    </div>
                    <div class="form-group">
                        <input class="form-control" type="text" id="nama" name="nama" placeholder="Nama Anggota" required value="<?php echo htmlspecialchars($nama); ?>">
                    </div>
                    <div class="form-group">
                        <select class="form-select" name="gender" required>
                            <option value="" disabled>--Pilih Gender--</option>
                            <option value="L" <?php echo $gender=='L' ? 'selected' : ''; ?>>Laki-Laki</option>
                            <option value="P" <?php echo $gender=='P' ? 'selected' : ''; ?>>Perempuan</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <input class="form-control" type="email" id="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($email); ?>" required>
                    </div>
                    <div class="form-group">
                        <input class="form-control" type="tel" id="nomer" name="nomer" placeholder="No WA 08***** / 8*****" required value="<?php echo htmlspecialchars($nomer); ?>">
                    </div>
                    <div class="form-group">
                        <textarea class="form-control" id="alamat" name="alamat" placeholder="Alamat Rumah" required><?php echo htmlspecialchars($alamat); ?></textarea>
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