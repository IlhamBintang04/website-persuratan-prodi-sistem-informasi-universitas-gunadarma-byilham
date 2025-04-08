<?php
session_start();
include 'config/database.php';

if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}

$keyword = '';
if (isset($_GET['keyword'])) {
    $keyword = trim($_GET['keyword']); // Membersihkan input dari spasi berlebih
}

// Proses hapus jika tombol hapus ditekan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hapus_id'])) {
    $id = intval($_POST['hapus_id']);
    $query = "DELETE FROM surat_masuk WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Data berhasil dihapus.</div>";
    } else {
        echo "<div class='alert alert-danger'>Gagal menghapus data: " . $conn->error . "</div>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Surat masuk - Penyuratan Prodi Sistem Infromasi</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
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

    .table-container {
        background-color: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
    }

    .thead-dark th {
        background-color: #7F40B6;
        color: white;
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

    .content h2 {
        color: #7F40B6;
        font-weight: bold;
        font-style: italic;
    }
    /* Gaya Button Tambah Surat */
    .btn-primary {
    background-color: #5A2D9D; /* Warna latar belakang */
    border-color: #5A2D9D; /* Warna border */
    }

    .btn-primary:hover {
    background-color: #7F40B6; /* Warna latar belakang saat hover */
    border-color: #7F40B6; /* Warna border saat hover */
    }
    
</style>

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
                            <a class="nav-link" href="laporan_surat_masuk.php">Laporan surat masuk</a>
                        </li>
                        <li class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'laporan_surat_keluar.php') ? 'active' : ''; ?>">
                            <a class="nav-link" href="laporan_surat_keluar.php">Laporan surat keluar</a>
                        </li>
                        <li class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'admin.php') ? 'active' : ''; ?>">
                        <a class="nav-link" href="admin.php">Admin</a>
                    </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">Logout</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Content -->
        <div class="content-container">
            <div class="container mt-5 content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Surat Masuk</h2>
                    <a href="tambah_surat_masuk.php" class="btn btn-primary">Tambah Surat</a>
                </div>

                <form method="GET" action="" class="mb-4">
                    <div class="input-group">
                        <input type="text" name="keyword" class="form-control" placeholder="Cari ..." value="<?php echo htmlspecialchars($keyword); ?>" aria-label="Cari ...">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-outline-secondary">Cari</button>
                        </div>
                    </div>
                </form>

                <div class="table-container">
                    <table class="table table-bordered table-striped">
                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th>Nomor Surat</th>
                                <th>Pengirim</th>
                                <th>Waktu</th>
                                <th>Perihal</th>
                                <th>File Scan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Menggunakan prepared statement untuk pencarian di semua kolom
                            $sql = "SELECT * FROM surat_masuk
                                    WHERE nomor_surat LIKE ? 
                                    OR pengirim_surat LIKE ? 
                                    OR waktu_surat LIKE ? 
                                    OR perihal_surat LIKE ?";
                            $stmt = $conn->prepare($sql);
                            $search = "%" . $keyword . "%";
                            $stmt->bind_param("ssss", $search, $search, $search, $search);
                            $stmt->execute();
                            $result = $stmt->get_result();

                            if ($result->num_rows > 0):
                                $no = 1; // Inisialisasi nomor urut
                                while ($row = $result->fetch_assoc()):
                            ?>
                                    <tr>
                                        <td><?= $no++; ?></td> <!-- Menampilkan nomor urut -->
                                        <td><?= htmlspecialchars($row['nomor_surat']); ?></td>
                                        <td><?= htmlspecialchars($row['pengirim_surat']); ?></td>
                                        <td><?= htmlspecialchars($row['waktu_surat']); ?></td>
                                        <td><?= htmlspecialchars($row['perihal_surat']); ?></td>
                                        <td>
                                            <?php
                                            $file_scan = $row['file_scan'];
                                            $max_length = 15; // Panjang maksimal tampilan nama file
                                            echo !empty($file_scan)
                                                ? (strlen($file_scan) > $max_length
                                                    ? htmlspecialchars(substr($file_scan, 0, $max_length)) . "..."
                                                    : htmlspecialchars($file_scan))
                                                : "Tidak ada file";
                                            ?>
                                        </td>
                                        <td>
                                            <a href="uploads/<?= htmlspecialchars($row['file_scan']); ?>" target="_blank" class="btn btn-info btn-sm">Lihat File</a>
                                            <a href="edit_surat_masuk.php?id=<?= $row['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                            <form method="POST" style="display:inline;">
                                                <input type="hidden" name="hapus_id" value="<?= $row['id']; ?>">
                                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php
                                endwhile;
                            else:
                                ?>
                                <tr>
                                    <td colspan="7">Tidak ada data surat</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer>
            Copyright © 2025 Kelompok 1 Penyuratan 4KA26 
        </footer>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
