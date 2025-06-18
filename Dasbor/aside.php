<?php
if (basename(__FILE__) == basename($_SERVER['PHP_SELF'])) {
    // Kalau file ini diakses langsung, redirect atau tampilkan error
    header('HTTP/1.0 403 Forbidden');
    exit('No direct access allowed.');

include_once 'sessionconfig.php';
session_start();

}?>
<aside class="d-flex flex-column justify-content-between">
    <div>
        <a href="/Dasbor/">
        <img alt="logo" class="logobesar" src="/assets/img/Trisula%20logo%20besar.png">
        <img alt="logo" class="logokecil" src="/assets/img/Trisula%20Logo%20Kecil.png"></a>
        <hr style="height: 2px;border: none;background: white;">
        <div class="v-navmenu">
            <p class="Navmenus">Main Menu</p>
            <a class="Menu" href="/Dasbor/">
                <i class="fa fa-home icon-navbar"></i>
                <p class="navpar">Dasbor</p>
            </a><a class="Menu" href="/Dasbor/Anggota/"><i class="fa fa-user icon-navbar"></i>
                <p class="navpar">Anggota</p>
            </a>
            <?php if ($_SESSION['role']!='N'): ?>
            <div class="dropdown"><a class="Menu dropdown-toggle" data-bs-toggle="dropdown"><i class="fas fa-handshake icon-navbar"></i>
                    <p class="navpar">Transaksi</p>
                </a>
                <div class="dropdown-menu position-absolute">
                    <a class="dropdown-item" href="/Dasbor/Transaksi/Simpanan/">Simpanan</a>
                    <a class="dropdown-item" href="/Dasbor/Transaksi/Pinjaman/">Pinjaman</a>
                    <a class="dropdown-item" href="/Dasbor/Transaksi/Pelunasan/">Pelunasan</a></div>
            </div>
            <?php endif?>
        </div>
        <hr style="height: 2px;border: none;background: white;">
        <div class="v-navmenu">
            <p class="Navmenus">Setup</p>
            <a class="Menu" href="/Dasbor/Setup/"><i class="fa fa-gear icon-navbar"></i>
                <p class="navpar">Setup Koperasi</p>
            </a>
        </div>
        <hr style="height: 2px;border: none;background: white;margin-top: 5px;margin-bottom: 5px;">
        <div class="v-navmenu">
            <p class="Navmenus">Ringkasan</p>
            <a class="Menu" href="/Dasbor/Ringkasan/Simpanan/"><i class="fas fa-hands icon-navbar"></i>
                <p class="navpar">Simpanan</p>
            </a><a class="Menu" href="/Dasbor/Ringkasan/Pinjaman/"><i class="fas fa-hands-helping icon-navbar"></i>
                <p class="navpar">Pinjaman</p>
            </a>
        </div>
        <hr style="height: 2px;border: none;background: white;margin-top: 5px;margin-bottom: 5px;">
        <div class="v-navmenu">
            <p class="Navmenus">Laporan</p>
            <a class="Menu" href="/Dasbor/Laporan/Simpanan/"><i class="fas fa-hands icon-navbar"></i>
                <p class="navpar">Simpanan</p>
            </a><a class="Menu" href="/Dasbor/Laporan/Pelunasan/"><i class="fas fa-hands-helping icon-navbar"></i>
                <p class="navpar">Pelunasan</p>
            </a>
        </div>
    </div>
    <div class="mb-3">
        <p class="text-white mb-0" style="font-size: 10px;">Copyright © 2025 Trisula</p>
    </div>
</aside>