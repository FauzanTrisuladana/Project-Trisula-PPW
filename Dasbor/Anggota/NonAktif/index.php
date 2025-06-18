<?php
// Include file koneksi database
include_once("../../../databaseconfig.php");
// Include file session config
include_once("../../../sessionconfig.php");
// Mulai session
startSession();
$asal = 'Dasbor/Anggota';
if (!isLoggedIn()) {
    header('Location: /Login/?redirect=' . urlencode($asal));
    exit();
}
if (!isHasAccess()) {
    header('Location: /Dasbor/Anggota/');
    exit();
}

// Cek apakah ada ID yang dikirimkan
if(isset($_GET['id'])) {
    $id = $_GET['id'];

    // validasi id untuk apakah boleh dinonaktifkan
    $stmt = mysqli_prepare($conn, "SELECT id_koperasi FROM anggota WHERE id_anggota = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $id_koperasi);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    if ($id_koperasi === $_SESSION['id_koperasi']) {
        // Query untuk menghapus data
        $query = "UPDATE anggota SET aktif=? WHERE id_anggota = ?";
        $stmt = mysqli_prepare($conn, $query);
        $aktif = 'N'; // Nonaktifkan anggota
        mysqli_stmt_bind_param($stmt, 'si', $aktif, $id);
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    } 
    // Redirect ke halaman utama
    header("Location: /Dasbor/Anggota/");
    exit();
} else {
    // Jika tidak ada ID yang dikirimkan, kembali ke halaman utama
    header("Location: /Dasbor/Anggota/");
    exit();
}
?>