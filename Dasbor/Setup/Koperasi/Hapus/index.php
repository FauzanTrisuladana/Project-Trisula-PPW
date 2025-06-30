<?php
// Include file koneksi database
include_once("../../../databaseconfig.php");
require_once ('../../../sessionconfig.php');

startSession();
$asal = 'Dasbor/Setup/';
if (!isLoggedIn()) {
    header('Location: /Login/?redirect=' . urlencode($asal));
    exit();
}
if (!isHasAccess()) {
    header('Location: /Dasbor/Setup/');
    exit();
}

// Cek apakah ada ID yang dikirimkan
if(isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // validasi id apakah boleh dihapus
    $stmt = mysqli_prepare($conn, "SELECT role_akun FROM akun WHERE id_akun = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $role);
    mysqli_stmt_fetch($stmt);
    
    if ($role !== 'S') {
        // Query untuk menghapus data
        $stmt = mysqli_prepare($conn, "DELETE FROM login WHERE id_akun= ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        $stmt = mysqli_prepare($conn, "DELETE FROM akun WHERE id_akun= ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    } 
    // Redirect ke halaman utama
    header("Location: /Dasbor/Setup/");
    exit();
} else {
    // Jika tidak ada ID yang dikirimkan, kembali ke halaman utama
    header("Location: /Dasbor/Setup/");
    exit();
}
?>