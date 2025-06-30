<?php
require_once '../../databaseconfig.php';
require_once '../../sessionconfig.php';

// Mulai session
startSession();
$asal = 'Dasbor/Setup';
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

?>

<?php
// Ambil data profil koperasi
$profilQuery = "SELECT *
FROM view_profil_koperasi 
WHERE id_koperasi = ?";
$stmt = mysqli_prepare($conn, $profilQuery);
mysqli_stmt_bind_param($stmt, 'i', $_SESSION['id_koperasi']);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $_, $nama_koperasi, $alamat, $provinsi, $kota, $simpanan_pokok, $simpanan_wajib);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

//ambil data provinsi
$provinsiquery = "SELECT * FROM kota";
$stmt = mysqli_prepare($conn,$provinsiquery);
mysqli_stmt_execute($stmt);
$kota_result=mysqli_stmt_get_result($stmt);

//ambil data jasa pinjaman
$jasaQuery = "SELECT persen FROM jasa WHERE id_jasa=(SELECT MAX(id_jasa) FROM jasa WHERE id_koperasi = ?)";
$stmt = mysqli_prepare($conn, $jasaQuery);
mysqli_stmt_bind_param($stmt, 'i', $_SESSION['id_koperasi']);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $jasa_pinjaman);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

// ambil semua akun yang ada di koperasi
$akunQuery = "SELECT a.id_akun AS id_akun, username, role_admin FROM login l JOIN akun a ON l.id_akun=a.id_akun WHERE id_koperasi = ?";
$stmt = mysqli_prepare($conn, $akunQuery);
mysqli_stmt_bind_param($stmt, 'i', $_SESSION['id_koperasi']);
mysqli_stmt_execute($stmt);
$akun_result = mysqli_stmt_get_result($stmt);
?>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    if (isset($_POST['kop-simpan'])) {
        $nama_koperasi = $_POST['nama_koperasi'];
        $alamat = $_POST['alamat'];
        $kota = $_POST['kota'];
        $simpanan_pokok = $_POST['simpanan_pokok'];
        $simpanan_wajib = $_POST['simpanan_wajib'];
        $password=$_POST["password"];

        //cek pasword
        $queypasw="SELECT password FROM login WHERE id_akun = ?";
        $stmt = mysqli_prepare($conn, $queypasw);
        mysqli_stmt_bind_param($stmt, 'i', $_SESSION['id_akun']);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $passworddb);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);

        //id_kota
        $kotaquery = "SELECT id_kota FROM kota WHERE nama_kota = ?";
        $stmt = mysqli_prepare($conn, $kotaquery);
        mysqli_stmt_bind_param($stmt, 's', $kota);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $id_kota);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);

        if ($id_kota>0){
            if(password_verify($password, $passworddb)){
                //update data profil
                $updateQuery = "UPDATE koperasi 
                SET nama_koperasi = ?, alamat = ?, id_kota = ?, simpanan_pokok = ?, 
                simpanan_wajib = ? WHERE id_koperasi = ?";
                $stmt = mysqli_prepare($conn, $updateQuery);
                mysqli_stmt_bind_param($stmt, 'ssiiii', $nama_koperasi, $alamat, $id_kota, $simpanan_pokok, $simpanan_wajib, $_SESSION['id_koperasi']);
                if(mysqli_stmt_execute($stmt)){
                    $sukses = "Berhasil memperbarui Koperasi";
                    header("Refresh: 2; URL=/Dasbor/Setup/");
                } else {
                    $error = "Tidak dapat memperbarui data koperasi silahkan coba lagi";
                }
            mysqli_stmt_close($stmt);
            } else {
                $error = "Password yang Anda masukkan salah";
            }
        } else{
            $error="kota ".  $kota ." tidak ditemukan";
        }
    }   elseif (isset($_POST['jasa-simpan'])) {
        $jasa_pinjaman = $_POST['jasa-pinjaman'];
        $password_jasa = $_POST['password-jasa'];

        //cek pasword
        $queypasw="SELECT password FROM login WHERE id_akun = ?";
        $stmt = mysqli_prepare($conn, $queypasw);
        mysqli_stmt_bind_param($stmt, 'i', $_SESSION['id_akun']);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $passworddb);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);

        if(password_verify($password_jasa, $passworddb)){
            //update jasa pinjaman
            $updateJasaQuery = "INSERT INTO jasa (id_koperasi, persen)
            VALUES (?, ?)";
            $stmt = mysqli_prepare($conn, $updateJasaQuery);
            mysqli_stmt_bind_param($stmt, 'ii', $_SESSION['id_koperasi'], $jasa_pinjaman);
            if(mysqli_stmt_execute($stmt)){
                $jasasukses = "Berhasil memperbarui Jasa Pinjaman";
                header("Refresh: 2; URL=/Dasbor/Setup/");
            } else {
                $jasaerror = "Tidak dapat memperbarui Jasa Pinjaman silahkan coba lagi";
            }
            mysqli_stmt_close($stmt);
        } else {
            $jasaerror = "Password yang Anda masukkan salah";
        }
    } elseif (isset($_POST['new-simpan'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $konfirm_password = $_POST['konfirm-pasword'];
        $role_admin = isset($_POST['formCheck-1']) ? 'Y' : 'N';

        // Cek apakah password dan konfirmasi password cocok
        if ($password !== $konfirm_password) {
            $errornew = "Password dan Konfirmasi Password tidak cocok.";
        } else {
            // Enkripsi password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $query = "CALL sp_register_user(?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, 'sssi', $username, $hashedPassword, $role_admin, $_SESSION['id_koperasi']);
            if (!mysqli_stmt_execute($stmt)) {
                // Ambil pesan error dari database
                $errornew = mysqli_stmt_error($stmt);
            } else {
                // Proses sukses, misal redirect
                header("Refresh: 0; URL=/Dasbor/Setup/");
                exit();
            }
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
    <?php require_once '../../Dasbor/aside.php'; ?>
    <main>
        <section class="d-flex justify-content-between">
            <div class="w-auto">
                <h1 class="jdl w-auto">Setup Koperasi</h1>
                <div class="navatas">
                    <a class="linkatasfirst" href="/Dasbor/">Dasbor</a>
                    <a class="linkatas" href="/Dasbor/">
                        <span id="namakop">
                            <?php echo htmlspecialchars($nama_koperasi) ;?>
                        </span></a>
                    <p class="thissite">Setup Koperasi</p>
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
        <section class="text-center mt-5">
            <div class="container m-md-0">
                <div class="row-cols-1 row row-cols-md-2 gx-3 gy-4">
                    <div class="col">
                        <div class="card m-2">
                            <div class="card-header">
                                <h5>Profil Koperasi</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <p class="text-start col-7 col-md-4">Nama Koperasi</p>
                                    <p class="col-5 col-md-1 text-start">:</p>
                                    <p class="text-end text-md-start col-12 col-md-7" id="pk-namakop">
                                        <?php echo htmlspecialchars($nama_koperasi) ;?>
                                    </p>
                                </div>
                                <div class="row">
                                    <p class="text-start col-7 col-md-4">Provinsi</p>
                                    <p class="col-5 col-md-1 text-start">:</p>
                                    <p class="text-end text-md-start col-12 col-md-7" id="pk-provinsi">
                                        <?php echo htmlspecialchars($provinsi) ;?>
                                    </p>
                                </div>
                                <div class="row">
                                    <p class="text-start col-7 col-md-4">Kota</p>
                                    <p class="col-5 col-md-1 text-start">:</p>
                                    <p class="text-end text-md-start col-12 col-md-7" id="pk-kota">
                                        <?php echo htmlspecialchars($kota) ;?>
                                    </p>
                                </div>
                                <div class="row">
                                    <p class="text-start col-7 col-md-4">Alamat</p>
                                    <p class="col-5 col-md-1 text-start">:</p>
                                    <p class="text-end text-md-start col-12 col-md-7" id="pk-alamat-kop">
                                        <?php echo htmlspecialchars($alamat) ;?>
                                    </p>
                                </div>
                                <div class="row">
                                    <p class="text-start col-7 col-md-4">Simpanan Pokok</p>
                                    <p class="col-5 col-md-1 text-start">:</p>
                                    <p class="text-end text-md-start col-12 col-md-7" id="simpanan-wajib">
                                        Rp. 
                                        <?php echo number_format($simpanan_pokok, 2, ',', '.') ;?>
                                    </p>
                                </div>
                                <div class="row">
                                    <p class="text-start col-7 col-md-4">Simpanan Wajib</p>
                                    <p class="col-5 col-md-1 text-start">:</p>
                                    <p class="text-end text-md-start col-12 col-md-7" id="simpanan-wajib-1">
                                        Rp. 
                                        <?php echo number_format($simpanan_wajib, 2, ',', '.') ;?>
                                    </p>
                                </div>
                            </div>
                            <?php if ($_SESSION['role'] != 'N'): ?>
                                <div class="card-footer">
                                    <?php if (isset($sukses)): ?>
                                        <div class="alert alert-success"><?php echo htmlspecialchars($sukses)?></div>
                                    <?php elseif (isset($error)): ?>
                                        <div class="alert alert-warning"><?php echo htmlspecialchars($error)?></div>
                                    <?php endif; ?>
                                    <button class="btn btn-warning text-white" type="button" data-bs-toggle="modal" data-bs-target="#edit-pk">
                                        ✏️ Edit&nbsp;
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card m-2">
                            <div class="card-header">
                                <h5>Jasa Pinjaman</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <p class="text-start col-5">Jasa</p>
                                    <p class="col-1">:</p>
                                    <p class="text-start col-5" id="ik-namakop-4"><?php echo htmlspecialchars($jasa_pinjaman)?></p>
                                    <p class="col-1">%</p>
                                </div>
                            </div>
                            <?php if ($_SESSION['role']!='N'): ?>
                                <div class="card-footer">
                                    <?php if (isset($jasasukses)): ?>
                                        <div class="alert alert-success"><?php echo htmlspecialchars($jasasukses)?></div>
                                    <?php elseif (isset($jasaerror)): ?>
                                        <div class="alert alert-warning"><?php echo htmlspecialchars($jasaerror)?></div>
                                    <?php endif; ?>
                                    <button class="btn btn-warning text-white" type="button" data-bs-toggle="modal" data-bs-target="#edit-jp">
                                        ✏️ Edit&nbsp;
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container">
                <div class="card m-2">
                    <h5 class="card-header">Akses Akun</h5>
                    <?php if (isset($errornew)): ?>
                        <div class="alert alert-warning"><?php echo htmlspecialchars($errornew)?></div>
                    <?php endif; ?>
                    <div class="card-body">
                        <?php if ($_SESSION['role'] != 'N'): ?>
                        <div class="d-flex justify-content-end align-items-center header-action mb-1">
                            <a class="add-button" href="Tambah" data-bs-toggle="modal" data-bs-target="#plus-aa">➕ Akses</a>
                        </div>
                        <?php endif; ?>
                        <div class="table-responsive wadah-table">
                            <table class="table table-hover table-bordered">
                                <thead class="table">
                                    <tr>
                                        <th class="tno">No</th>
                                        <th>Username</th>
                                        <th>Peran</th>
                                        <?php if ($_SESSION['role'] != 'N'): ?>
                                            <th>Action</th>
                                        <?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $nomer=1; ?>
                                    <?php while ($row = mysqli_fetch_assoc($akun_result)): ?>
                                        <tr>
                                            <td class="tno"><?php echo $nomer?></td>
                                            <td><?php echo $row['username']?></td>
                                            <td><?php echo $row['role_admin']=='S' ? 'Super Admin' : ($row['role_admin'] == 'Y' ? 'Admin' : 'Pengawas')?></td>
                                            <?php if ($_SESSION['role'] != 'N') : ?>
                                                <?php if ($row['role_admin']!='S') : ?>
                                                <td class="d-flex justify-content-center">
                                                    <a class="btn btn-warning text-white me-2 d-flex" href="/Dasbor/Setup/Edit/?id=<?php echo htmlspecialchars($row['id_akun'])?>">
                                                        ✏️<p class='tbltable'>Edit</p>
                                                    </a>
                                                    <a class="btn btn-danger d-flex" href="/Dasbor/Setup/Hapus/?id=<?php echo htmlspecialchars($row['id_akun'])?>" onclick="return confirm('Apakah Anda yakin ingin menghapus akun ini?');">
                                                        🗑️<p class='tbltable'>Hapus</p>
                                                </a>
                                                </td>
                                                <?php else:?>
                                                <td></td>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                            <?php $nomer++; ?>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <?php if ($_SESSION['role'] != 'N'): ?>
    <div id="edit-pk" class="modal fade" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Profil Koperasi</h5>
                    <button class="btn btn-close" type="button" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST">
                        <div class="form-group">
                            <input class="form-control" type="text" id="nama" name="nama_koperasi" placeholder="Nama Koperasi" required value="<?php echo htmlspecialchars($nama_koperasi)?>">
                        </div>
                        <div class="form-group">
                            <input class="form-control" type="text" name="kota" placeholder="Kota" list="menu-kota" required value="<?php echo htmlspecialchars($kota)?>">
                            <datalist id="menu-kota">
                                <?php
                                if (isset($kota_result)):
                                while ($row = mysqli_fetch_assoc($kota_result)):
                                ?>
                                <option value="<?php echo htmlspecialchars($row['nama_kota'])?>"></option>
                                <?php endwhile; 
                                endif;?>
                            </datalist>
                        </div>
                        <div class="form-group">
                            <textarea class="form-control" id="alamat" name="alamat" placeholder="Alamat Koperasi" required><?php echo($alamat)?></textarea>
                        </div>
                        <div class="form-group">
                            <input class="form-control" type="number" id="simpanan-pokok" name="simpanan_pokok" placeholder="Simpanan Pokok" required value="<?php echo htmlspecialchars($simpanan_pokok)?>"></div>
                        <div class="form-group">
                            <input class="form-control" type="number" id="simpanan-wajib" name="simpanan_wajib" placeholder="Simpanan Wajib" required value="<?php echo htmlspecialchars($simpanan_wajib)?>"></div>
                        <div class="form-group">
                            <input class="form-control" type="password" name="password" placeholder="Password" required></div>
                        <div class="text-end form-group">
                            <button class="btn btn-primary btn-success" type="submit" name="kop-simpan">💾 Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div id="edit-jp" class="modal fade" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Jasa Pinjaman</h5>
                    <button class="btn btn-close" type="button" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST">
                        <div class="form-group">
                            <input class="form-control" type="number" id="jasa-pinjaman" name="jasa-pinjaman" placeholder="Persen Jasa Pinjaman %" required value="<?php echo htmlspecialchars($jasa_pinjaman)?>">
                        </div>
                        <div class="form-group">
                            <input class="form-control" type="password" id="password-jasa" name="password-jasa" placeholder="Password" required>
                        </div>
                        <div class="text-end form-group">
                            <button class="btn btn-primary btn-success" type="submit" name="jasa-simpan">💾 Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div id="plus-aa" class="modal fade" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Akses Akun</h5><button class="btn btn-close" type="button" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST">
                        <div class="form-group">
                            <input class="form-control" type="text" id="username" name="username" placeholder="username" required></div>
                        <div class="form-group">
                            <input class="form-control" type="password" id="pasword" name="password" placeholder="Password" required></div>
                        <div class="form-group">
                            <input class="form-control" type="password" id="konfirm-password" name="konfirm-pasword" placeholder="Konfirmasi Password" required></div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="formCheck-1" name="formCheck-1">
                            <label class="form-check-label" for="formCheck-1">Admin</label></div>
                        <div class="text-end form-group">
                            <button class="btn btn-primary btn-success" type="submit" name="new-simpan">💾 Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <script src="/assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="/assets/js/script.js"></script>
</body>

</html>