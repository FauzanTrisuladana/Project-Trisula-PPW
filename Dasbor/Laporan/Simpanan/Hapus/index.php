<?php
// Include file koneksi database
include_once("../../../../databaseconfig.php");
require_once ('../../../../sessionconfig.php');

startSession();
$asal = 'Dasbor/Laporan/Simpanan';
if (!isLoggedIn()) {
    header('Location: /Login/?redirect=' . urlencode($asal));
    exit();
}
if (!isHasAccess()) {
    header('Location: /Dasbor/Laporan/Simpanan/');
    exit();
}

// Cek apakah ada ID yang dikirimkan
if(isset($_GET['id'])) {
    $id = $_GET['id'];

    //ambil angsuran dan id_pinjaman dari yg akan dihapus
    $query = "SELECT angsuran_ke, id_pinjaman, id_koperasi FROM simpanan WHERE id_simpanan = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $angsuran_ke, $id_pinjaman, $id_koperasi);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    // Validasi apakah pelunasan milik koperasi ini
    if ($id_koperasi === $_SESSION['id_koperasi']) {
        // Query untuk menghapus data
        $stmt = mysqli_prepare($conn, "DELETE FROM simpanan WHERE id_simpanan= ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    // Redirect ke halaman utama
    header("Location: /Dasbor/Laporan/Simpanan/");
    exit();
} else {
    // Jika tidak ada ID yang dikirimkan, kembali ke halaman utama
    header("Location: /Dasbor/Laporan/Simpanan/");
    exit();
}
?>