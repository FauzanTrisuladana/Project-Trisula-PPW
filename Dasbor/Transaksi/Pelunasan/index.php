<?php
require_once '../../../databaseconfig.php';
require_once '../../../sessionconfig.php';

// Mulai session
startSession();
$asal = 'Dasbor/Transaksi/Pelunasan';
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

//Ambil data pinjaman
$pinjamanQuery = "SELECT id_pinjaman_custom, nama, nilai_pokok_angsuran, pelunasan_terakhir FROM pinjaman p JOIN anggota a ON p.id_anggota=a.id_anggota WHERE p.id_koperasi = ?";
$stmt = mysqli_prepare($conn, $pinjamanQuery);
mysqli_stmt_bind_param($stmt, 'i', $_SESSION['id_koperasi']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($result)) {
    $pinjamandata[] = $row;
}
mysqli_stmt_close($stmt);

// Proses form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_pinjaman = $_POST['id_pinjaman'];
    $tanggal = $_POST['tanggal'];

    //validasi id_pinjaman_custom ada di database
    $query = "SELECT COUNT(*) FROM pinjaman WHERE id_pinjaman_custom = ? AND id_koperasi = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'si', $id_pinjaman, $_SESSION['id_koperasi']);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $count);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
    if ($count == 0) {
        $error = "ID Pinjaman tidak ditemukan atau tidak valid.";
    }

    // Validasi input
    if (empty($tanggal)) {
        $error= "Tanggal harus diisi.";
    }

    if (empty($error)) {
        // Ambil data angsuran terakhir dan jumlah dari pinjaman
        $query = "SELECT id_pinjaman, pelunasan_terakhir, nilai_pokok_angsuran FROM pinjaman WHERE id_pinjaman_custom = ? AND id_koperasi = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 'si', $id_pinjaman, $_SESSION['id_koperasi']);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt,$id_pinjamanreel, $pelunasan_terakhir, $jumlah);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);

        // Increment angsuran terakhir
        $pelunasan_terakhir += 1;

        // Insert data pelunasan
        $query = "INSERT INTO pelunasan (id_pinjaman, tanggal, angsuran_ke, nilai, id_koperasi) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 'ssdii', $id_pinjamanreel, $tanggal, $pelunasan_terakhir, $jumlah, $_SESSION['id_koperasi']);
        if (mysqli_stmt_execute($stmt)) {
            $sukses = "Data pelunasan berhasil disimpan.";
            header('refresh: 5; url=/Dasbor/Transaksi/Pelunasan/');
        } else {
            $error = "Gagal menyimpan data pelunasan.";
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
                <h1 class="jdl w-auto">Transaksi Pelunasan</h1>
                <div class="navatas">
                    <a class="linkatasfirst" href="/Dasbor/">Dasbor</a>
                    <a class="linkatas" href="/Dasbor/">
                        <span id="namakop">
                            <?php echo htmlspecialchars($nama_koperasi) ;?>
                        </span>
                    </a>
                    <a class="linkatas" href="/Dasbor/">Transaksi</a>
                    <p class="thissite">Pelunasan</p>
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
                    <a class="dropdown-item" href="/Logout/">Keluar</a>
                </div>
            </div>
        </section>
        <section class="mt-5">
            <div class="container container-ang">
                <h3 class="text-center header-form p-2 rounded-4">Form Pelunasan</h3>
                <form method='post' >
                    <div class="form-group">
                        <input class="form-control" type="text" id="id-pinjaman" name="id_pinjaman" placeholder="Id Pinjaman" list="anggota">
                        <datalist id="anggota">
                            <?php foreach ($pinjamandata as $pinjaman): ?>
                                <option value="<?php echo htmlspecialchars($pinjaman['id_pinjaman_custom']); ?>">
                                    <?php echo htmlspecialchars($pinjaman['nama']); ?>
                                </option>
                            <?php endforeach; ?>
                        </datalist>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <p class="text-start col-4">Angsuran ke-</p>
                            <p class="text-start col-8" id="anggsuran"></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <input class="form-control" id="tanggal" name="tanggal" placeholder="Tanggal" type="date" value="<?php echo isset($_POST['tanggal']) ? htmlspecialchars($_POST['tanggal']) : date('Y-m-d'); ?>" required>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <p class="text-start col-4">Jumlah</p>
                            <p class="col-1">:</p>
                            <p class="text-start col-7" id="jumlah"></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-primary btn-success" type="submit">💾 Simpan</button>
                        <a class="btn btn-primary btn-warning ms-2" type="button" href="/Dasbor/">❌ Batal</a>
                    </div>
                </form>
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger mt-3" role="alert">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php elseif (isset($sukses)): ?>
                    <div class="alert alert-success mt-3" role="alert">
                        <?php echo htmlspecialchars($sukses); ?>
                        <a href="/Dasbor/Laporan/Pelunasan/">Lihat Laporan</a>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </main>
    <script>
        window.pinjamanData = <?php echo json_encode($pinjamandata); ?>;
    </script>
    <script src="/Dasbor/Transaksi/Pelunasan/pelunasan.js"></script>
    <script src="/assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="/assets/js/script.js"></script>
</body>
</html>