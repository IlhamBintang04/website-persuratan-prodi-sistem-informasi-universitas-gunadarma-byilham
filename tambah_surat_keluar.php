<?php
session_start();
include 'config/database.php';

// Redirect jika tidak login
if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form
    $nomor_surat = htmlspecialchars(trim($_POST['nomor_surat']));
    $pengirim_surat = htmlspecialchars(trim($_POST['pengirim_surat']));
    $waktu_surat = $_POST['waktu_surat'] ?? null;
    $lampiran_surat = $_POST['lampiran_surat'] ?? null;
    $perihal_surat = $_POST['perihal_surat'] === 'dll' ? $_POST['manual_perihal'] : $_POST['perihal_surat'];
    $penerima_surat = $_POST['penerima_surat'] ?? null;
    $jabatan_penerima = $_POST['jabatan_penerima'] ?? null;
    $isi_surat = $_POST['isi_surat'] ?? null;
    $unit_penerbit = "Prodi Sistem Informasi";
    $nama_mengesahkan = $_POST['nama_mengesahkan'] ?? null;
    $tempat_surat = $_POST['tempat_surat'] ?? null;
    $nama_tembusan = $_POST['nama_tembusan'] ?? null;

    // Periksa apakah koneksi database berhasil
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Periksa apakah nomor surat sudah ada
    $query = "SELECT * FROM surat_keluar WHERE nomor_surat = ?";
    $stmt = $conn->prepare($query);
    if ($stmt === false) {
        die("Error in query preparation: " . $conn->error);
    }
    $stmt->bind_param("s", $nomor_surat);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "Nomor surat sudah ada, harap masukkan nomor surat yang berbeda.";
    } else {
        // Proses upload file
        $file_scan = null;
        if (isset($_FILES['file_scan']) && $_FILES['file_scan']['size'] > 0) {
            $target_dir = "uploads/";
            $target_file = $target_dir . basename($_FILES["file_scan"]["name"]);
            $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            if ($_FILES["file_scan"]["size"] > 5000000) {
                echo "Maaf, file terlalu besar.";
            } elseif (!in_array($fileType, ['jpg', 'jpeg', 'png'])) {
                echo "Maaf, hanya file JPG, JPEG, PNG, yang diperbolehkan.";
            } elseif (move_uploaded_file($_FILES["file_scan"]["tmp_name"], $target_file)) {
                $file_scan = basename($_FILES["file_scan"]["name"]);
            } else {
                echo "Maaf, terjadi kesalahan saat mengupload file.";
            }
        }

        // Masukkan data ke database
        $sql = "INSERT INTO surat_keluar (nomor_surat, pengirim_surat, waktu_surat, lampiran_surat, perihal_surat, penerima_surat, jabatan_penerima, isi_surat, unit_penerbit, tempat_surat, nama_mengesahkan, nama_tembusan, file_scan) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            die("Error in query preparation: " . $conn->error);
        }
        $stmt->bind_param(
            "sssssssssssss",
            $nomor_surat,
            $pengirim_surat,
            $waktu_surat,
            $lampiran_surat,
            $perihal_surat,
            $penerima_surat,
            $jabatan_penerima,
            $isi_surat,
            $unit_penerbit,
            $tempat_surat,
            $nama_mengesahkan,
            $nama_tembusan,
            $file_scan
        );

        if ($stmt->execute()) {
            header('Location: dashboard.php');
        } else {
            echo "Error: " . $stmt->error;
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
            position: fixed;
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
            background-image: url('img/background.png');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            color: white;
            min-height: 100vh;
        }

        .content {
            padding: 20px;
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
    <div class="main-container">
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
                            <a class="nav-link" href="laporan_surat_masuk.php">Laporan Surat Masuk</a>
                        </li>
                        <li class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'laporan_surat_keluar.php') ? 'active' : ''; ?>">
                            <a class="nav-link" href="laporan_surat_keluar.php">Laporan Surat Keluar</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">Logout</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="content-container">
            <div class="container mt-5 content">
                <div class="form-container">
                    <h2 class="mb-4">Tambah Surat Keluar</h2>
                    <form action="" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="nomor_surat">Nomor Surat</label>
                            <input type="text" class="form-control" id="nomor_surat" name="nomor_surat" value="NO/PS-SI/S1/ROMAWI/TAHUN" required>
                        </div>

                        <div class="form-group">
                            <label for="pengirim_surat">Pengirim Surat</label>
                            <input type="text" class="form-control" id="pengirim_surat" name="pengirim_surat" value="Prodi Sistem Informasi" readonly required>
                        </div>

                        <div class="form-group">
                            <label for="waktu_surat">Waktu Surat</label>
                            <input type="datetime-local" class="form-control" id="waktu_surat" name="waktu_surat" required>
                        </div>

                        <div class="form-group">
                            <label for="perihal_surat">Perihal Surat</label>
                            <div class="input-group">
                                <select class="form-control" id="perihal_surat" name="perihal_surat" required>
                                    <option value="">-- Pilih Perihal --</option>
                                    <option value="Surat Undangan">Surat Undangan</option>
                                    <option value="Surat Peminjaman Ruang">Surat Peminjaman Ruang</option>
                                    <option value="Surat Keterangan">Surat Keterangan</option>
                                    <option value="Surat Ketrangan Permohonan">Surat Keterangan Permohonan</option>
                                    <option value="Surat Standby Uts">Surat Standby Uts</option>
                                    <option value="Surat Standby Uas">Surat Standby Uas</option>
                                    <option value="Surat Izin">Surat izin</option>
                                    <option value="dll">Lainnya (Isi Manual)</option>
                                </select>
                            </div>
                            <input type="text" class="form-control mt-2" id="manual_perihal" name="manual_perihal"
                                placeholder="Masukkan perihal surat secara manual" style="display: none;">
                        </div>

                        <div class="form-group">
                            <label for="lampiran_surat">Lampiran Surat</label>
                            <input type="text" class="form-control" id="lampiran_surat" name="lampiran_surat">
                        </div>

                        <div class="form-group">
                            <label for="penerima_surat">Penerima Surat</label>
                            <input type="text" class="form-control" id="penerima_surat" name="penerima_surat" required>
                        </div>

                        <div class="form-group">
                            <label for="penerima_surat">Jabatan Penerima Surat</label>
                            <input type="text" class="form-control" id="jabatan_penerima" name="jabatan_penerima" required>
                        </div>

                        <div class="form-group">
                            <label for="isi_surat">Isi Surat</label>
                            <textarea class="form-control" id="isi_surat" name="isi_surat" rows="5" required></textarea>
                        </div>

                        <div class="form-group">
                            <label for="unit_penerbit">Unit Penerbit</label>
                            <input type="text" class="form-control" id="unit_penerbit" name="unit_penerbit" value="Prodi Sistem Informasi" readonly required>
                        </div>

                        <div class="form-group">
                            <label for="tempat_surat">Tempat Surat</label>
                            <input type="text" class="form-control" id="tempat_surat" name="tempat_surat" value="Depok" readonly>
                        </div>

                        <div class="form-group">
                            <label for="nama_mengesahkan">Nama yang Mengesahkan</label>
                            <select class="form-control" id="nama_mengesahkan" name="nama_mengesahkan" required>
                                <option value="Prof.Dr.rer.nat.Achmad Benny Mutiara" selected>Prof.Dr.rer.nat.Achmad Benny Mutiara</option>
                                <option value="Prof.Dewi Agushinta Rahayu">Prof.Dewi Agushinta Rahayu</option>
                                <option value="Dr.Metty Mustikasari">Dr.Metty Mustikasari,Skom.MSc</option>
                                <option value="Dr.Marliza Garnefi Gumay">Dr.Marliza Garnefi Gumay,Skom.MMSI</option>
                                <option value="Dr.Setia Wirawan,Skom.MMSI">Dr.Setia Wirawan,Skom.MMSI</option>
                                <option value="Dr.Ana Kurniawati,Skom.MMSI">Dr.Ana Kurniawati,Skom.MMSI</option>
                               
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="nama_tembusan">Nama Tembusan</label>
                            <input type="text" class="form-control" id="nama_tembusan" name="nama_tembusan">
                        </div>

                        <div class="form-group">
                            <label for="file_scan">File Scan</label>
                            <input type="file" class="form-control" id="file_scan" name="file_scan">
                        </div>

                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <footer>
        Copyright © 2025 Kelompok 1 Penyuratan 4KA26
    </footer>

    <script>
        document.getElementById('perihal_surat').addEventListener('change', function() {
            const manualInput = document.getElementById('manual_perihal');
            if (this.value === 'dll') {
                manualInput.style.display = 'block';
                manualInput.required = true;
            } else {
                manualInput.style.display = 'none';
                manualInput.required = false;
                manualInput.value = ''; // Clear the manual input when switching back to dropdown
            }
        });
    </script>

</body>

</html>