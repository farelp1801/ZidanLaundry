<?php
include __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_layanan = $_POST['id_layanan'];

    $sql = "DELETE FROM layanan WHERE id_layanan='$id_layanan'";

    if ($conn->query($sql) === TRUE) {
        echo "Layanan berhasil dihapus";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    header('Location: add_service.php');
}
?>
