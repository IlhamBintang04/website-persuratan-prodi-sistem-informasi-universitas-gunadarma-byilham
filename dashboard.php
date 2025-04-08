<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Home - Penyuratan Prodi Sistem Informasi</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        /* Gaya Navbar */
        .navbar {
            background-color: #3B1E54;
            padding: 10px 0;
        }

        .navbar-brand {
            font-size: 18px;
            font-weight: bold;
            color: white !important;
        }

        .navbar-nav .nav-link {
            font-size: 16px;
            padding: 10px 15px;
            color: white !important;
            margin-right: 15px;
            position: relative; /* Memungkinkan penambahan garis bawah */
        }

        .navbar-nav .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 5px;
        }

        /* Menambahkan garis bawah pada link yang aktif */
        .navbar-nav .nav-item.active .nav-link {
            border-bottom: 2px solid white; /* Menambahkan garis bawah */
        }

        .navbar-toggler {
            border-color: white;
        }

        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml;charset=UTF8,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='%23fff' viewBox='0 0 30 30'%3E%3Cpath stroke='rgba(255, 255, 255, 1)' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3E%3C/svg%3E");
        }

        /* Gaya Footer */
        footer {
            background-color: #3B1E54;
            color: white;
            text-align: center;
            padding: 10px 0;
            position: flex;
            width: 100%;
            bottom: 0;
        }

        /* Gaya Konten */
        .content {
            position: relative;
            height: 100vh; /* Membuat konten mengambil seluruh tinggi layar */
        }

        .carousel-item img {
            width: 100%;    /* Memastikan gambar memenuhi lebar layar */
            height: 100vh;  /* Memastikan gambar memenuhi tinggi layar */
            object-fit: cover;  /* Menjaga proporsi gambar dan memotong bagian yang berlebih */
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">
                <img src="img/navimage.png" alt="Sistem Informasi" style="height: 50px;">
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : ''; ?>">
                        <a class="nav-link" href="dashboard.php">Home</a>
                    </li>
                    <li class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'surat_masuk.php') ? 'active' : ''; ?>">
                        <a class="nav-link" href="surat_masuk.php">Surat Masuk</a>
                    </li>
                    <li class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'surat_keluar.php') ? 'active' : ''; ?>">
                        <a class="nav-link" href="surat_keluar.php">Surat Keluar</a>
                    </li>
                    <li class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'laporan_surat_masuk.php') ? 'active' : ''; ?>">
                        <a class="nav-link" href="laporan_surat_masuk.php">Laporan surat masuk</a>
                    </li>
                    <li class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'laporan_surat_keluar.php') ? 'active' : ''; ?>">
                        <a class="nav-link" href="laporan_surat_keluar.php">Laporan surat keluar</a>
                    </li>
                    <li class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'laporan_surat_keluar.php') ? 'active' : ''; ?>">
                        <a class="nav-link" href="admin.php">Admin</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="content">
    <div id="homeCarousel" class="carousel slide" data-ride="carousel" data-interval="1000">
        <ol class="carousel-indicators">
            <li data-target="#homeCarousel" data-slide-to="0" class="active"></li>
            <li data-target="#homeCarousel" data-slide-to="1"></li>
            <li data-target="#homeCarousel" data-slide-to="2"></li>
        </ol>
        <div class="carousel-inner">
            <div class="carousel-item active" data-interval="1000">
                <img src="img/homeimage.png" alt="Slide 1">
            </div>
            <div class="carousel-item" data-interval="1000">
                <img src="img/Frame 1.png" alt="Slide 2">
            </div>
            <div class="carousel-item" data-interval="1000">
                <img src="img/FRM 1.png" alt="Slide 3">
            </div>
        </div>
        <a class="carousel-control-prev" href="#homeCarousel" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next" href="#homeCarousel" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>
    </div>
</div>

<script>
    // Atur default interval menjadi 0,5 detik
    $('#homeCarousel').carousel({
        interval: 1000 // 500 ms = 0,5 detik
    });
</script>


<script>
    // Atur default interval jika ingin memastikan waktu transisi tetap 1 detik
    $('#homeCarousel').carousel({
        interval: 1000 // 1000 ms = 1 detik
    });
</script>


    <footer>
        Copyright © 2025 Kelompok 1 Penyuratan 4KA26
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.4.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
