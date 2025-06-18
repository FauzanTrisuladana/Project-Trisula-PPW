<?php
include_once 'databaseconfig.php';

// Mulai session
function startSession($remember = false) {
    if (session_status() === PHP_SESSION_NONE) {
        if ($remember) {
            $lifetime = 60 * 60 * 24 * 30; // 30 hari
        } else {
            $lifetime = 60 * 60 * 24;      // 1 hari
        }

        // Atur session cookie dan server lifetime sebelum start session
        session_set_cookie_params([
                'lifetime' => $lifetime,
                'path' => '/',
                'secure' => true,
                'httponly' => true,
                'samesite' => 'Lax'
            ]);
        ini_set('session.gc_maxlifetime', $lifetime);

        session_start();
        session_regenerate_id(true);
    }
}

// zona
date_default_timezone_set('Asia/Jakarta');

// Fungsi untuk cek login
function isLoggedIn() {
    global $conn;
    // cek di database
    if (isset($_SESSION['id_akun'])) {
        $query= "SELECT * FROM akun WHERE id_akun = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 'i', $_SESSION['id_akun']);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if (mysqli_num_rows($result) > 0) {
            return true;
        } else {
            header("Location: /Logout/");
        }
    } else {
        return false;
    }
}
function isHasAccess() {
    global $conn;
    if (isset($_SESSION['role'])) {
        if ($_SESSION['role'] !== 'N') {
            return true;
        }
    return false;
    }
}
?>