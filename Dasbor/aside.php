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
    <img alt="logo" class="logoheader m-1" src="/assets/img/Trisula%20logo%20kecil.png">
</header>
<aside class="d-none d-md-flex flex-column justify-content-between overflow-auto">
    <div>
        <a href="/Dasbor/">
            <img alt="logo" class="logobesar" src="/assets/img/Trisula%20logo%20besar.png">
        </a>
        <hr style="height: 2px;border: none;background: white;">
        <div class="v-navmenu">
            <p class="Navmenus">Main Menu</p>
            <a class="Menu" href="/Dasbor/">
                <i class="fa fa-home icon-navbar"></i>
                <p class="navpar">Dasbor</p>
            </a>
            <a class="Menu" href="/Dasbor/Anggota/">
                <i class="fa fa-user icon-navbar"></i>
                <p class="navpar">Anggota</p>
            </a>
            <?php if ($_SESSION['role']!='N'): ?>
                <a class="Menu accordion-button d-flex align-items-center collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#transaksiMenu">
                    <i class="fas fa-handshake icon-navbar"></i>
                    <p class="navpar">Transaksi</p>
                </a>
                <div class="accordion-body collapse" id="transaksiMenu">
                    <a class="Menu" href="/Dasbor/Transaksi/Simpanan/">Simpanan</a>
                    <a class="Menu" href="/Dasbor/Transaksi/Pinjaman/">Pinjaman</a>
                    <a class="Menu" href="/Dasbor/Transaksi/Pelunasan/">Pelunasan</a>
                </div>
            <?php endif?>
        </div>
        <hr style="height: 2px;border: none;background: white;">
        <div class="v-navmenu">
            <p class="Navmenus">Setup</p>
            <a class="Menu" href="/Dasbor/SetupKoperasi/"><i class="fa fa-gear icon-navbar"></i>
                <p class="navpar">Setup Koperasi</p>
            </a>
            <a class="Menu" href="/Dasbor/SetupAkunKeuangan/"><i class="fa fa-gear icon-navbar"></i>
                <p class="navpar">Setup Akun Keuangan</p>
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

<div class="d-md-none offcanvas offcanvas-start" tabindex="-1" id="mobileSidebar" style="width: 180px; background: #262626;">
    <aside class="d-flex flex-column justify-content-between overflow-auto">
        <div>
            <a href="/Dasbor/">
                <img alt="logo" class="logobesar" src="/assets/img/Trisula%20logo%20besar.png">
            </a>
            <hr style="height: 2px;border: none;background: white;">
            <div class="v-navmenu">
                <p class="Navmenus">Main Menu</p>
                <a class="Menu" href="/Dasbor/">
                    <i class="fa fa-home icon-navbar"></i>
                    <p class="navpar">Dasbor</p>
                </a>
                <a class="Menu" href="/Dasbor/Anggota/">
                    <i class="fa fa-user icon-navbar"></i>
                    <p class="navpar">Anggota</p>
                </a>
                <?php if ($_SESSION['role']!='N'): ?>
                    <a class="Menu accordion-button d-flex align-items-center collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#transaksiMenu">
                        <i class="fas fa-handshake icon-navbar"></i>
                        <p class="navpar">Transaksi</p>
                    </a>
                    <div class="accordion-body collapse" id="transaksiMenu">
                        <a class="Menu" href="/Dasbor/Transaksi/Simpanan/">Simpanan</a>
                        <a class="Menu" href="/Dasbor/Transaksi/Pinjaman/">Pinjaman</a>
                        <a class="Menu" href="/Dasbor/Transaksi/Pelunasan/">Pelunasan</a>
                    </div>
                <?php endif?>
            </div>
            <hr style="height: 2px;border: none;background: white;">
            <div class="v-navmenu">
                <p class="Navmenus">Setup</p>
                <a class="Menu" href="/Dasbor/SetupKoperasi/"><i class="fa fa-gear icon-navbar"></i>
                    <p class="navpar">Setup Koperasi</p>
                </a>
                <a class="Menu" href="/Dasbor/SetupAkunKeuangan/"><i class="fa fa-gear icon-navbar"></i>
                    <p class="navpar">Setup Akun Keuangan</p>
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
</div>