<?php
require_once '../../../databaseconfig.php';
require_once '../../../sessionconfig.php';

// Mulai session
startSession();
$asal = 'Dasbor/Setup/Edit';
if (!isLoggedIn()) {
    header('Location: /Login/?redirect=' . urlencode($asal));
    exit();
}
if (!isHasAccess()) {
    header('Location: /Dasbor/Setup/');
    exit();
}
?>


<?php
// ambil get
if (isset($_GET['id'])) {
    $id_akun = $_GET['id'];
} else {
    // Jika tidak ada id_akun, redirect atau tampilkan pesan error
    header('Location: /Dasbor/Setup/');
    exit();
}

// ambil data akun dari database
$query = "SELECT username, password, role_admin FROM akun a JOIN login l ON a.id_akun=l.id_akun WHERE a.id_akun = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 'i', $id_akun);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $username, $password, $role_admin);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

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

// Proses form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $passwordnew = $_POST['password'];
    $konfirm_password = $_POST['konfirm-pasword'];
    $role_admin = isset($_POST['role_admin']) ? 'Y' : 'N';
    $password_akun = $_POST['password-akun'];

    // Validasi password akun
    if (empty($password_akun) || !password_verify($password_akun, $password)) {
        $error = "Password akun tidak valid.";
    } else {
        // Hash password
        $hashedPassword = password_hash($passwordnew, PASSWORD_DEFAULT);

        // Validasi password
        if ($passwordnew !== $konfirm_password) {
            $error= "Password dan Konfirmasi Password tidak cocok.";
        } else {
            $query = "CALL sp_update_user(?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, 'isss', $id_akun, $username, $hashedPassword, $role_admin);
            if (!mysqli_stmt_execute($stmt)) {
                $error = mysqli_stmt_error($stmt);
            } else {
                header("Location: /Dasbor/Setup/");
                exit();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
                <h1 class="jdl w-auto">Setup Koperasi</h1>
                <div class="navatas">
                    <a class="linkatasfirst" href="/Dasbor/">Dasbor</a>
                    <a class="linkatas" href="/Dasbor/">
                        <span id="namakop">
                            <?php echo htmlspecialchars($nama_koperasi) ;?>
                        </span></a>
                    <a href="/Dasbor/Setup/" class="linkatas">Setup Koperasi</a>
                    <p class="thissite">Setup Koperasi</p>
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
                    <a class="dropdown-item" href="/Logout/">Keluar</a>
                </div>
            </div>
        </section>
        <section class="mt-5">
            <form method="POST">
                <div class="container-ang container">
                    <h3 class="text-center header-form p-2 rounded-4">Edit Akses Akun</h3>
                    <div class="form-group">
                        <input class="form-control" type="text" id="username" name="username" placeholder="username" value="<?php echo htmlspecialchars($username); ?>" required>
                    </div>
                    <div class="form-group">
                        <input class="form-control" type="password" id="pasword" name="password" placeholder="Password" required>
                    </div>
                    <div class="form-group">
                        <input class="form-control" type="password" id="konfirm-password" name="konfirm-pasword" placeholder="Konfirmasi Password"  required>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="formCheck-1" name="role_admin" <?php echo $role_admin=='Y' ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="formCheck-1">Admin</label>
                    </div>
                    <div class="form-group">
                        <input class="form-control" type="password" id="password-input" name="password-akun" placeholder="Password akun untuk Konfirmasi" required>
                    <div class="text-end form-group">
                        <button class="btn btn-primary btn-success mt-2" type="submit" name='simpan'>Konfirmasi</button>
                    </div>
                </div>
            </form>
        </section>
    </main>
</body>
</html>