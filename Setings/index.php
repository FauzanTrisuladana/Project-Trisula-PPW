<?php
    require_once '../databaseconfig.php';
    require_once '../sessionconfig.php';
    // Mulai session
    startSession(); 
    // Cek apakah pengguna sudah login
    $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'Dasbor';
    if (!isLoggedIn()) {
        // Jika belum login, arahkan ke halaman login
        header("Location: /Login/?redirect=" . urlencode($redirect));
        exit();
    }
?>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $password = $_POST['password'];
    $newpass = $_POST['newpass'];
    $connewpass = $_POST['connewpass'];

    //validasi password lama
    $query = "SELECT password FROM login WHERE id_akun = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $_SESSION['id_akun']);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $hashedPassword);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
    if (!password_verify($password, $hashedPassword)) {
        $error = "Password lama salah!";
    } elseif (password_verify($newpass, $hashedPassword)) {
        $error = "Password baru tidak boleh sama dengan password lama!";
    } elseif (strlen($newpass) < 6) {
        $error = 'Password harus minimal 6 karakter.';
    } elseif (!preg_match('/[A-Za-z]/', $newpass) || !preg_match('/[0-9]/', $password)) {
        $error = 'Password harus mengandung huruf dan angka.';
    } elseif ($newpass!==$connewpass) {
        $error = "Konfirmasi password baru tidak cocok!";
    } else {
        // Jika semua validasi berhasil, lanjutkan ke proses perubahan password
        $sukses = "Password berhasil diubah!";
        // Simpan password baru ke database
        $hashedNewPassword = password_hash($newpass, PASSWORD_DEFAULT);
        $updateQuery = "UPDATE login SET password = ? WHERE id_akun = ?";
        $updateStmt = mysqli_prepare($conn, $updateQuery);
        mysqli_stmt_bind_param($updateStmt, 'si', $hashedNewPassword, $_SESSION['id_akun']);
        mysqli_stmt_execute($updateStmt);
        mysqli_stmt_close($updateStmt);
        $sukses = "Password berhasil diubah!";
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
    <link rel="stylesheet" href="/assets/css/styles.css">
    <link rel="icon" href="/assets/img/Trisula.png">
</head>

<body class="d-flex justify-content-center align-items-center bodylogin">
    <div class="text-center wadah-form-login-signup rounded-3 p-5">
        <div class="mb-3 rounded-2 p-2" style="background: #262626;">
            <img alt="logo" class="logoheader" src="/assets/img/Trisula%20logo%20besar.png">
        </div>
        <?php if (!empty($error)): ?>
            <div class="alert alert-warning"><?php echo $error;?></div>
        <?php endif;?>
        <form method="post">
            <div class="input-group">
                <input class="form-control mb-2" type="password" id="password" name="password" placeholder="Password Lama" required value="<?php echo isset($password) ? htmlspecialchars($password) : ''; ?>">
                <button class="btn button-eye mb-2" type="button" onclick="seekpass()">
                    <i class="far fa-eye"></i>
                </button>
            </div>
            <div class="input-group">
                <input class="form-control mb-2" type="password" id="newpass" name="newpass" placeholder="Password Baru" required value="<?php echo isset($newpass) ? htmlspecialchars($newpass) : ''; ?>">
                <button class="btn button-eye mb-2" type="button" onclick="seeknewpass()">
                    <i class="far fa-eye"></i>
                </button>
            </div>
            <div class="input-group">
                <input class="form-control mb-2" type="password" id="connewpass" name="connewpass" placeholder="Konfirmasi Password Baru" required value="<?php echo isset($connewpass) ? htmlspecialchars($connewpass) : ''; ?>">
                <button class="btn button-eye mb-2" type="button" onclick="seekconnewpass()">
                    <i class="far fa-eye"></i>
                </button>
            </div>
            <div>
                <button class="btn btn-primary btn-login mb-2" type="submit">Konfirmasi</button>
            </div>
        </form>
        <?php if (!empty($sukses)): ?>
            <div class="alert alert-success">
                <?php echo $sukses; ?>
                <br>
                <a href="<?php echo '/' . urlencode($_GET['redirect']) . '/'; ?>">Kembali</a>
            </div>
        <?php endif; ?>
    </div>
    <script src="/assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="/assets/js/script.js"></script>
    <script src="/Setings/script.js"></script>
</body>
</html>