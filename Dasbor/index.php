<?php
require_once '../databaseconfig.php';
require_once '../sessionconfig.php';

// Mulai session
startSession();
$asal = 'Dasbor';
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
?>
<?php
// Ambol data untuk chart simpanan
$simpananQuery = 'SELECT total_pokok, total_wajib, total_sukarela FROM view_total_simpanan WHERE id_koperasi = ?';
$stmt = mysqli_prepare($conn, $simpananQuery);
mysqli_stmt_bind_param($stmt, 'i', $_SESSION['id_koperasi']);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $total_pokok, $total_wajib, $total_sukarela);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

// Ambil data untuk chart anggota
$anggotaQuery = 'SELECT total_laki, total_perempuan FROM view_total_anggota_per_koperasi WHERE id_koperasi = ?';
$stmt = mysqli_prepare($conn, $anggotaQuery);
mysqli_stmt_bind_param($stmt, 'i', $_SESSION['id_koperasi']);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $total_laki, $total_perempuan);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);         
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
    <?php require_once '../Dasbor/aside.php'; ?>
    <main>
        <section class="d-flex justify-content-between">
            <div class="w-auto">
                <h1 class="jdl w-auto">Dasbor</h1>
                <div class="navatas"><a class="linkatasfirst" href="#">Dasbor</a>
                    <p class="thissite">
                      <span id="namakop"><?php echo htmlspecialchars($nama_koperasi) ;?></span>
                    </p>
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
            <div class="row-cols-1 row row-cols-md-2 gy-4">
                <div class="col">
                    <div class="card p-2">
                        <h5 class="chartitle">Grafik Anggota</h5>
                        <canvas id="ChartAnggota"></canvas>
                    </div>
                </div>
                <div class="col">
                    <div class="card p-2">
                        <h5 class="chartitle">Grafik Simpanan</h5>
                        <canvas id="ChartSimpanan"></canvas>
                    </div>
                </div>
                <div class="col">
                    <div class="card p-2">
                        <h5 class="chartitle">Grafik Simpanan</h5>
                        <canvas id="myChart"></canvas>
                    </div>
                </div>
                <div class="col">
                    <div class="card p-2">
                        <h5 class="chartitle">Grafik Simpanan</h5>
                        <canvas id="nextchar">Custom Code</canvas>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="/assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="/assets/js/script.js"></script>
    <script src="/assets/js/chart.js"></script>
    <script>makechart(<?php echo $total_laki?>, 
      <?php echo $total_perempuan?>, 
      <?php echo $total_wajib?>,
      <?php echo $total_sukarela?>, 
      <?php echo $total_pokok?>)
    </script>
</body>

</html>