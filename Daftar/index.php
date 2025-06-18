<?php
require_once '../databaseconfig.php';
?>

<?php
$provinsis_sql="SELECT * FROM provinsi";
$provinsis_result = mysqli_query($conn, $provinsis_sql);
?>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['kota-show']) && !empty($_POST['Provinsi'])) {
        $provinsi = $_POST['Provinsi'] ?? '';
        $kota_sql = "SELECT * FROM kota k JOIN provinsi p on p.id_provinsi=k.id_provinsi WHERE nama_provinsi = '$provinsi'";
        $kota_result = mysqli_query($conn, $kota_sql);
    } elseif (isset($_POST['submit'])) {
        $nama = $_POST['nama'] ?? '';
        $provinsi = $_POST['Provinsi'] ?? '';
        $kota = $_POST['Kota'] ?? '';
        $alamat = $_POST['alamat'] ?? '';
        $simpanan_pokok = $_POST['simpanan-pokok'] ?? 0;
        $simpanan_wajib = $_POST['simpanan-wajib'] ?? 0;
        $email = $_POST['email'] ?? '';
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        // Cek apakah username sudah ada
        $check_username_sql = "SELECT * FROM login WHERE username='$username'";
        $check_username_result = mysqli_query($conn, $check_username_sql);
        if (mysqli_num_rows($check_username_result) > 0) {
            $error = 'Username sudah digunakan. Silakan pilih yang lain.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Email tidak valid';
        } elseif ($simpanan_pokok < 0 || $simpanan_wajib < 0) {
            $error = 'Simpanan Pokok dan Simpanan Wajib tidak boleh negatif.';
        } elseif (!preg_match('/^[a-zA-Z0-9\s]+$/', $nama)) {
            $error = 'Nama Koperasi hanya boleh mengandung huruf, angka, dan spasi.';
        } elseif (!preg_match('/^[a-zA-Z\s]+$/', $provinsi)) {
            $error = 'Provinsi hanya boleh mengandung huruf dan spasi.';
        } elseif (!preg_match('/^[a-zA-Z\s]+$/', $kota)) {
            $error = 'Kota hanya boleh mengandung huruf dan spasi.';
        } elseif (!filter_var($alamat, FILTER_SANITIZE_STRING)) {
            $error = 'Alamat tidak valid.';
        } elseif (strlen($username) < 3 || strlen($username) > 20) {
            $error = 'Username harus antara 3 hingga 20 karakter.';
        } elseif (strlen($password) < 6) {
            $error = 'Password harus minimal 6 karakter.';
        } elseif (!preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) {
            $error = 'Password harus mengandung huruf dan angka.';
        } elseif (!preg_match('/^[a-zA-Z0-9\s]+$/', $nama)) {
            $error = 'Nama Koperasi hanya boleh mengandung huruf, angka, dan spasi.';
        } elseif ($password !== $confirm_password) {
            $error = 'Password dan Konfirmasi Password tidak cocok.';
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert data into koperasi table
            $call = "CALL sp_register_koperasi(?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $call);

            mysqli_stmt_bind_param(
                $stmt,
                'sssiisss',
                $nama,          
                $alamat,
                $kota,
                $simpanan_pokok,
                $simpanan_wajib,
                $username,
                $hashed_password,
                $email
            );
            if (mysqli_stmt_execute($stmt)) {
                $success = 'Pendaftaran berhasil. Silakan masuk ke akun Anda.';
            } else {
                $error = 'Terjadi kesalahan saat mendaftar. Silakan coba lagi.';
            }
            mysqli_stmt_close($stmt);
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
    <link rel="stylesheet" href="/assets/css/styles.css">
    <link rel="icon" href="/assets/img/Trisula.png">
</head>

<body class="bodylogin">
    <section class="wadah-form-login-signup m-5">
        <div class="text-center rounded-3 p-5">
            <div class="mb-3 rounded-2 p-2" style="background: #262626;"><img alt="logo" class="logoheader" src="/assets/img/Trisula%20logo%20besar.png"></div>
            <?php if (isset($error)): ?>
            <div class="alert alert-warning">
                <p><?php echo htmlspecialchars($err); ?></p>
            </div>
            <?php endif; ?>
                <form method="POST" class="row-cols-1 row row-cols-md-2" id="form-daftar">
                    <div class="col">
                        <div class="card">
                            <div class="card-body">
                                <div class="form-group">
                                    <input class="form-control" type="text" id="nama" name="nama" placeholder="Nama Koperasi" value="<?php echo isset($_POST['nama']) ? htmlspecialchars($_POST['nama']) : ''; ?>" required>
                                </div>
                                <div class="form-group input-group">
                                    <input class="form-control" type="text" id="provinsi" name="Provinsi" placeholder="Provinsi" list="menu-prov" value="<?php echo isset($_POST['Provinsi']) ? htmlspecialchars($_POST['Provinsi']) : ''; ?>" required>
                                    <datalist id="menu-prov">
                                        <?php 
                                        while ($row = mysqli_fetch_assoc($provinsis_result)):
                                        ?>
                                        <option value="<?php echo htmlspecialchars($row['nama_provinsi'])?>"></option>
                                        <?php endwhile; ?>
                                    </datalist>
                                    <button class="btn button-eye text-white" name="kota-show" onclick="nonWajib()">Lihat Kota</button>
                                </div>
                                <div class="form-group">
                                    <input class="form-control" type="text" id="kota" name="Kota" placeholder="Kota" list="menu-kota" value="<?php echo isset($_POST['Kota']) ? htmlspecialchars($_POST['Kota']) : ''; ?>" required>
                                    <datalist id="menu-kota">
                                        <?php
                                        if (isset($kota_result)):
                                        while ($row = mysqli_fetch_assoc($kota_result)):
                                        ?>
                                        <option value="<?php echo htmlspecialchars($row['nama_kota'])?>"></option>
                                        <?php endwhile; 
                                        endif;
                                        ?>
                                    </datalist>
                                </div>
                                <div class="form-group">
                                    <textarea class="form-control" id="alamat" name="alamat" placeholder="Alamat Koperasi" required><?php echo isset($_POST['alamat']) ? htmlspecialchars($_POST['alamat']) : ''; ?></textarea>
                                </div>
                                <div class="form-group">
                                    <input class="form-control" type="number" id="simpanan-pokok" name="simpanan-pokok" placeholder="Simpanan Pokok" value="<?php echo isset($_POST['simpanan-pokok']) ? htmlspecialchars($_POST['simpanan-pokok']) : ''; ?>" required>
                                </div>
                                <div class="form-group">
                                    <input class="form-control" type="number" id="simpanan-wajib" name="simpanan-wajib" placeholder="Simpanan Wajib" value="<?php echo isset($_POST['simpanan-wajib']) ? htmlspecialchars($_POST['simpanan-wajib']) : ''; ?>" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card mt-3 mt-md-0">
                            <div class="card-body">
                                <div class="input-group">
                                    <input class="form-control mb-2" type="email" placeholder="Email" name="email" id="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                                </div>
                                <div class="input-group">
                                    <input class="form-control mb-2" type="text" placeholder="Username" name="username" id="username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
                                </div>
                                <div class="input-group">
                                    <input class="form-control mb-2" type="password" id="password" placeholder="Password" name="password" required value="<?php echo isset($_POST['password']) ? htmlspecialchars($_POST['password']) : ''; ?>">
                                </div>
                                <div class="input-group">
                                    <input class="form-control mb-2" type="password" id="konpassword" placeholder="Konfirmasi Password" name="confirm_password" required value="<?php echo isset($_POST['confirm_password']) ? htmlspecialchars($_POST['confirm_password']) : ''; ?>">
                                </div>
                                <div>
                                    <button class="btn btn-login btn-success" type="submit" name="submit" onclick="wajib()">Daftar</button>
                                </div>
                            </div>
                        </div>
                        <div class="mt-2">
                            <p>Sudah punya akun? <a href="/Login/<?php echo !empty($_GET['redirect']) ? '?redirect=' . urlencode($_GET['redirect']) : '';?>">klik di sini</a></p>
                        </div>
                        <?php if (isset($success)): ?>
                            <div class="alert alert-success"><?php echo $success; ?>
                                <a href="/Login/<?php echo !empty($_GET['redirect']) ? '?redirect=' . urlencode($_GET['redirect']) : '';?>">Klik di sini untuk masuk</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </section>
    <script src="/assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="/assets/js/script.js"></script>
    <script src="/Daftar/form.js"></script>
</body>

</html>