<?php
session_start();
include 'config/database.php';

// Redirect if not logged in
if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}

// Validate ID received via GET
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$sql = "SELECT * FROM surat_keluar WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
} else {
    echo "Surat tidak ditemukan.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $nomor_surat = htmlspecialchars(trim($_POST['nomor_surat']));
    $pengirim_surat = htmlspecialchars(trim($_POST['pengirim_surat']));
    $waktu_surat = $_POST['waktu_surat'] ?? null;
    $lampiran_surat = $_POST['lampiran_surat'] ?? null;

    // Handle perihal_surat based on selection
    $perihal_surat = $_POST['perihal_surat'] === 'dll' ? $_POST['manual_perihal'] : $_POST['perihal_surat'];
    $penerima_surat = $_POST['penerima_surat'] ?? null;
    $jabatan_penerima = $_POST['jabatan_penerima'] ?? null;
    $isi_surat = $_POST['isi_surat'] ?? null;
    $unit_penerbit = "Prodi Sistem Informasi";
    $nama_mengesahkan = $_POST['nama_mengesahkan'] ?? null; // Ensure this is set
    $tempat_surat = $_POST['tempat_surat'] ?? null;
    $nama_tembusan = $_POST['nama_tembusan'] ?? null;

    // Check if a new file was uploaded
    $file_scan = $row['file_scan']; // Use old value as default
    if (isset($_FILES['file_scan']) && $_FILES['file_scan']['size'] > 0) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["file_scan"]["name"]);
        $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        if ($_FILES["file_scan"]["size"] > 5000000) {
            echo "Maaf, file terlalu besar.";
        } elseif (!in_array($fileType, ['jpg', 'jpeg', 'png'])) {
            echo "Maaf, hanya file JPG, JPEG, PNG yang diperbolehkan.";
        } elseif (move_uploaded_file($_FILES["file_scan"]["tmp_name"], $target_file)) {
            $file_scan = basename($_FILES["file_scan"]["name"]);
        } else {
            echo "Maaf, terjadi kesalahan saat mengupload file.";
        }
    }

    // Process update data into database
    $sql = "UPDATE surat_keluar SET 
                pengirim_surat = ?, 
                waktu_surat = ?, 
                lampiran_surat = ?, 
                perihal_surat = ?, 
                penerima_surat = ?, 
                jabatan_penerima = ?, 
                isi_surat = ?, 
                unit_penerbit = ?, 
                tempat_surat = ?, 
                nama_mengesahkan = ?, 
                nama_tembusan = ?, 
                file_scan = ?
            WHERE nomor_surat = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "sssssssssssss",
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
        $file_scan,
        $nomor_surat
    );

    if ($stmt->execute()) {
        header('Location: dashboard.php');
    } else {
        echo "Error: " . $stmt->error;
    }
} else {
    // Fetch data for editing
    if ($id) {
        $query = "SELECT * FROM surat_keluar WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        // Ensure there's data before accessing $row
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            // Get value of perihal_surat and manual_perihal
            $perihal_surat = $row['perihal_surat'];
            $nama_mengesahkan = $row['nama_mengesahkan']; // Ensure this is set
        } else {
            echo "Data surat keluar tidak ditemukan.";
            exit;
        }
    } else {
        echo "Nomor surat tidak valid.";
        exit;
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

    <body>

        <div class="content-container">
            <div class="container mt-5">
                <div class="card">
                    <div class="card-header">
                        <h2>Edit Surat Keluar</h2>
                    </div>
                    <div class="card-body">
                        <form method="post" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="nomor_surat">Nomor Surat</label>
                                <input type="text" class="form-control" id="nomor_surat" name="nomor_surat" value="<?php echo htmlspecialchars($row['nomor_surat']); ?>" required>
                            </div>
                            <div class="form-group">
                            <label for="pengirim_surat">Pengirim Surat</label>
                            <input type="text" class="form-control" id="pengirim_surat" name="pengirim_surat" value="Prodi Sistem Informasi" readonly required>
                        </div>
                            <div class="form-group">
                                <label for="waktu_surat">Waktu Surat</label>
                                <input type="datetime-local" class="form-control" id="waktu_surat" name="waktu_surat" value="<?php echo date('Y-m-d\TH:i', strtotime($row['waktu_surat'])); ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="perihal_surat">Perihal Surat</label>
                                <div class="input-group">
                                    <select class="form-control" id="perihal_surat" name="perihal_surat" required>
                                        <option value="">-- Pilih Perihal --</option>
                                        <option value="Surat Undangan" <?php echo ($perihal_surat == 'Surat Undangan') ? 'selected' : ''; ?>>Surat Undangan</option>
                                        <option value="Surat Peminjaman Ruang" <?php echo ($perihal_surat == 'Surat Peminjaman Ruang') ? 'selected' : ''; ?>>Surat Peminjaman Ruang</option>
                                        <option value="Surat Keterangan" <?php echo ($perihal_surat == 'Surat Keterangan') ? 'selected' : ''; ?>>Surat Keterangan</option>
                                        <option value="Surat Keterangan Permohonan" <?php echo ($perihal_surat == 'Surat Keterangan Permohonan') ? 'selected' : ''; ?>>Surat Keterangan Permohonan</option>
                                        <option value="Surat Standby Uts" <?php echo ($perihal_surat == 'Surat Standby Uts') ? 'selected' : ''; ?>>Surat Standby Uts</option>
                                        <option value="Surat Standby Uas" <?php echo ($perihal_surat == 'Surat Standby Uas') ? 'selected' : ''; ?>>Surat Standby Uas</option>
                                        <option value="Surat Izin" <?php echo ($perihal_surat == 'Surat Izin') ? 'selected' : ''; ?>>Surat Izin</option>
                                        <option value="dll" <?php echo ($perihal_surat == 'dll') ? 'selected' : ''; ?>>Lainnya (Isi Manual)</option>
                                    </select>
                                </div>

                                <!-- Input Manual -->
                                <input
                                    type="text"
                                    class="form-control mt-2"
                                    id="manual_perihal"
                                    name="manual_perihal"
                                    value="<?php echo htmlspecialchars($row['perihal_surat']); ?>">
                            </div>


                            <div class="form-group">
                                <label for="lampiran_surat">Lampiran Surat</label>
                                <input type="text" class="form-control" id="lampiran_surat" name="lampiran_surat" value="<?php echo htmlspecialchars($row['lampiran_surat']); ?>">
                            </div>
                            <div class="form-group">
                                <label for="penerima_surat">Penerima Surat</label>
                                <input type="text" class="form-control" id="penerima_surat" name="penerima_surat" value="<?php echo htmlspecialchars($row['penerima_surat']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="jabatan_penerima">Jabatan Penerima</label>
                                <input type="text" class="form-control" id="jabatan_penerima" name="jabatan_penerima" value="<?php echo htmlspecialchars($row['jabatan_penerima']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="isi_surat">Isi Surat</label>
                                <textarea class="form-control" id="isi_surat" name="isi_surat" rows="5" required><?php echo htmlspecialchars($row['isi_surat']); ?></textarea>

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
                                <label for="nama_mengesahkan">Nama Mengesahkan</label>
                                <div class="input-group">
                                    <select class="form-control" id="nama_mengesahkan" name="nama_mengesahkan" required>
                                    <option value="">-- Pilih Nama Mengesahkan --</option>
                                    <option value="Prof.Dr.rer.nat.Achmad Benny Mutiara" <?php echo ($nama_mengesahkan == 'Prof.Dr.rer.nat.Achmad Benny Mutiara') ? 'selected' : ''; ?>>Prof.Dr.rer.nat.Achmad Benny Mutiara</option>
                                    <option value="Prof.Dewi Agushinta Rahayu" <?php echo ($nama_mengesahkan == 'Prof.Dewi Agushinta Rahayu') ? 'selected' : ''; ?>>Prof.Dewi Agushinta Rahayu</option>
                                    <option value="Dr.Metty Mustikasari,Skom.MSc" <?php echo ($nama_mengesahkan == 'Dr.Metty Mustikasari,Skom.MSc') ? 'selected' : ''; ?>>Dr.Metty Mustikasari,Skom.MSc</option>
                                    <option value="Dr.Marliza Garnefi Gumay,Skom.MMSI" <?php echo ($nama_mengesahkan == 'Dr.Marliza Garnefi Gumay,Skom.MMSI') ? 'selected' : ''; ?>>Dr.Marliza Garnefi Gumay,Skom.MMSI</option>
                                    <option value="Dr.Setia Wirawan,Skom.MMSI" <?php echo ($nama_mengesahkan == 'Dr.Setia Wirawan,Skom.MMSI') ? 'selected' : ''; ?>>Dr.Setia Wirawan,Skom.MMSI</option>
                                    <option value="Dr.Ana Kurniawati,Skom.MMSI" <?php echo ($nama_mengesahkan == 'Dr.Ana Kurniawati,Skom.MMSI') ? 'selected' : ''; ?>>Dr.Ana Kurniawati,Skom.MMSI</option>
                                    </select>

                                </div>
                                
                            <div class="form-group">
                                <label for="nama_tembusan">Nama Tembusan</label>
                                <input type="text" class="form-control" id="nama_tembusan" name="nama_tembusan" value="<?php echo htmlspecialchars($row['nama_tembusan']); ?>">
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
        </div>
        
         <!-- Footer -->
         <footer>
            Copyright © 2025 Kelompok 1 Penyuratan 4KA26 
        </footer>

        <script>
            document.getElementById('perihal_surat').addEventListener('change', function() {
                const manualInput = document.getElementById('manual_perihal');
                if (this.value === 'dll') {
                    manualInput.style.display = 'block';
                } else {
                    manualInput.style.display = 'none';
                    manualInput.value = ''; // Reset jika bukan "dll"
                }
            });
        </script>

        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    </body>

</html>