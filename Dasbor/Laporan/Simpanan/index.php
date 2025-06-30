<?php
require_once '../../../databaseconfig.php';
require_once '../../../sessionconfig.php';

// Mulai session
startSession();
$asal = 'Dasbor/Laporan/Simpanan';
if (!isLoggedIn()) {
    header('Location: /Login/?redirect=' . urlencode($asal));
    exit();
}
?>

<?php
// Ambil data dari URL
$perpage = isset($_GET['perpage']) ? $_GET['perpage'] : '10';
$search = isset($_GET['search']) ? $_GET['search'] : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$pokok = isset($_GET['pokok']) ? (int)$_GET['pokok'] : 1;
$wajib = isset($_GET['wajib']) ? (int)$_GET['wajib'] : 1;
$sukarela = isset($_GET['sukarela']) ? (int)$_GET['sukarela'] : 1;
// Validasi input
if (!is_numeric($pokok) || $pokok < 0 || $pokok > 1) {
    $pokok = 1; // Set default jika tidak valid
}
if (!is_numeric($wajib) || $wajib < 0 || $wajib > 1) {
    $wajib = 1; // Set default jika tidak valid
}
if (!is_numeric($sukarela) || $sukarela < 0 || $sukarela > 1) {
    $sukarela = 1; // Set default jika tidak valid
}
// ubah pokok, wajib, dan sukarela menjadi string untuk query
$pokokquer = $pokok===1 ? 'pokok' : '';
$wajibquer = $wajib===1 ? 'wajib' : '';
$sukarelaquer = $sukarela===1 ? 'sukarela' : '';
// Validasi perpage
$valid_perpage = ['10', '50', '100', 'all'];
if (!in_array($perpage, $valid_perpage)) {
    $perpage = '10'; // Default value
}
// Validasi page
if ($page < 1) {
    $page = 1; // Set page ke 1 jika kurang dari 1
}
// Validasi search
$search = htmlspecialchars(trim($search)); // Sanitasi input

if ($search !== '') {
    $search = '%' . $search . '%'; // Tambahkan wildcard untuk LIKE query
} else {
    $search = '%'; // Jika tidak ada pencarian, ambil semua data
}
// Ambil total data untuk pagination
$totalQuery = "SELECT COUNT(*) as total FROM view_laporan_simpanan 
WHERE id_koperasi = ? 
AND (id_custom_anggota LIKE ? OR nama_anggota LIKE ?) 
AND (jenis_simpanan = ? OR jenis_simpanan = ? OR jenis_simpanan = ?)";

$totalStmt = mysqli_prepare($conn, $totalQuery);
mysqli_stmt_bind_param($totalStmt, 'isssss', $_SESSION['id_koperasi'], $search, $search, $pokokquer, $wajibquer, $sukarelaquer);
mysqli_stmt_execute($totalStmt);
$totalResult = mysqli_stmt_get_result($totalStmt);
$totalRow = mysqli_fetch_assoc($totalResult);
$totalData = $totalRow['total'];

// Hitung total halaman perpageget=perpage final
if ($perpage === 'all') {
    $perpageget = $totalData; // Set perpageget ke total data jika 'all'
} else {
    $perpageget = (int)$perpage; // Pastikan perpageget adalah integer
}
if ($perpageget <= 0) {
    $perpageget = 10; // Set default perpageget jika tidak valid
}
$totalPages = ceil($totalData / $perpageget);
// Cek apakah halaman melebihi total halaman
if ($page > $totalPages && $totalPages > 0) {
    $page = $totalPages; // Set ke halaman terakhir jika melebihi
}

// hitung offset
$offset = ($page - 1) * $perpageget;
// Query untuk mengambil data simpanan
$query = "SELECT id_simpanan, id_custom_anggota, nama_anggota, jenis_simpanan, nilai, tanggal, keterangan 
FROM view_laporan_simpanan 
WHERE id_koperasi = ? 
AND (id_custom_anggota LIKE ? OR nama_anggota LIKE ?) 
AND jenis_simpanan IN (?, ?, ?)
ORDER BY tanggal DESC
LIMIT ? OFFSET ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 'isssssii', $_SESSION['id_koperasi'], $search, $search, $pokokquer, $wajibquer, $sukarelaquer, $perpageget, $offset);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
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
                <h1 class="jdl w-auto">Laporan Simpanan</h1>
                <div class="navatas">
                    <a class="linkatasfirst" href="/Dasbor/">Dasbor</a>
                    <a class="linkatas" href="/Dasbor/">
                        <span id="namakop">
                            <?php echo htmlspecialchars($nama_koperasi) ;?>
                        </span>
                    </a>
                    <a href="/Dasbor/" class="linkatas">Laporan</a>
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
            <div class="d-flex justify-content-between align-items-center flex-wrap header-action">
                <div class="input-group searchinput">
                    <input class="form-control" type="text" placeholder='Cari ID atau Nama Anggota' id='search' value="<?php echo $search!=="%" ? htmlspecialchars($search) : ''; ?>">
                    <button class="btn btn-primary" type="button" onclick="Search()">🔍 Cari</button>
                </div>
                <div class="d-flex align-items-center flex-wrap">
                    <div class="form-check me-2">
                        <input class="form-check-input" type="checkbox" id="pokokaktif" <?php echo $pokok!=0 ? 'checked' : ''; ?>>
                        <label class="form-check-label checaktif" for="pokokaktif">Pokok</label>
                    </div>
                    <div class="form-check me-2">
                        <input class="form-check-input" type="checkbox" id="wajibaktif" <?php echo $wajib!=0 ? 'checked' : ''; ?>>
                        <label class="form-check-label checaktif" for="wajibaktif">Wajib</label>
                    </div>
                    <div class="form-check me-2">
                        <input class="form-check-input" type="checkbox" id="sukarelaaktif" <?php echo $sukarela!=0 ? 'checked' : ''; ?>>
                        <label class="form-check-label checaktif" for="sukarelaaktif">Sukarela</label>
                    </div>
                    <div class="form-check me-2">
                        <select id="perpage" class="form-select">
                            <option disabled>Perhalaman</option>
                            <option value="10" <?php echo $perpage=='10' ? 'selected' : '' ?>>10</option>
                            <option value="50" <?php echo $perpage=='50' ? 'selected' : '' ?>>50</option>
                            <option value="100" <?php echo $perpage=='100' ? 'selected' : '' ?>>100</option>
                            <option value="all" <?php echo $perpage=='all' ? 'selected' : '' ?>>Semua</option>
                        </select>
                    </div>
                    
                </div>
            </div>
            <div class="table-responsive wadah-table">
                <table class="table table-hover table-bordered">
                    <thead>
                        <tr>
                            <th class="tno">No</th>
                            <th>Tanggal</th>
                            <th>ID Anggota</th>
                            <th>Nama Anggota</th>
                            <th>Jenis Simpanan</th>
                            <th>Nilai</th>
                            <th>Keterangan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = $offset + 1; ?>
                        <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                            <tr>
                                <td class="tno"><?php echo $no++; ?></td>
                                <td><?php echo htmlspecialchars($row['tanggal']); ?></td>
                                <td><?php echo htmlspecialchars($row['id_custom_anggota']); ?></td>
                                <td><?php echo htmlspecialchars($row['nama_anggota']); ?></td>
                                <td><?php echo htmlspecialchars($row['jenis_simpanan']); ?></td>
                                <td>Rp. <?php echo number_format($row['nilai'], 2, ',', '.'); ?></td>
                                <td><?php echo htmlspecialchars($row['keterangan']); ?></td>
                                <?php if ($_SESSION['role'] != 'N' && $row['jenis_simpanan']!='pokok'): ?>
                                <td class="text-center">
                                    <a class="btn btn-danger" type="button" href="/Dasbor/Laporan/Simpanan/Hapus/?id=<?php echo $row['id_simpanan']; ?>">🗑️Hapus</a>
                                </td>
                                <?php endif; ?>
                            </tr>
                        <?php endwhile; ?>
                        <?php if ($totalData === 0) : ?>
                            <tr>
                                <td colspan="8" class="text-center">Tidak ada data yang ditemukan</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center card-footer">
                <?php if ($totalData > 0 && $page > 1) : ?>
                <button class="btn btn-primary me-3" type="button" onclick="PrevPage()">Sebelumnya</button>
                <?php endif; ?>
                <?php if ($totalData > 0 && $page < $totalPages) : ?>
                <button class="btn btn-primary" type="button" onclick="NextPage(<?php echo($totalPages)?>)">Selanjutnya</button>
                <?php endif; ?>
            </div>
        </section>
    </main>
    <script src="/assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="/Dasbor/Laporan/Simpanan/script.js"></script>
    <script src="/assets/js/pagination.js"></script>
    <script src="/Dasbor/Laporan/Simpanan/check.js"></script>
</body>

</html>