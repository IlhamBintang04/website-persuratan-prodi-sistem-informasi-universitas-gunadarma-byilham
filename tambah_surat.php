<?php
session_start();
include 'config/database.php';

if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nomor_surat = $_POST['nomor_surat'];
    $pengirim_surat = $_POST['pengirim_surat'];
    $waktu_surat = $_POST['waktu_surat'];
    $lampiran_surat = $_POST['lampiran_surat'];
    $perihal_surat = $_POST['perihal_surat'];
    $penerima_surat = $_POST['penerima_surat'];
    $isi_surat = $_POST['isi_surat'];
    $unit_penerbit = $_POST['unit_penerbit'];
    $tempat_surat = $_POST['tempat_surat'];
    $nama_mengesahkan = $_POST['nama_mengesahkan'];
    $nama_tembusan = $_POST['nama_tembusan'];
    $tipe_surat = $_POST['tipe_surat'];

    $query = "SELECT * FROM surat WHERE nomor_surat = '$nomor_surat'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        echo "Nomor surat sudah ada, harap masukkan nomor surat yang berbeda.";
    } else {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["file_scan"]["name"]);
        $uploadOk = 1;
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
                $sql = "INSERT INTO surat (nomor_surat, pengirim_surat, waktu_surat, lampiran_surat, perihal_surat, penerima_surat, isi_surat, unit_penerbit, tempat_surat, nama_mengesahkan, nama_tembusan, tipe_surat, file_scan) 
                        VALUES ('$nomor_surat', '$pengirim_surat', '$waktu_surat', '$lampiran_surat', '$perihal_surat', '$penerima_surat', '$isi_surat', '$unit_penerbit', '$tempat_surat', '$nama_mengesahkan', '$nama_tembusan', '$tipe_surat', '$file_name')";

                if ($conn->query($sql) === TRUE) {
                    header('Location: dashboard.php');
                } else {
                    echo "Error: " . $sql . "<br>" . $conn->error;
                }
            } else {
                echo "Maaf, terjadi kesalahan saat mengupload file.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Tambah Surat</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <?php include 'includes/footer.php'; ?>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <h2>Tambah Surat</h2>
            </div>
            <div class="card-body">
                <form action="" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="tipe_surat" value="<?php echo $_GET['tipe']; ?>">
                    <div class="form-group">
                        <label for="nomor_surat">Nomor Surat</label>
                        <input type="text" class="form-control" id="nomor_surat" name="nomor_surat" required>
                    </div>
                    <div class="form-group">
                        <label for="pengirim_surat">Pengirim Surat</label>
                        <input type="text" class="form-control" id="pengirim_surat" name="pengirim_surat" required>
                    </div>
                    <div class="form-group">
                        <label for="waktu_surat">Waktu Surat</label>
                        <input type="datetime-local" class="form-control" id="waktu_surat" name="waktu_surat" required>
                    </div>
                    <div class="form-group">
                        <label for="lampiran_surat">Lampiran Surat</label>
                        <input type="text" class="form-control" id="lampiran_surat" name="lampiran_surat">
                    </div>
                    <div class="form-group">
                        <label for="perihal_surat">Perihal Surat</label>
                        <input type="text" class="form-control" id="perihal_surat" name="perihal_surat" required>
                    </div>
                    <div class="form-group">
                        <label for="penerima_surat">Penerima Surat</label>
                        <input type="text" class="form-control" id="penerima_surat" name="penerima_surat" required>
                    </div>
                    <div class="form-group">
                        <label for="isi_surat">Isi Surat</label>
                        <textarea class="form-control" id="isi_surat" name="isi_surat" rows="5" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="unit_penerbit">Unit Penerbit</label>
                        <select class="form-control" id="unit_penerbit" name="unit_penerbit" required>
                            <option value="Institusi">Institusi</option>
                            <option value="Jurusan Teknik Sipil">Jurusan Teknik Sipil</option>
                            <option value="Jurusan Teknik Mesin">Jurusan Teknik Mesin</option>
                            <option value="Jurusan Teknik Elektro">Jurusan Teknik Elektro</option>
                            <option value="Jurusan Akuntansi">Jurusan Akuntansi</option>
                            <option value="Jurusan Administrasi Bisnis">Jurusan Administrasi Bisnis</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="tempat_surat">Tempat Surat</label>
                        <input type="text" class="form-control" id="tempat_surat" name="tempat_surat" required>
                    </div>
                    <div class="form-group">
                        <label for="nama_mengesahkan">Nama yang Mengesahkan</label>
                        <select class="form-control" id="nama_mengesahkan" name="nama_mengesahkan" required>
                            <option value="Direktur">Direktur</option>
                            <option value="Ketua Jurusan Teknik Sipil">Ketua Jurusan Teknik Sipil</option>
                            <option value="Ketua Jurusan Teknik Mesin">Ketua Jurusan Teknik Mesin</option>
                            <option value="Ketua Jurusan Teknik Elektro">Ketua Jurusan Teknik Elektro</option>
                            <option value="Ketua Jurusan Akuntansi">Ketua Jurusan Akuntansi</option>
                            <option value="Ketua Jurusan Administrasi Bisnis">Ketua Jurusan Administrasi Bisnis</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="nama_tembusan">Nama Tembusan</label>
                        <input type="text" class="form-control" id="nama_tembusan" name="nama_tembusan">
                    </div>
                    <div class="mb-3">
                        <label for="file_scan" class="font-weight-bold">File Scan</label>
                        <input type="file" id="file_scan" name="file_scan" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>