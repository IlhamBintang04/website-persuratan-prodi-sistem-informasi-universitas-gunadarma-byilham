<?php
session_start();
include 'config/database.php';

if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}

// Fetch all surat_keluar data
$sql = "SELECT * FROM surat_keluar";
$result = $conn->query($sql);

$id = $_GET['id']; // Ambil ID dari URL
$sql = "SELECT * FROM surat_keluar WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();


// Cek apakah 'waktu_surat' ada dan tidak null
if (!empty($row['waktu_surat'])) {
    $tanggalSurat = date('d', strtotime($row['waktu_surat']));
    $bulanSurat = date('F', strtotime($row['waktu_surat']));
    $tahunSurat = date('Y', strtotime($row['waktu_surat']));
} else {
    $tanggalSurat = '';
    $bulanSurat = '';
    $tahunSurat = '';
}



?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Surat</title>
</head>

<body>
    <style>
        .container {
            width: 21cm;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
        }

        .header {
            display: flex;
            align-items: flex-start;
            border-bottom: 3px solid #000;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .logo {
            width: 150px;
            /* Increased from 120px */
            flex-shrink: 0;
            margin-right: 20px;
            /* Increased from 15px */
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logo img {
            margin-top: 25px;
            width: 150px;
            /* Increased from 135px */
            height: auto;
            max-height: 150px;
            object-fit: contain;
        }

        .header-text {
            flex-grow: 1;
            padding-left: 10px;
        }

        .header-text h1 {
            font-size: 26px;
            /* Increased from 24px */
            margin: 0;
            text-transform: uppercase;
            font-weight: bold;
            text-decoration: none;
            margin-bottom: 8px;
            text-align: left;
        }

        .header-text p {
            margin: 3px 0;
            font-size: 12px;
            /* Adjusted from 14px for better fit */
            line-height: 1.4;
        }



        .sk-text {
            text-align: center;
            font-weight: bold;
            margin: 5px 0;
            font-size: 13px;
        }

        .faculty-text {
            text-align: justify;
            margin-top: 8px;
            padding-right: 10px;
        }

        .faculty-text strong {
            font-weight: bold;
        }

        .content {
            margin-top: 20px;
        }

        /* Previous styles remain the same */
        .letter-info {
            position: relative;
            width: 100%;
        }

        .letter-info-top {
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .letter-details {
            flex: 1;
        }

        .letter-date {
            text-align: right;
            width: 200px;
        }

        .letter-info p {
            margin: 5px 0;
            line-height: 1.4;
        }

        .letter-recipient {
            margin-top: 80px;
            clear: both;
        }

        .signature {
            margin-top: 30px;
            text-align: right;
        }

        .tembusan {
            margin-top: 20px;
        }

        .print-button {
            text-align: center;
            margin-top: 20px;
        }

        .print-button button {
            background-color:rgb(63, 0, 163);
            /* Warna biru */
            color: white;
            /* Teks putih */
            border: none;
            /* Hilangkan border */
            border-radius: 5px;
            /* Sudut membulat */
            padding: 10px 15px;
            /* Padding atas-bawah dan kanan-kiri */
            font-size: 14px;
            /* Ukuran teks */
            cursor: pointer;
            /* Menunjukkan bahwa tombol bisa diklik */
            transition: background-color 0.3s ease;
            /* Animasi saat hover */
        }

        @media print {
            .print-button {
                display: none !important;
            }
        }
    </style>
    </head>

    <body>
        <div class="container">
            <div class="header">
                <div class="logo">
                    <img src="./img/images.png" alt="Logo Universitas Gunadarma">
                </div>
                <div class="header-text">
                    <h1 style="margin-left: 10px;">UNIVERSITAS GUNADARMA</h1>
                    <p style="margin-left: 10px;"><strong>SK No. 92 / DIKTI/ Kep / 1996</strong></p>
                    <p style="margin-left: 10px;">
                        Fakultas Ilmu Komputer, Teknologi Industri, Ekonomi, Teknik Sipil & Perencanaan, Psikologi, Sastra<br>
                        <strong>Program Diploma (D3):</strong> Manajemen Informatika, Teknik Komputer, Akuntansi Komputer, Manajemen Keuangan dan Pemasaran<br>
                        <strong>Program Sarjana (S1):</strong> Sistem Informasi, Sistem Komputer, Teknik Informatika, Teknik Elektro, Teknik Mesin, Teknik Industri, Akuntansi, Manajemen, Arsitektur, Teknik Sipil, Psikologi, Sastra Inggris<br>
                        <strong>Program Magister (S2):</strong> Sistem Informasi, Manajemen, Teknik Elektro, Sastra Inggris, Psikologi, Teknik Sipil<br>
                        <strong>Program Doktor (S3):</strong> Ilmu Ekonomi, Teknologi Informasi / Ilmu Komputer
                    </p>


                </div>
            </div>

            <div class="content">
                <div class="letter-info">
                    <div class="letter-info-top">
                        <div class="letter-details">
                            <p>No. : <?php echo isset($row['nomor_surat']) ? $row['nomor_surat'] : '-'; ?></p>
                            <p>Hal : <?php echo isset($row['perihal_surat']) ? $row['perihal_surat'] : '-'; ?></p>
                            <p>Lamp. : <?php echo isset($row['lampiran_surat']) ? $row['lampiran_surat'] : '-'; ?></p>
                        </div>
                        <div class="letter-date">
                            <p><?php echo $row['tempat_surat']; ?>, <?php echo date('d F Y', strtotime($row['waktu_surat'])); ?></p>
                        </div>
                    </div>

                    <div class="letter-recipient">
                        <p>Kepada Yth,</p>
                        <p><?php echo $row['penerima_surat']; ?></p>
                        <p><?php echo $row['jabatan_penerima']; ?></p>
                        <p>di Tempat</p>
                    </div>
                </div>

                <div style="margin-top: 30px;">
                    <p>Dengan hormat,</p>
                    <p style="text-align: justify;"><?php echo nl2br($row['isi_surat']); ?></p>
                </div>

                <div class="signature">
                    <p style="margin-bottom: 5px;">Mengetahui,</p>
                    <?php if (!empty($row['file_scan'])): ?>
                        <img src="uploads/<?php echo $row['file_scan']; ?>" alt="Tanda Tangan" style="max-height: 100px; margin: 5px 0;">
                    <?php endif; ?>
                    <p style="margin: 5px 0;"><strong><?php echo $row['nama_mengesahkan']; ?></strong></p>
                    <p style="margin-top: 0;"><strong>Prodi Sistem Informasi</strong></p>
                </div>

                <?php if (!empty($row['nama_tembusan'])): ?>
                    <div class="tembusan">
                        <p><strong>Tembusan:</strong></p>
                        <ol>
                            <?php
                            $tembusanList = explode(',', $row['nama_tembusan']);
                            foreach ($tembusanList as $tembusan) {
                                echo '<li>' . trim($tembusan) . '</li>';
                            }
                            ?>
                        </ol>
                    </div>
                <?php endif; ?>

                <div class="print-button">
                    <button class="btn btn-primary" onclick="window.print()">Cetak Surat</button>
                </div>
            </div>
        </div>
        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    </body>

</html>