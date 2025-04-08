<?php
session_start();
include 'config/database.php';

if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}

$id = $_GET['id'];
$sql = "DELETE FROM surat WHERE id='$id'";

if ($conn->query($sql) === TRUE) {
    header('Location: surat_masuk.php');
} else {
    echo "Error deleting record: " . $conn->error;
}
