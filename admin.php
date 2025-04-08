<?php
include 'config/database.php';

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

function customEncrypt($string)
{
    $key = "K3L0MP0K1"; // Kunci enkripsi - ganti dengan kunci yang lebih aman
    $result = '';
    for ($i = 0; $i < strlen($string); $i++) {
        $char = substr($string, $i, 1);
        $keychar = substr($key, ($i % strlen($key)) - 1, 1);
        $char = chr(ord($char) + ord($keychar));
        $result .= $char;
    }
    return base64_encode($result);
}

function customDecrypt($string)
{
    $key = "K3L0MP0K1"; // Gunakan kunci yang sama dengan enkripsi
    $result = '';
    $string = base64_decode($string);
    for ($i = 0; $i < strlen($string); $i++) {
        $char = substr($string, $i, 1);
        $keychar = substr($key, ($i % strlen($key)) - 1, 1);
        $char = chr(ord($char) - ord($keychar));
        $result .= $char;
    }
    return $result;
}

// Modifikasi bagian switch case untuk menggunakan enkripsi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'update':
                $id = $_POST['id'];
                $username = $_POST['username'];
                $password = customEncrypt($_POST['password']); // Enkripsi password

                $query = "UPDATE users SET username=?, password=? WHERE id=?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("ssi", $username, $password, $id);

                echo $stmt->execute() ? "Admin berhasil diupdate!" : "Error: " . $stmt->error;
                break;

            case 'add':
                $username = $_POST['username'];
                $password = customEncrypt($_POST['password']); // Enkripsi password

                $query = "INSERT INTO users (username, password) VALUES (?, ?)";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("ss", $username, $password);

                echo $stmt->execute() ? "Admin baru berhasil ditambahkan!" : "Error: " . $stmt->error;
                break;

            case 'delete': // Tambahkan kasus untuk delete
                $id = $_POST['id'];
                $query = "DELETE FROM users WHERE id=?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("i", $id);

                echo $stmt->execute() ? "Admin berhasil dihapus!" : "Error: " . $stmt->error;
                break;
        }
        exit;
    }
}

$result = $conn->query("SELECT * FROM users ORDER BY id DESC");
$adminCount = $result->num_rows;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        :root {
            --primary-color: #3B1E54;
            --secondary-color: #7F40B6;
            --accent-color: #9B6B9E;
            --light-color: #F5F5F5;
        }

        body {
            background-color: var(--light-color);
        }

        /* Gaya Navbar */
        .navbar {
            background-color: #3B1E54;
            padding: 10px 0;
            position: sticky;
            top: 0;
            z-index: 1000; /* Pastikan navbar berada di atas konten lainnya */
            width: 100%; /* Pastikan navbar memiliki lebar penuh */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Opsional: menambahkan bayangan */
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
            /* Memungkinkan penambahan garis bawah */
        }

        .navbar-nav .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 5px;
        }

        /* Menambahkan garis bawah pada link yang aktif */
        .navbar-nav .nav-item.active .nav-link {
            border-bottom: 2px solid white;
            /* Menambahkan garis bawah */
        }

        .navbar-toggler {
            border-color: white;
        }

        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml;charset=UTF8,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='%23fff' viewBox='0 0 30 30'%3E%3Cpath stroke='rgba(255, 255, 255, 1)' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3E%3C/svg%3E");
        }

        .content-container {
            padding-top: 80px;
            padding-bottom: 60px;
        }

        .stats-card {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .admin-table {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .table thead th {
            background-color: var(--primary-color);
            color: white;
            border: none;
        }

        .btn-action {
            margin: 2px;
            padding: 5px 10px;
            border-radius: 5px;
        }

        .search-box {
            position: relative;
            margin-bottom: 20px;
        }

        .search-box input {
            padding-left: 35px;
            border-radius: 20px;
        }

        .search-box i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
        }

        .add-admin-btn {
            background-color: var(--secondary-color);
            color: white;
            border-radius: 20px;
            padding: 8px 20px;
            margin-bottom: 20px;
        }

        .modal-content {
            border-radius: 15px;
        }

        .modal-header {
            background-color: var(--primary-color);
            color: white;
            border-radius: 15px 15px 0 0;
        }

        .close {
            color: white;
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

        footer {
            background-color: var(--primary-color);
            padding: 15px 0;
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
                        <a class="nav-link" href="laporan_surat_masuk.php">Laporan Surat Masuk</a>
                    </li>
                    <li class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'laporan_surat_keluar.php') ? 'active' : ''; ?>">
                        <a class="nav-link" href="laporan_surat_keluar.php">Laporan Surat Keluar</a>
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

    <div class="content-container">
        <div class="container">
            <!-- Stats Card -->
            <div class="row">
                <div class="col-md-4">
                    <div class="stats-card">
                        <h4>Total Admin</h4>
                        <h2><?= $adminCount ?></h2>
                        <p>Aktif dalam sistem</p>
                    </div>
                </div>
            </div>

            <!-- Search and Add Button -->
            <div class="d-flex justify-content-between align-items-center">
                <div class="search-box">
                    <i class="bi bi-search"></i>
                    <input type="text" id="searchInput" class="form-control" placeholder="Cari admin...">
                </div>
                <button class="btn add-admin-btn" data-toggle="modal" data-target="#addModal">
                    <i class="bi bi-plus-circle"></i> Tambah Admin
                </button>
            </div>

            <!-- Admin Table -->
            <div class="admin-table">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Password</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()) {
                            $decryptedPassword = customDecrypt($row['password']); // Dekripsi password
                        ?>
                            <tr>
                                <td><?= $row['id'] ?></td>
                                <td><?= $row['username'] ?></td>
                                <td><?= $decryptedPassword ?></td>
                                <td>
                                    <span class="badge badge-success">Aktif</span>
                                </td>
                                <td>
                                    <button class="btn btn-warning btn-action edit-btn"
                                        data-id="<?= $row['id'] ?>"
                                        data-username="<?= $row['username'] ?>"
                                        data-password="<?= $decryptedPassword ?>">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-danger btn-action delete-btn" data-id="<?= $row['id'] ?>">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Admin Modal -->
    <div class="modal fade" id="addModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="addForm">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Admin Baru</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" class="form-control" name="username" required>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" name="password" required>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary toggle-password" type="button">
                                        <i class="bi bi-eye-fill"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Tambah</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Edit Admin</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="edit-id" name="id">
                        <div class="form-group">
                            <label for="edit-username">Username</label>
                            <input type="text" class="form-control" id="edit-username" name="username" required>
                        </div>
                        <div class="form-group">
                            <label for="edit-password">Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="edit-password" name="password">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary toggle-password" type="button">
                                        <i class="bi bi-eye-fill"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <footer class="text-center text-white">
        Copyright © 2025 Kelompok 1 Penyuratan 4KA26
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            // Search functionality
            $("#searchInput").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                $("table tbody tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });

            // Add admin
            $('#addForm').submit(function(e) {
                e.preventDefault();
                const formData = $(this).serialize() + "&action=add";
                $.post('', formData, function(response) {
                    alert(response);
                    location.reload();
                });
            });

            // Buka modal edit
            $(document).on('click', '.edit-btn', function() {
                const id = $(this).data('id');
                const username = $(this).data('username');
                const password = $(this).data('password');
                $('#edit-id').val(id);
                $('#edit-username').val(username);
                $('#edit-password').val(password);
                $('#editModal').modal('show');
            });

            // Proses update admin
            $('#editForm').submit(function(e) {
                e.preventDefault();
                const formData = $(this).serialize() + "&action=update";
                $.post('', formData, function(response) {
                    alert(response);
                    location.reload(); // Refresh halaman setelah update
                });
            });

            // Hapus admin
            $(document).on('click', '.delete-btn', function() {
                if (confirm('Apakah Anda yakin ingin menghapus admin ini?')) {
                    const id = $(this).data('id');
                    $.post('', {
                        id: id,
                        action: 'delete'
                    }, function(response) {
                        alert(response);
                        location.reload(); // Refresh halaman setelah delete
                    });
                }
            });

            // Password toggle functionality for both modals
            $('.toggle-password').click(function() {
                const passwordField = $(this).closest('.input-group').find('input');
                const type = passwordField.attr('type') === 'password' ? 'text' : 'password';
                passwordField.attr('type', type);
                $(this).html(type === 'password' ?
                    '<i class="bi bi-eye-fill"></i>' :
                    '<i class="bi bi-eye-slash-fill"></i>');
            });
        });
    </script>
</body>

</html>