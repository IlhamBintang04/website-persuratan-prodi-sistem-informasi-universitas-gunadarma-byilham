<?php
session_start();
include 'config/database.php';

if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}

$id = $_GET['id'];
$sql = "SELECT * FROM surat_masuk WHERE id='$id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
} else {
    echo "Surat tidak ditemukan.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $waktu_surat = $_POST['waktu_surat'];
    $lampiran_surat = $_POST['lampiran_surat'];
    $perihal_surat = $_POST['perihal_surat'];
    $unit_penerbit = $_POST['unit_penerbit'];
    $tempat_surat = "Depok";
    $nama_tembusan = $_POST['nama_tembusan'];

    $uploadOk = 1;
    $file_name = $row['file_scan'];
    if (!empty($_FILES["file_scan"]["name"])) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["file_scan"]["name"]);
        $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        if ($_FILES["file_scan"]["size"] > 5000000) {
            echo "Maaf, file terlalu besar.";
            $uploadOk = 0;
        }

        if (
            $fileType != "jpg" && $fileType != "png" && $fileType != "jpeg"
            && $fileType != "pdf" && $fileType != "doc" && $fileType != "docx"
        ) {
            echo "Maaf, hanya file JPG, JPEG, PNG, PDF, DOC & DOCX yang diperbolehkan.";
            $uploadOk = 0;
        }

        if ($uploadOk == 0) {
            echo "Maaf, file Anda tidak terupload.";
        } else {
            if (move_uploaded_file($_FILES["file_scan"]["tmp_name"], $target_file)) {
                $file_name = basename($_FILES["file_scan"]["name"]);
            } else {
                echo "Maaf, terjadi kesalahan saat mengupload file.";
                $uploadOk = 0;
            }
        }
    }

    if ($uploadOk) {
        $sql = "UPDATE surat_masuk SET 
                    waktu_surat='$waktu_surat', 
                    lampiran_surat='$lampiran_surat', 
                    perihal_surat='$perihal_surat', 
                    unit_penerbit='$unit_penerbit', 
                    tempat_surat='$tempat_surat', 
                    nama_tembusan='$nama_tembusan', 
                    file_scan='$file_name' 
                WHERE id='$id'";

        if ($conn->query($sql) === TRUE) {
            header('Location: surat_masuk.php');
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Tambah Surat</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        /* Gaya Navbar */
        .navbar {
            background-color: #3B1E54;
            padding: 10px 0;
            position: sticky;
            width: 100%;
            top: 0;
            left: 0;
            z-index: 1000;
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
            position: relative;
        }

        .navbar-nav .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 5px;
        }

        .navbar-nav .nav-item.active .nav-link {
            border-bottom: 2px solid white;
        }

        /* Gaya Konten */
        .content-container {
            padding-top: 50px;
            background-image: url('img/background.png'); /* Periksa path gambar di sini */
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            color: white;
            min-height: 100vh;
            background-color: #f5f5f5; /* Fallback color jika gambar tidak ditemukan */
        }

        .content {
            padding: 20px;
        }

        .card-body {
            color: #000;
        }

        .card-header h2 {
            color: black;
        }

        .form-container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }

        .content h2 {
            color: #7F40B6;
            font-weight: bold;
            font-style: italic;
        }

        .form-container label {
            color: black;
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
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
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
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">Logout</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="content-container">
            <div class="container mt-5">
                <div class="card">
                    <div class="card-header">
                        <h2>Edit Surat Keluar</h2>
                    </div>
            <div class="card-body">
                <form method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="waktu_surat">Waktu Surat</label>
                        <input type="datetime-local" class="form-control" id="waktu_surat" name="waktu_surat" value="<?php echo date('Y-m-d\TH:i', strtotime($row['waktu_surat'])); ?>">
                    </div>
                    <div class="form-group">
                        <label for="lampiran_surat">Lampiran Surat</label>
                        <textarea class="form-control" id="lampiran_surat" name="lampiran_surat"><?php echo $row['lampiran_surat']; ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="perihal_surat">Perihal Surat</label>
                        <input type="text" class="form-control" id="perihal_surat" name="perihal_surat" value="<?php echo $row['perihal_surat']; ?>">
                    </div>
                    <div class="form-group">
                        <label for="unit_penerbit">Unit Penerbit</label>
                        <input type="text" class="form-control" id="unit_penerbit" name="unit_penerbit" value="<?php echo $row['unit_penerbit']; ?>">
                    </div>
                    <div class="form-group">
                        <label for="nama_tembusan">Nama Tembusan</label>
                        <input type="text" class="form-control" id="nama_tembusan" name="nama_tembusan" value="<?php echo $row['nama_tembusan']; ?>">
                    </div>
                    <div class="form-group">
                        <label for="file_scan">Unggah Scan Surat</label>
                        <input type="file" class="form-control" id="file_scan" name="file_scan">
                        <?php if ($row['file_scan']) : ?>
                            <p>File saat ini: <?php echo $row['file_scan']; ?></p>
                        <?php endif; ?>
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        Copyright © 2025 Kelompok 1 Penyuratan 4KA26 
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
