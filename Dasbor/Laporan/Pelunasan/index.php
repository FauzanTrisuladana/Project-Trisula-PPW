<?php
require_once '../../../databaseconfig.php';
require_once '../../../sessionconfig.php';

// Mulai session
startSession();
$asal = 'Dasbor/Laporan/Pinjaman';
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
$totalQuery = "SELECT COUNT(*) as total FROM view_laporan_pelunasan WHERE id_koperasi = ? AND id_pinjaman_custom LIKE ?";
$totalStmt = mysqli_prepare($conn, $totalQuery);
mysqli_stmt_bind_param($totalStmt, 'is', $_SESSION['id_koperasi'], $search);
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
$query = "SELECT id_pelunasan, id_pinjaman_custom, angsuran_ke, nilai, tanggal FROM view_laporan_pelunasan WHERE id_koperasi = ? AND id_pinjaman_custom LIKE ? ORDER BY tanggal DESC LIMIT ? OFFSET ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 'isii', $_SESSION['id_koperasi'], $search, $perpageget, $offset);
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
                <h1 class="jdl w-auto">Laporan Pelunasan</h1>
                <div class="navatas">
                    <a class="linkatasfirst" href="/Dasbor/">Dasbor</a>
                    <a class="linkatas" href="/Dasbor/">
                        <span id="namakop">
                            <?php echo htmlspecialchars($nama_koperasi) ;?>
                        </span>
                    </a>
                    <a href="/Dasbor/" class="linkatas">Laporan</a>
                    <p class="thissite">Pelunasan</p>
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
            <div class="d-flex justify-content-between align-items-center flex-wrap header-action">
                <div class="input-group searchinput">
                    <input class="form-control" type="text" placeholder="Cari berdasarkan ID Pinjaman" id="search" value="<?php echo $search!=="%" ? htmlspecialchars($search) : ''; ?>">
                    <button class="btn btn-primary" type="button" onclick='Search()'>🔍 Cari</button>
                </div>
                <div class="d-flex align-items-center">
                    <select id="perpage" class="form-select">
                        <option disabled>Perhalaman</option>
                        <option value="10" <?php echo $perpage=='10' ? 'selected' : '' ?>>10</option>
                        <option value="50" <?php echo $perpage=='50' ? 'selected' : '' ?>>50</option>
                        <option value="100" <?php echo $perpage=='100' ? 'selected' : '' ?>>100</option>
                        <option value="all" <?php echo $perpage=='all' ? 'selected' : '' ?>>Semua</option>
                    </select>
                </div>
            </div>
            <div class="table-responsive wadah-table">
                <table class="table table-hover table-bordered">
                    <thead>
                        <tr>
                            <th class="tno">No</th>
                            <th>Tanggal</th>
                            <th>ID Pinjaman</th>
                            <th>Angsuran Ke-</th>
                            <th>Nilai</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = $offset + 1; ?>
                        <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                            <tr>
                                <td class="tno"><?php echo $no++; ?></td>
                                <td><?php echo htmlspecialchars($row['tanggal']); ?></td>
                                <td><?php echo htmlspecialchars($row['id_pinjaman_custom']); ?></td>
                                <td><?php echo htmlspecialchars($row['angsuran_ke']); ?></td>
                                <td>Rp. <?php echo number_format($row['nilai'], 2, ',', '.'); ?></td>
                                <?php if ($_SESSION['role'] != 'N') : ?>
                                    <td class="text-center">
                                        <a class="btn btn-danger" href="/Dasbor/Laporan/Pelunasan/Hapus/?id=<?php echo htmlspecialchars($row['id_pelunasan']); ?>">🗑️Hapus</a>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endwhile; ?>
                        <?php if ($totalData === 0) : ?>
                            <tr>
                                <td colspan="6" class="text-center">Tidak ada data yang ditemukan</td>
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
    <script src="/assets/js/script.js"></script>
    <script src="/assets/js/pagination.js"></script>
</body>

</html>