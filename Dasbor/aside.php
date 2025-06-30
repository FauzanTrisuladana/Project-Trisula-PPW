<?php
if (basename(__FILE__) == basename($_SERVER['PHP_SELF'])) {
    // Kalau file ini diakses langsung, redirect atau tampilkan error
    header('HTTP/1.0 403 Forbidden');
    exit('No direct access allowed.');

include_once 'sessionconfig.php';
session_start();

}?>
<header class="d-flex justify-content-between align-items-center sticky-top d-md-none">
    <button class="btn d-md-none m-1 bg-white" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar">
        ☰
    </button>
    <div class="dropdown">
        <a class="d-flex align-items-center profile" data-bs-toggle="dropdown">
        <i class="far fa-user-circle icon-profile namaakun-header"></i>
        <span id="namaakun" class="ms-2 me-2 namaakun-header">
            <?php echo htmlspecialchars($nama_depan) . " " . htmlspecialchars($nama_belakang)?>
        </span>
        </a>
        <div class="dropdown-menu">
            <a class="dropdown-item" href="/Dasbor/Profil/">Profil</a>
            <a class="dropdown-item" href="/Setings/">Ganti Password</a>
            <a class="dropdown-item" href="/Logout/">Keluar</a>
        </div>
    </div>
</header>
<aside class="d-none d-md-flex flex-column justify-content-between overflow-auto">
    <div>
        <a href="/Dasbor/">
            <img alt="logo" class="logobesar" src="/assets/img/Trisula%20logo%20besar.png">
        </a>
        <div class="v-navmenu mt-4">
            <a class="Menu" href="/Dasbor/">
                <i class="fa fa-home icon-navbar"></i>
                <p class="navpar">Dasbor</p>
            </a>
        </div>
        <div class="v-navmenu">
            <a class="Menu accordion-button d-flex align-items-center collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#MainMenu">
                <i class="fa fa-bars icon-navbar"></i>
                <p class="navpar">Main Menu</p>
            </a>
            <div class="accordion-body collapse p-1" id="MainMenu">
                <a class="Menu" href="/Dasbor/Anggota/">
                <i class="fa fa-user icon-navbar"></i>
                <p class="navpar">Anggota</p>
            </a>
            <?php if ($_SESSION['role']!='N'): ?>
                <a class="Menu accordion-button d-flex align-items-center collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#transaksiMenu">
                    <i class="fas fa-handshake icon-navbar"></i>
                    <p class="navpar">Transaksi</p>
                </a>
                <div class="accordion-body collapse p-1" id="transaksiMenu">
                    <a class="Menu" href="/Dasbor/Transaksi/Simpanan/">Simpanan</a>
                    <a class="Menu" href="/Dasbor/Transaksi/Pinjaman/">Pinjaman</a>
                    <a class="Menu" href="/Dasbor/Transaksi/Pelunasan/">Pelunasan</a>
                </div>
            <?php endif?>
            </div>
        </div>
        <div class="v-navmenu">
            <a class="Menu accordion-button d-flex align-items-center collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#setupMenu">
                <i class="fa fa-cog icon-navbar"></i>
                <p class="navpar">Setup</p>
            </a>
            <div class="accordion-body collapse p-1" id="setupMenu">
                <a class="Menu" href="/Dasbor/SetupKoperasi/">
                    <i class="fa fa-gear icon-navbar"></i>
                    <p class="navpar">Setup Koperasi</p>
                </a>
                <a class="Menu" href="/Dasbor/SetupAkunKeuangan/">
                    <i class="fa fa-gear icon-navbar"></i>
                    <p class="navpar">Setup Akun Keuangan</p>
                </a>
            </div>
        </div>
        <div class="v-navmenu">
            <a class="Menu accordion-button d-flex align-items-center collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#ringkasanMenu">
                <i class="fas fa-check icon-navbar"></i>
                <p class="navpar">Ringkasan</p>
            </a>
            <div class="accordion-body collapse p-1" id="ringkasanMenu">
                <a class="Menu" href="/Dasbor/Ringkasan/Simpanan/">
                    <i class="fas fa-hands icon-navbar"></i>
                    <p class="navpar">Simpanan</p>
                </a><a class="Menu" href="/Dasbor/Ringkasan/Pinjaman/">
                    <i class="fas fa-hands-helping icon-navbar"></i>
                    <p class="navpar">Pinjaman</p>
                </a>
            </div>
        </div>
        <div class="v-navmenu">
            <a class="Menu accordion-button d-flex align-items-center collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#laporanMenu">
                <i class="fa fa-list icon-navbar"></i>
                <p class="navpar">Laporan</p>
            </a>
            <div class="accordion-body collapse p-1" id="laporanMenu">
                <a class="Menu" href="/Dasbor/Laporan/Simpanan/">
                    <i class="fas fa-hands icon-navbar"></i>
                    <p class="navpar">Simpanan</p>
                </a>
                <a class="Menu" href="/Dasbor/Laporan/Pelunasan/">
                    <i class="fas fa-hands-helping icon-navbar"></i>
                    <p class="navpar">Pelunasan</p>
                </a>
            </div>
        </div>
    </div>
    <div class="mb-3">
        <p class="text-white mb-0" style="font-size: 10px;">Copyright © 2025 Trisula</p>
    </div>
</aside>

<div class="d-md-none offcanvas offcanvas-start" tabindex="-1" id="mobileSidebar" style="width: 180px; background: #262626;">
    <aside class="d-none d-md-flex flex-column justify-content-between overflow-auto">
        <div>
            <a href="/Dasbor/">
                <img alt="logo" class="logobesar" src="/assets/img/Trisula%20logo%20besar.png">
            </a>
            <div class="v-navmenu mt-4">
                <a class="Menu" href="/Dasbor/">
                    <i class="fa fa-home icon-navbar"></i>
                    <p class="navpar">Dasbor</p>
                </a>
            </div>
            <div class="v-navmenu">
                <a class="Menu accordion-button d-flex align-items-center collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#MainMenu">
                    <i class="fa fa-bars icon-navbar"></i>
                    <p class="navpar">Main Menu</p>
                </a>
                <div class="accordion-body collapse p-1" id="MainMenu">
                    <a class="Menu" href="/Dasbor/Anggota/">
                    <i class="fa fa-user icon-navbar"></i>
                    <p class="navpar">Anggota</p>
                </a>
                <?php if ($_SESSION['role']!='N'): ?>
                    <a class="Menu accordion-button d-flex align-items-center collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#transaksiMenu">
                        <i class="fas fa-handshake icon-navbar"></i>
                        <p class="navpar">Transaksi</p>
                    </a>
                    <div class="accordion-body collapse p-1" id="transaksiMenu">
                        <a class="Menu" href="/Dasbor/Transaksi/Simpanan/">Simpanan</a>
                        <a class="Menu" href="/Dasbor/Transaksi/Pinjaman/">Pinjaman</a>
                        <a class="Menu" href="/Dasbor/Transaksi/Pelunasan/">Pelunasan</a>
                    </div>
                <?php endif?>
                </div>
            </div>
            <div class="v-navmenu">
                <a class="Menu accordion-button d-flex align-items-center collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#setupMenu">
                    <i class="fa fa-cog icon-navbar"></i>
                    <p class="navpar">Setup</p>
                </a>
                <div class="accordion-body collapse p-1" id="setupMenu">
                    <a class="Menu" href="/Dasbor/SetupKoperasi/">
                        <i class="fa fa-gear icon-navbar"></i>
                        <p class="navpar">Setup Koperasi</p>
                    </a>
                    <a class="Menu" href="/Dasbor/SetupAkunKeuangan/">
                        <i class="fa fa-gear icon-navbar"></i>
                        <p class="navpar">Setup Akun Keuangan</p>
                    </a>
                </div>
            </div>
            <div class="v-navmenu">
                <a class="Menu accordion-button d-flex align-items-center collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#ringkasanMenu">
                    <i class="fas fa-check icon-navbar"></i>
                    <p class="navpar">Ringkasan</p>
                </a>
                <div class="accordion-body collapse p-1" id="ringkasanMenu">
                    <a class="Menu" href="/Dasbor/Ringkasan/Simpanan/">
                        <i class="fas fa-hands icon-navbar"></i>
                        <p class="navpar">Simpanan</p>
                    </a><a class="Menu" href="/Dasbor/Ringkasan/Pinjaman/">
                        <i class="fas fa-hands-helping icon-navbar"></i>
                        <p class="navpar">Pinjaman</p>
                    </a>
                </div>
            </div>
            <div class="v-navmenu">
                <a class="Menu accordion-button d-flex align-items-center collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#laporanMenu">
                    <i class="fa fa-list icon-navbar"></i>
                    <p class="navpar">Laporan</p>
                </a>
                <div class="accordion-body collapse p-1" id="laporanMenu">
                    <a class="Menu" href="/Dasbor/Laporan/Simpanan/">
                        <i class="fas fa-hands icon-navbar"></i>
                        <p class="navpar">Simpanan</p>
                    </a>
                    <a class="Menu" href="/Dasbor/Laporan/Pelunasan/">
                        <i class="fas fa-hands-helping icon-navbar"></i>
                        <p class="navpar">Pelunasan</p>
                    </a>
                </div>
            </div>
        </div>
        <div class="mb-3">
            <p class="text-white mb-0" style="font-size: 10px;">Copyright © 2025 Trisula</p>
        </div>
    </aside>
</div>