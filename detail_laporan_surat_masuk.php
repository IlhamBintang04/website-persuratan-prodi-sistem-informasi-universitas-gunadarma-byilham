<?php
session_start();
include 'config/database.php';

if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}

// Fetch all surat_masuk data
$sql = "SELECT * FROM surat_masuk";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Surat Masuk</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            line-height: 1.6;
            background-color: #f8f9fa;
            color: #343a40;
        }

        .header {
            border-bottom: 3px solid #343a40;
            padding-bottom: 15px;
            margin-bottom: 30px;
        }

        .header .logo img {
            max-width: 135px;
        }

        .header .text {
            text-align: left;
        }

        .header .text h1,
        .header .text h2,
        .header .text p {
            margin: 0;
        }

        .content {
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .content p {
            margin-bottom: 10px;
        }

        .signature {
            margin-top: 30px;
            text-align: right;
        }

        .tembusan {
            margin-top: 20px;
        }

        .tembusan ol {
            padding-left: 20px;
        }

        .print-button {
            margin-top: 20px;
            display: block;
            text-align: center;
        }

        table {
            margin-top: 30px;
            width: 100%;
            border-collapse: collapse;
        }

        table,
        th,
        td {
            border: 1px solid #343a40;
        }

        th,
        td {
            padding: 10px;
            text-align: left;
        }

        @media print {
            .print-button {
                display: none;
            }

            .container {
                box-shadow: none;
            }
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
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="row align-items-center">
                <div class="col-2 text-center logo">
                    <img src="./img/images.png" alt="Logo Universitas Gunadarma">
                </div>
                <div class="col-10 text">
                    <h1>Sistem Informasi Pengelolaan Surat Masuk dan Keluar Prodi Sistem Informasi</h1>
                </div>
            </div>
        </div>

        <div class="content">
            <h2>Laporan Surat Masuk</h2>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nomor Surat</th>
                        <th>Pengirim</th>
                        <th>Perihal</th>
                        <th>Waktu Surat</th>
                        <th>Tempat Surat</th>
                        <th>File Lampiran</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo $row['nomor_surat']; ?></td>
                                <td><?php echo $row['pengirim_surat']; ?></td>
                                <td><?php echo $row['perihal_surat']; ?></td>
                                <td><?php echo date('d-m-Y H:i', strtotime($row['waktu_surat'])); ?></td>
                                <td><?php echo $row['tempat_surat']; ?></td>
                                <td><?php echo $row['file_scan']; ?></td>
                            </tr>
                        <?php }
                    } else { ?>
                        <tr>
                            <td colspan="7" class="text-center">No data available</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <div class="print-button">
            <button class="btn btn-primary" onclick="window.print()">Cetak Laporan</button>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>