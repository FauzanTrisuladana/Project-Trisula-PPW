
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
    <?php
    require_once '../databaseconfig.php';
    require_once '../sessionconfig.php';
    // Mulai session
    startSession(); 
    // Cek apakah pengguna sudah login
    // Jika sudah login, redirect ke halaman yang ditentukan atau ke Dasbor
    $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'Dasbor';
    if (isLoggedIn()) {
        header("Location: /$redirect/");
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if(isset($_POST['masuk'])) {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            $ingat = isset($_POST['ingat']) ? true : false;

            $query = "SELECT a.id_akun, id_koperasi, role_admin, password 
            FROM login l JOIN akun a ON l.id_akun = a.id_akun
            WHERE username = ? OR email = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, 'ss', $username, $username);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $id_akun, $id_koperasi, $role, $hashed_password);
            if (mysqli_stmt_fetch($stmt)) {
                // Verifikasi password
                if (password_verify($password, $hashed_password)) {
                    // Set session variables
                    $_SESSION['id_akun'] = $id_akun;
                    $_SESSION['id_koperasi'] = $id_koperasi;
                    $_SESSION['role'] = $role;

                    startSession($ingat);

                    // Redirect ke halaman yang ditentukan atau ke Dasbor
                    header("Location: /$redirect/");
                    exit();
                } else {
                    $error = "Username atau password salah.";
                }
            } else {
                $error = "Username atau password salah.";
            }
            mysqli_stmt_close($stmt);
        }

    }
    ?>
    <div class="text-center wadah-form-login-signup rounded-3 p-5">
        <div class="mb-3 rounded-2 p-2" style="background: #262626;">
            <img alt="logo" class="logoheader" src="/assets/img/Trisula%20logo%20besar.png">
        </div>
        <?php if (isset($error)): ?>
            <div class="alert alert-warning"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="input-group">
                <input class="form-control mb-2" type="text" placeholder="Username Atau email" name="username" required value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
            </div>
            <div class="input-group">
                <input class="form-control mb-2" type="password" id="password" placeholder="Password" name="password" required value="<?php echo isset($_POST['password']) ? htmlspecialchars($_POST['password']) : ''; ?>">
                <button class="btn button-eye mb-2" type="button" onclick="seek()">
                    <i class="far fa-eye"></i>
                </button>
            </div>
            <div class="d-flex justify-content-end mb-2">
                <input type="checkbox" id="ingat" class="me-1" placeholder="Password" name="ingat" value="yes">
                <label style="color: #595d6e;" for="ingat">Ingat Saya</label>
            </div>
            <div>
                <button class="btn btn-primary btn-login mb-2" type="submit" name="masuk">Masuk</button>
                <p class="mb-3 mt-1" style="color: #595d6e;">Atau</p>
                <a class="btn btn-login btn-secondary" type="button" href="/Daftar/<?php echo !empty($_GET['redirect']) ? '?redirect=' . urlencode($_GET['redirect']) : ''; ?>">Daftar</a>
        </form>
        </div>
    </div>
    <script src="/assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="/assets/js/script.js"></script>
</body>

</html>