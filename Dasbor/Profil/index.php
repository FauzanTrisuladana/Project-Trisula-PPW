<?php
require_once '../../databaseconfig.php';
require_once '../../sessionconfig.php';

// Mulai session
startSession();
$asal = 'Dasbor/Profil';
if (!isLoggedIn()) {
    header('Location: /Login/?redirect=' . urlencode($asal));
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $namadepan = $_POST['namadepan'] ?? '';
    $namabelakang = $_POST['namabelakang'] ?? '';

    // Update data profil di database
    $updateQuery = "UPDATE akun SET nama_depan = ?, nama_belakang = ? WHERE id_akun = ?";
    $stmt = mysqli_prepare($conn, $updateQuery);
    mysqli_stmt_bind_param($stmt, 'ssi', $namadepan, $namabelakang, $_SESSION['id_akun']);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Redirect ke halaman profil setelah update
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
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
    <?php require_once '../../Dasbor/aside.php'; ?>
    <main>
        <section class="d-flex justify-content-between">
            <div class="w-auto">
                <h1 class="jdl w-auto">Profil</h1>
                <div class="navatas">
                    <a class="linkatasfirst" href="/Dasbor/">Dasbor</a>
                    <a class="linkatas" href="/Dasbor/">
                        <span id="namakop">
                            <?php echo htmlspecialchars($nama_koperasi) ;?>
                        </span>
                    </a>
                    <p class="thissite">Profil</p>
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
                    <a class="dropdown-item" href="#">Profil</a>
                    <a class="dropdown-item" href="/Setings/">Ganti Password</a>
                    <a class="dropdown-item" href="/Logout/">Keluar</a>
                </div>
            </div>
        </section>
        <section class="mt-5">
            <div class="col">
                <div class="card m-2">
                    <div class="text-center card-header">
                        <h5>Profil Akun</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <p class="text-start col-5">Nama Depan</p>
                            <p class="col-1">:</p>
                            <p class="text-start col-6" id="namadepan"><?php echo htmlspecialchars($nama_depan)?></p>
                        </div>
                        <div class="row">
                            <p class="text-start col-5">Nama Belakang</p>
                            <p class="col-1">:</p>
                            <p class="text-start col-6" id="namabelakang"><?php echo htmlspecialchars($nama_belakang);?></p>
                        </div>
                    </div>
                    <div class="text-center card-footer">
                        <button class="btn btn-warning text-white" type="button" data-bs-toggle="modal" data-bs-target="#edit-pa">
                            ✏️ Edit&nbsp;
                        </button>
                    </div>
                </div>
            </div>
        </section>
        <?php if (isset($error)): ?>
            <div class="alert alert-warning" role="alert">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
    </main>
    <div id="edit-pa" class="modal fade" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Akun</h5>
                    <button class="btn btn-close" type="button" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST">
                        <div class="form-group">
                            <input class="form-control" type="text" id="nama-depan" name="namadepan" placeholder="Nama Depan" value="<?php echo htmlspecialchars($nama_depan); ?>">
                        </div>
                        <div class="form-group">
                            <input class="form-control" type="text" id="nama-belakang" name="namabelakang" placeholder="Nama Belakang" value="<?php echo htmlspecialchars($nama_belakang); ?>">
                        </div>
                        <div class="text-end form-group">
                            <button class="btn btn-primary btn-success" type="submit">💾 Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="/assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="/assets/js/script.js"></script>
</body>

</html>