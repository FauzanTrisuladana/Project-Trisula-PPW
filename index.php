<?php
require_once 'databaseconfig.php';

//ambil data banyak akun
$query = "SELECT COUNT(*) AS total FROM akun";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $totalAkun);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);
//ambil data banyak anggota
$query = "SELECT COUNT(*) AS total FROM anggota";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $totalAnggota);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);
//ambil data banyak transaksi= banyak pinjaman, banyak simpanan, banyak pelunasan di total
$query = "SELECT 
(SELECT COUNT(*) AS total FROM pinjaman) 
+ (SELECT COUNT(*) AS total FROM simpanan)
+ (SELECT COUNT(*) AS total FROM pelunasan) AS totaltransaksi";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $totalTransaksi);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);
//ambil data banyak koperasi
$query = "SELECT COUNT(*) AS total FROM koperasi";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $totalKoperasi);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);
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
    <link rel="stylesheet" href="/assets/css/accordion-faq-list.css">
    <link rel="stylesheet" href="/assets/css/Features-Large-Icons-icons.css">
    <link rel="stylesheet" href="/assets/css/Footer-Dark-Multi-Column-icons.css">
    <link rel="stylesheet" href="/assets/css/Hero-Carousel-images.css">
    <link rel="stylesheet" href="/assets/css/Simple-Slider-swiper-bundle.min.css">
    <link rel="stylesheet" href="/assets/css/Simple-Slider.css">
    <link rel="stylesheet" href="/assets/css/styles.css">
    <link rel="icon" href="/assets/img/Trisula.png">
</head>

<body>
    <header class="d-flex justify-content-between align-items-center sticky-top">
        <a class="transform-translate" href="#">
            <img alt="logo" class="logoheader" src="/assets/img/Trisula%20logo%20besar.png">
        </a>
        <nav class="d-flex">
            <a class="land-button transform-translate" href="#">Tentang</a>
            <a class="land-button transform-translate" href="Login/">Masuk</a>
            <a class="land-button transform-translate" href="Daftar/">Daftar</a>
        </nav>
    </header>
    <section>
        <div class="container py-4 py-xl-5">
            <div class="row gy-4 gy-md-0">
                <div class="col-md-6">
                    <div class="p-xl-5 m-xl-5"><img class="rounded img-fluid w-100 fit-cover bluring" alt="Gambar" style="min-height: 300px;" src="/assets/img/Hero.png"></div>
                </div>
                <div class="col-md-6 d-md-flex align-items-md-center">
                    <div style="max-width: 350px;">
                        <h2 class="text-uppercase fw-bold">Manajemen Akuntansi koperasi simpan pinjam</h2>
                        <p class="my-3">kelola transaksi, anggota, simpanan dan pinjaman dalam satu aplikasi</p><a class="btn btn-primary btn-lg me-2 transform-translate btn-depan" role="button" href="https://wa.me/6285712216860"><i class="fab fa-whatsapp me-2"></i>Chat WhatsApp</a><a class="btn btn-outline-primary btn-lg transform-translate btn-depan" role="button" href="Daftar/">Daftar</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section>
        <div class="container py-4 py-xl-5">
            <div class="row gy-4 row-cols-2 row-cols-md-4">
                <div class="col">
                    <div class="text-center d-flex flex-column justify-content-center align-items-center py-3">
                        <div class="bs-icon-xl bs-icon-circle bs-icon-primary d-flex flex-shrink-0 justify-content-center align-items-center d-inline-block mb-2 bs-icon lg"><i class="fas fa-user-check"></i></div>
                        <div class="px-3">
                            <h2 class="fw-bold mb-0"><?php echo htmlspecialchars($totalAnggota) ;?>+</h2>
                            <p class="mb-0">Anggota Koperasi Terdaftar</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="text-center d-flex flex-column justify-content-center align-items-center py-3">
                        <div class="bs-icon-xl bs-icon-circle bs-icon-primary d-flex flex-shrink-0 justify-content-center align-items-center d-inline-block mb-2 bs-icon lg"><i class="fas fa-users"></i></div>
                        <div class="px-3">
                            <h2 class="fw-bold mb-0"><?php echo htmlspecialchars($totalAkun) ;?>+</h2>
                            <p class="mb-0">Akun Terdaftar</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="text-center d-flex flex-column justify-content-center align-items-center py-3">
                        <div class="bs-icon-xl bs-icon-circle bs-icon-primary d-flex flex-shrink-0 justify-content-center align-items-center d-inline-block mb-2 bs-icon lg"><i class="fas fa-receipt"></i></div>
                        <div class="px-3">
                            <h2 class="fw-bold mb-0"><?php echo htmlspecialchars($totalTransaksi) ;?>+</h2>
                            <p class="mb-0">Jumlah Transaksi</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="text-center d-flex flex-column justify-content-center align-items-center py-3">
                        <div class="bs-icon-xl bs-icon-circle bs-icon-primary d-flex flex-shrink-0 justify-content-center align-items-center d-inline-block mb-2 bs-icon lg"><i class="fas fa-building"></i></div>
                        <div class="px-3">
                            <h2 class="fw-bold mb-0"><?php echo htmlspecialchars($totalKoperasi) ;?></h2>
                            <p class="mb-0">Koperasi Terdaftar</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section>
        <div class="container py-4 py-xl-5">
            <div class="row">
                <div class="col-md-8 col-xl-6 mx-auto p-4">
                    <div class="d-flex align-items-center align-items-md-start align-items-xl-center">
                        <div class="bs-icon-xl bs-icon-circle bs-icon-primary d-flex flex-shrink-0 justify-content-center align-items-center me-4 d-inline-block bs-icon xl"><i class="fas fa-users"></i></div>
                        <div>
                            <h4>Manajemen Akun</h4>
                            <p>Dapat menggunakan lebih dari satu akun dalam satu koperasi, untuk mengaktifkannya terdapat di setup koperasi.</p><a href="#">Lebih Lanjut&nbsp;<svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor" viewBox="0 0 16 16" class="bi bi-arrow-right">
                                    <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8"></path>
                                </svg></a>
                        </div>
                    </div>
                    <hr class="my-5">
                    <div class="d-flex align-items-center align-items-md-start align-items-xl-center">
                        <div class="bs-icon-xl bs-icon-circle bs-icon-primary d-flex flex-shrink-0 justify-content-center align-items-center order-last ms-4 d-inline-block bs-icon xl"><i class="fas fa-user-cog"></i></div>
                        <div>
                            <h4>Manajemen Anggota</h4>
                            <p>Manajemen pendataan anggota untuk segala transaksi simpan pinjam.</p><a href="#">Lebih Lanjut<svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor" viewBox="0 0 16 16" class="bi bi-arrow-right">
                                    <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8"></path>
                                </svg></a>
                        </div>
                    </div>
                    <hr class="my-5">
                    <div class="d-flex align-items-center align-items-md-start align-items-xl-center">
                        <div class="bs-icon-xl bs-icon-circle bs-icon-primary d-flex flex-shrink-0 justify-content-center align-items-center me-4 d-inline-block bs-icon xl"><i class="fas fa-balance-scale"></i></div>
                        <div>
                            <h4>Perhitungan Jasa Otomatis</h4>
                            <p>Dengan memasukkan jenis pinjaman yang ada jasa dihitung secara otomatis.</p><a href="#">Lebih Lanjut&nbsp;<svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor" viewBox="0 0 16 16" class="bi bi-arrow-right">
                                    <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8"></path>
                                </svg></a>
                        </div>
                    </div>
                    <hr class="my-5">
                    <div class="d-flex align-items-center align-items-md-start align-items-xl-center">
                        <div class="bs-icon-xl bs-icon-circle bs-icon-primary d-flex flex-shrink-0 justify-content-center align-items-center order-last ms-4 d-inline-block bs-icon xl"><i class="fas fa-chart-pie"></i></div>
                        <div>
                            <h4>Pantau Keuangan Real-time</h4>
                            <p>Pantau keuangan, keanggotaan, simpanan secara real-time dengan chart</p><a href="#">Lebih Lanjut<svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor" viewBox="0 0 16 16" class="bi bi-arrow-right">
                                    <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8"></path>
                                </svg></a>
                        </div>
                    </div>
                    <hr class="my-5">
                    <div class="d-flex align-items-center align-items-md-start align-items-xl-center">
                        <div class="bs-icon-xl bs-icon-circle bs-icon-primary d-flex flex-shrink-0 justify-content-center align-items-center me-4 d-inline-block bs-icon xl"><i class="fas fa-file-download"></i></div>
                        <div>
                            <h4>Pembuatan Laporan</h4>
                            <p>Laporan tahunan? dengan aplikasi trisuladana hanya dengan klik satu tombol</p><a href="#">Lebih Lanjut&nbsp;<svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor" viewBox="0 0 16 16" class="bi bi-arrow-right">
                                    <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8"></path>
                                </svg></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="py-5 bg-light">
        <h1 class="text-center text-success" style="padding-top: 15px;padding-bottom: 15px;color: #262626!important;">Pertanyaan</h1>
        <div class="container">
            <div class="row">
                <div class="col-md-6 offset-md-3">
                    <div id="faqlist" class="accordion accordion-flush">
                        <div class="accordion-item">
                            <h2 class="accordion-header"><button class="btn accordion-button collapsed btn-depan" type="button" data-bs-toggle="collapse" data-bs-target="#content-accordion-1">Bagaimana cara mendaftar?</button></h2>
                            <div id="content-accordion-1" class="accordion-collapse collapse" data-bs-parent="#faqlist">
                                <p class="accordion-body"> Klik daftar lalu lengkapi semua detail yang di butuhkan, dari identitas koperasi serta username admin koperasi yang unik dan password.</p>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header"><button class="btn accordion-button collapsed btn-depan" type="button" data-bs-toggle="collapse" data-bs-target="#content-accordion-2">Beda daftar dan akses akun?</button></h2>
                            <div id="content-accordion-2" class="accordion-collapse collapse" data-bs-parent="#faqlist">
                                <p class="accordion-body"> Daftar adalah untuk membuat akses koperasi baru. Setiap koperasi mempunyai banyak akun yang bisa mengelola, itulah akses akun</p>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header"><button class="btn accordion-button collapsed btn-depan" type="button" data-bs-toggle="collapse" data-bs-target="#content-accordion-3">Apa itu Admin?</button></h2>
                            <div id="content-accordion-3" class="accordion-collapse collapse" data-bs-parent="#faqlist">
                                <p class="accordion-body"> Admin berfungsi sebagai pengelola koperasi, setiap perubahan data hanya bisa di ubah oleh admin, namun menambahkan data bisa siapa saja.</p>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header"><button class="btn accordion-button collapsed btn-depan" type="button" data-bs-toggle="collapse" data-bs-target="#content-accordion-4">Bisakah meaktifkan anggota non-aktif?</button></h2>
                            <div id="content-accordion-4" class="accordion-collapse collapse" data-bs-parent="#faqlist">
                                <p class="accordion-body"> Bisa data anggota lama tetap disimpan untuk keamanan data lain, seperti riwayat, simpanan, atau pinjaman lama. Caranya ada di bagain anggota kemudian aktifkan</p>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header"><button class="btn accordion-button collapsed btn-depan" type="button" data-bs-toggle="collapse" data-bs-target="#content-accordion-5">Bisakah akses akun dicabut?</button></h2>
                            <div id="content-accordion-5" class="accordion-collapse collapse" data-bs-parent="#faqlist">
                                <p class="accordion-body"> Bisa dengan di hapus oleh admin.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section>
        <div class="simple-slider">
            <div class="swiper-container">
                <div class="swiper-wrapper">
                    <div class="swiper-slide" style="background: url(&quot;/assets/img/FormAnggota.png&quot;) center / contain no-repeat;"></div>
                    <div class="swiper-slide" style="background: url(&quot;/assets/img/Setupkop.png&quot;) center center / contain no-repeat;"></div>
                    <div class="swiper-slide" style="background: url(&quot;/assets/img/Pinjaman.png&quot;) center center / contain no-repeat;"></div>
                </div>
                <div class="swiper-pagination"></div>
                <div class="swiper-button-prev"></div>
                <div class="swiper-button-next"></div>
            </div>
        </div>
    </section>
    <section>
        <section class="py-4 py-xl-5">
            <div class="container">
                <div class="border rounded border-0 border-dark overflow-hidden" style="background: #262626;">
                    <div class="row g-0">
                        <div class="col-md-6">
                            <div class="text-white p-4 p-md-5">
                                <h2 class="fw-bold text-white mb-3">Kelola pembukuan online jadi lebih mudah dengan Trisula</h2>
                                <p class="mb-4">Biarkan kami mempercepat dan mengautomasikan proses akuntansi serta keunagan koperasi simpan pinjam</p>
                                <div class="my-3">
                                    <a class="btn btn-primary btn-lg me-2 transform-translate btn-depan" role="button" href="https://wa.me/6285712216860">
                                        <i class="fab fa-whatsapp me-2"></i>
                                        WhatsApp sekarang
                                    </a>
                                    <a class="btn btn-lg btn-outline-primary text-white transform-translate btn-depan" role="button" href="Daftar/">
                                        Daftar
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 order-first order-md-last" style="min-height: 250px;"><img alt="Gambar" class="w-100 h-100 fit-cover bluring" src="/assets/img/CALL.png"></div>
                    </div>
                </div>
            </div>
        </section>
    </section>
    <footer class="text-white" style="background: #262626;">
        <div class="container py-4 py-lg-5">
            <div class="row justify-content-center">
                <div class="col-sm-4 col-md-3 text-center text-lg-start d-flex flex-column item">
                    <h3 class="fs-6 text-white">Fitur</h3>
                    <ul class="list-unstyled">
                        <li class="transform-translate"><a class="link-light" href="#">Akun</a></li>
                        <li class="transform-translate"><a class="link-light" href="#">Keanggotaan</a></li>
                        <li class="transform-translate"><a class="link-light" href="#">Pinjaman</a></li>
                        <li class="transform-translate"><a class="link-light" href="#">Simpanan</a></li>
                        <li class="transform-translate"><a class="link-light" href="#">Perhitungan Jasa</a></li>
                    </ul>
                </div>
                <div class="col-sm-4 col-md-3 text-center text-lg-start d-flex flex-column item">
                    <h3 class="fs-6 text-white">Tentang</h3>
                    <ul class="list-unstyled">
                        <li class="transform-translate"><a class="link-light" href="#">Developer</a></li>
                        <li class="transform-translate"><a class="link-light" href="#">Tim</a></li>
                        <li class="transform-translate"><a class="link-light" href="#">Legalitas</a></li>
                    </ul>
                </div>
                <div class="col-sm-4 col-md-3 text-center text-lg-start d-flex flex-column item">
                    <h3 class="fs-6 text-white">Informaasi</h3>
                    <ul class="list-unstyled">
                        <li class="transform-translate"><a class="link-light" href="mailto:fauzantrisuladana@fratsia.com">Email</a></li>
                        <li class="transform-translate"><a class="link-light" href="https://wa.me/+6285712216860">WhatsApp</a></li>
                        <li class="transform-translate"><a class="link-light" href="https://www.google.com/maps?q=-7.762157374478464,110.33746920365652">Kantor</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 text-center text-lg-start d-flex flex-column align-items-center order-first align-items-lg-start order-lg-last item social">
                    <div class="fw-bold d-flex align-items-center mb-2 transform-translate"><img alt="logo" class="logobesar" src="/assets/img/Trisula%20logo%20besar.png"></div>
                </div>
            </div>
            <hr>
            <div class="d-flex justify-content-between align-items-center pt-3">
                <p class="mb-0">Copyright © 2025 Trisula</p>
                <ul class="list-inline mb-0">
                    <li class="list-inline-item"><svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor" viewBox="0 0 16 16" class="bi bi-facebook">
                            <path d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951"></path>
                        </svg></li>
                    <li class="list-inline-item"><svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor" viewBox="0 0 16 16" class="bi bi-twitter">
                            <path d="M5.026 15c6.038 0 9.341-5.003 9.341-9.334 0-.14 0-.282-.006-.422A6.685 6.685 0 0 0 16 3.542a6.658 6.658 0 0 1-1.889.518 3.301 3.301 0 0 0 1.447-1.817 6.533 6.533 0 0 1-2.087.793A3.286 3.286 0 0 0 7.875 6.03a9.325 9.325 0 0 1-6.767-3.429 3.289 3.289 0 0 0 1.018 4.382A3.323 3.323 0 0 1 .64 6.575v.045a3.288 3.288 0 0 0 2.632 3.218 3.203 3.203 0 0 1-.865.115 3.23 3.23 0 0 1-.614-.057 3.283 3.283 0 0 0 3.067 2.277A6.588 6.588 0 0 1 .78 13.58a6.32 6.32 0 0 1-.78-.045A9.344 9.344 0 0 0 5.026 15"></path>
                        </svg></li>
                    <li class="list-inline-item"><svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor" viewBox="0 0 16 16" class="bi bi-instagram">
                            <path d="M8 0C5.829 0 5.556.01 4.703.048 3.85.088 3.269.222 2.76.42a3.917 3.917 0 0 0-1.417.923A3.927 3.927 0 0 0 .42 2.76C.222 3.268.087 3.85.048 4.7.01 5.555 0 5.827 0 8.001c0 2.172.01 2.444.048 3.297.04.852.174 1.433.372 1.942.205.526.478.972.923 1.417.444.445.89.719 1.416.923.51.198 1.09.333 1.942.372C5.555 15.99 5.827 16 8 16s2.444-.01 3.298-.048c.851-.04 1.434-.174 1.943-.372a3.916 3.916 0 0 0 1.416-.923c.445-.445.718-.891.923-1.417.197-.509.332-1.09.372-1.942C15.99 10.445 16 10.173 16 8s-.01-2.445-.048-3.299c-.04-.851-.175-1.433-.372-1.941a3.926 3.926 0 0 0-.923-1.417A3.911 3.911 0 0 0 13.24.42c-.51-.198-1.092-.333-1.943-.372C10.443.01 10.172 0 7.998 0h.003zm-.717 1.442h.718c2.136 0 2.389.007 3.232.046.78.035 1.204.166 1.486.275.373.145.64.319.92.599.28.28.453.546.598.92.11.281.24.705.275 1.485.039.843.047 1.096.047 3.231s-.008 2.389-.047 3.232c-.035.78-.166 1.203-.275 1.485a2.47 2.47 0 0 1-.599.919c-.28.28-.546.453-.92.598-.28.11-.704.24-1.485.276-.843.038-1.096.047-3.232.047s-2.39-.009-3.233-.047c-.78-.036-1.203-.166-1.485-.276a2.478 2.478 0 0 1-.92-.598 2.48 2.48 0 0 1-.6-.92c-.109-.281-.24-.705-.275-1.485-.038-.843-.046-1.096-.046-3.233 0-2.136.008-2.388.046-3.231.036-.78.166-1.204.276-1.486.145-.373.319-.64.599-.92.28-.28.546-.453.92-.598.282-.11.705-.24 1.485-.276.738-.034 1.024-.044 2.515-.045v.002zm4.988 1.328a.96.96 0 1 0 0 1.92.96.96 0 0 0 0-1.92zm-4.27 1.122a4.109 4.109 0 1 0 0 8.217 4.109 4.109 0 0 0 0-8.217zm0 1.441a2.667 2.667 0 1 1 0 5.334 2.667 2.667 0 0 1 0-5.334"></path>
                        </svg></li>
                </ul>
            </div>
        </div>
    </footer><a class="d-flex justify-content-center align-items-center text-white rounded-circle crcrtop" style="width: 50px;height: 50px;background: #262626;font-size: 32px;" href="#"><i class="fas fa-chevron-up"></i></a>
    <script src="/assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="/assets/js/script.js"></script>
    <script src="/assets/js/Simple-Slider-swiper-bundle.min.js"></script>
    <script src="/assets/js/Simple-Slider.js"></script>
</body>

</html>