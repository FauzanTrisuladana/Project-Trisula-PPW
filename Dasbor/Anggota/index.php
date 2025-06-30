<?php
require_once '../../databaseconfig.php';
require_once '../../sessionconfig.php';

// Mulai session
startSession();
$asal = 'Dasbor/Anggota';
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

// ambil search query jika ada
$search = isset($_GET['cari']) ? $_GET['cari'] : '';
// ambil status = 'aktif' atau 'nonaktif'

$aktif = isset($_GET['aktif']) ? $_GET['aktif'] : '';
$nonaktif = isset($_GET['nonaktif']) ? $_GET['nonaktif'] : '';

if ($aktif == 'Y' && $nonaktif == 'N') {
    $aktif = 'Y';
    $nonaktif = 'N';
    $status = '';
} elseif ($aktif == 'Y' && $nonaktif == '') {
    $aktif = 'Y';
    $nonaktif = '';
    $status = 'nonaktif';
} elseif ($aktif == '' && $nonaktif == 'N') {
    $aktif = '';
    $nonaktif = 'N';
    $status = 'nonaktif';
} else {
    $aktif = 'Y';
    $nonaktif = 'N';
    $status = '';
}
// Tambahkan kondisi untuk status anggota
// Ambil data anggota
$anggotaQuery = "SELECT * 
FROM anggota 
WHERE id_koperasi = ? AND (id_custom LIKE ? OR nama LIKE ?) AND (aktif = ? OR aktif = ?) 
ORDER BY id_anggota ASC";
$stmt = mysqli_prepare($conn, $anggotaQuery);
$searchParam = '%' . $search . '%';
mysqli_stmt_bind_param($stmt, 'issss', $_SESSION['id_koperasi'], $searchParam, $searchParam, $aktif, $nonaktif);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$anggotaData = mysqli_fetch_all($result, MYSQLI_ASSOC);
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
    <?php require_once '../../Dasbor/aside.php'; ?>
    <main>
        <section class="d-flex justify-content-between">
            <div class="w-auto">
                <h1 class="jdl w-auto">Anggota</h1>
                <div class="navatas">
                    <a class="linkatasfirst" href="/Dasbor/">Dasbor</a>
                    <a class="linkatas" href="/Dasbor/">
                        <span id="namakop">
                            <?php echo htmlspecialchars($nama_koperasi) ;?>
                        </span>
                    </a>
                    <p class="thissite">Anggota</p>
                </div>
            </div>
            <div class="dropdown d-none d-md-block">
              <a class="d-flex align-items-center profile " href="#" data-bs-toggle="dropdown">
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
            <form class="d-flex justify-content-between align-items-center flex-wrap header-action" method="GET">
                <div class="searchinput input-group">
                    <input class="form-control" type="text" placeholder="Cari berdasarkan ID Anggota, Nama Anggota" value="<?php echo htmlspecialchars($search); ?>" name="cari" id="cari">
                    <button class="btn btn-primary" type="submit">🔍 Cari</button>
                </div>
                <div class="d-flex align-items-center">
                    <div class="form-check me-2 align-items-center d-flex">
                        <input class="form-check-input me-1" type="checkbox" id="formCheck-1" name="aktif" value="Y" <?php echo empty($status) || $aktif=='Y' ? 'checked' : ''; ?>>
                        <label class="form-check-label checaktif" for="formCheck-1">Aktif</label>
                    </div>
                    <div class="form-check me-3 align-items-center d-flex">
                        <input class="form-check-input me-1" type="checkbox" id="formCheck-2" name="nonaktif" value="N" <?php echo empty($status) || $nonaktif=='N' ? 'checked' : ''; ?>>
                        <label class="form-check-label checaktif" for="formCheck-2">Non-Aktif</label>
                    </div>
                    <button class="btn btn-primary me-2" type='submit'>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="1em" height="1em" fill="currentColor">
                            <!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free (Icons: CC BY 4.0, Fonts: SIL OFL 1.1, Code: MIT License) Copyright 2023 Fonticons, Inc. -->
                            <path d="M105.1 202.6c7.7-21.8 20.2-42.3 37.8-59.8c62.5-62.5 163.8-62.5 226.3 0L386.3 160H336c-17.7 0-32 14.3-32 32s14.3 32 32 32H463.5c0 0 0 0 0 0h.4c17.7 0 32-14.3 32-32V64c0-17.7-14.3-32-32-32s-32 14.3-32 32v51.2L414.4 97.6c-87.5-87.5-229.3-87.5-316.8 0C73.2 122 55.6 150.7 44.8 181.4c-5.9 16.7 2.9 34.9 19.5 40.8s34.9-2.9 40.8-19.5zM39 289.3c-5 1.5-9.8 4.2-13.7 8.2c-4 4-6.7 8.8-8.1 14c-.3 1.2-.6 2.5-.8 3.8c-.3 1.7-.4 3.4-.4 5.1V448c0 17.7 14.3 32 32 32s32-14.3 32-32V396.9l17.6 17.5 0 0c87.5 87.4 229.3 87.4 316.7 0c24.4-24.4 42.1-53.1 52.9-83.7c5.9-16.7-2.9-34.9-19.5-40.8s-34.9 2.9-40.8 19.5c-7.7 21.8-20.2 42.3-37.8 59.8c-62.5 62.5-163.8 62.5-226.3 0l-.1-.1L125.6 352H176c17.7 0 32-14.3 32-32s-14.3-32-32-32H48.4c-1.6 0-3.2 .1-4.8 .3s-3.1 .5-4.6 1z"></path>
                        </svg>
                    </button>
                    <?php if ($_SESSION['role']!='N'): ?>
                    <a class="add-button" href="/Dasbor/Anggota/Tambah/">➕ Anggota</a>
                    <?php endif; ?>
                </div>
            </form>
            <div class="table-responsive wadah-table">
                <table class="table table-hover table-bordered">
                    <thead>
                        <tr>
                            <th class="tno">No</th>
                            <th>ID Anggota</th>
                            <th>Nama Anggota</th>
                            <th>No WA</th>
                            <th>Email</th>
                            <th>Alamat</th>
                            <th>Status</th>
                            <?php if ($_SESSION['role']!='N'): ?>
                                <th>Aksi</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        foreach ($anggotaData as $anggota): ?>
                        <tr>
                            <td class='tno'><?php echo $no ?></td>
                            <td><?php echo $anggota['id_custom'] ?></td>
                            <td><?php echo $anggota['nama']?></td>
                            <td><?php echo $anggota['no_wa']?></td>
                            <td><?php echo $anggota['email']?></td>
                            <td><?php echo $anggota['alamat']?></td>
                            <td><?php echo $anggota['aktif']=='Y' ? 'Aktif' : 'Tidak Aktif' ?></td>
                            <?php if ($_SESSION['role']!='N'): ?>
                                <td class='d-flex justify-content-center'>
                                    <a class='btn btn-warning text-white me-2 d-flex' type='button' href='/Dasbor/Anggota/Edit/?id=<?php echo $anggota['id_anggota']?>'>✏️ <p class='tbltable'>Edit</p></a>
                                    <?php if ($anggota['aktif'] == 'Y'): ?>
                                        <a class='btn btn-danger d-flex' type='button' onclick="return confirm('Non-Aktifkan anggota <?php echo $anggota['nama']?>')" href='/Dasbor/Anggota/NonAktif/?id=<?php echo $anggota['id_anggota']?>'>
                                            🚫 
                                            <p class='tbltable'>Non-aktif</p>
                                        </a>
                                    <?php else: ?>
                                        <a class='btn btn-success d-flex' type='button' onclick="return confirm('Aktifkan kembali anggota <?php echo $anggota['nama']?>')" href='/Dasbor/Anggota/Aktif/?id=<?php echo $anggota['id_anggota']?>'>
                                            ✅ 
                                            <p class='tbltable'>Aktifkan</p>
                                        </a>
                                    <?php endif; ?>
                                </td>
                            <?php endif; ?>
                        </tr>
                        <?php $no++; 
                        endforeach;?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
    <script src="/assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="/assets/js/script.js"></script>
</body>

</html>