<?php
session_start();
include 'config/database.php';

if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}

$id = $_GET['id'];
$sql = "SELECT * FROM surat WHERE id='$id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
} else {
    echo "Surat tidak ditemukan.";
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

    $query = "SELECT * FROM surat WHERE nomor_surat = '$nomor_surat' AND id != '$id'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        echo "Nomor surat sudah ada, harap masukkan nomor surat yang berbeda.";
    } else {
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
            $sql = "UPDATE surat SET nomor_surat='$nomor_surat', pengirim_surat='$pengirim_surat', waktu_surat='$waktu_surat', lampiran_surat='$lampiran_surat', perihal_surat='$perihal_surat', penerima_surat='$penerima_surat', isi_surat='$isi_surat', unit_penerbit='$unit_penerbit', tempat_surat='$tempat_surat', nama_mengesahkan='$nama_mengesahkan', nama_tembusan='$nama_tembusan', file_scan='$file_name' WHERE id='$id'";

            if ($conn->query($sql) === TRUE) {
                header('Location: surat_keluar.php');
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Edit Surat</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <?php include 'includes/footer.php'; ?>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <h2>Edit Surat</h2>
            </div>
            <div class="card-body">
                <form method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="nomor_surat">Nomor Surat</label>
                        <input type="text" class="form-control" id="nomor_surat" name="nomor_surat" value="<?php echo $row['nomor_surat']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="pengirim_surat">Pengirim Surat</label>
                        <input type="text" class="form-control" id="pengirim_surat" name="pengirim_surat" value="<?php echo $row['pengirim_surat']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="waktu_surat">Waktu Surat</label>
                        <input type="datetime-local" class="form-control" id="waktu_surat" name="waktu_surat" value="<?php echo date('Y-m-d\TH:i', strtotime($row['waktu_surat'])); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="lampiran_surat">Lampiran Surat</label>
                        <input type="text" class="form-control" id="lampiran_surat" name="lampiran_surat" value="<?php echo $row['lampiran_surat']; ?>">
                    </div>
                    <div class="form-group">
                        <label for="perihal_surat">Perihal Surat</label>
                        <input type="text" class="form-control" id="perihal_surat" name="perihal_surat" value="<?php echo $row['perihal_surat']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="penerima_surat">Penerima Surat</label>
                        <input type="text" class="form-control" id="penerima_surat" name="penerima_surat" value="<?php echo $row['penerima_surat']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="isi_surat">Isi Surat</label>
                        <textarea class="form-control" id="isi_surat" name="isi_surat" rows="5" required><?php echo $row['isi_surat']; ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="unit_penerbit">Unit Penerbit</label>
                        <select class="form-control" id="unit_penerbit" name="unit_penerbit" required>
                            <option value="Institusi" <?php if ($row['unit_penerbit'] == 'Institusi') echo 'selected'; ?>>Institusi</option>
                            <option value="Jurusan Teknik Sipil" <?php if ($row['unit_penerbit'] == 'Jurusan Teknik Sipil') echo 'selected'; ?>>Jurusan Teknik Sipil</option>
                            <option value="Jurusan Teknik Mesin" <?php if ($row['unit_penerbit'] == 'Jurusan Teknik Mesin') echo 'selected'; ?>>Jurusan Teknik Mesin</option>
                            <option value="Jurusan Teknik Elektro" <?php if ($row['unit_penerbit'] == 'Jurusan Teknik Elektro') echo 'selected'; ?>>Jurusan Teknik Elektro</option>
                            <option value="Jurusan Akuntansi" <?php if ($row['unit_penerbit'] == 'Jurusan Akuntansi') echo 'selected'; ?>>Jurusan Akuntansi</option>
                            <option value="Jurusan Administrasi Bisnis" <?php if ($row['unit_penerbit'] == 'Jurusan Administrasi Bisnis') echo 'selected'; ?>>Jurusan Administrasi Bisnis</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="tempat_surat">Tempat Surat</label>
                        <input type="text" class="form-control" id="tempat_surat" name="tempat_surat" value="<?php echo $row['tempat_surat']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="nama_mengesahkan">Nama yang Mengesahkan</label>
                        <select class="form-control" id="nama_mengesahkan" name="nama_mengesahkan" required>
                            <option value="Direktur" <?php if ($row['nama_mengesahkan'] == 'Direktur') echo 'selected'; ?>>Direktur</option>
                            <option value="Ketua Jurusan Teknik Sipil" <?php if ($row['nama_mengesahkan'] == 'Ketua Jurusan Teknik Sipil') echo 'selected'; ?>>Ketua Jurusan Teknik Sipil</option>
                            <option value="Ketua Jurusan Teknik Mesin" <?php if ($row['nama_mengesahkan'] == 'Ketua Jurusan Teknik Mesin') echo 'selected'; ?>>Ketua Jurusan Teknik Mesin</option>
                            <option value="Ketua Jurusan Teknik Elektro" <?php if ($row['nama_mengesahkan'] == 'Ketua Jurusan Teknik Elektro') echo 'selected'; ?>>Ketua Jurusan Teknik Elektro</option>
                            <option value="Ketua Jurusan Akuntansi" <?php if ($row['nama_mengesahkan'] == 'Ketua Jurusan Akuntansi') echo 'selected'; ?>>Ketua Jurusan Akuntansi</option>
                            <option value="Ketua Jurusan Administrasi Bisnis" <?php if ($row['nama_mengesahkan'] == 'Ketua Jurusan Administrasi Bisnis') echo 'selected'; ?>>Ketua Jurusan Administrasi Bisnis</option>
                        </select>
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
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>