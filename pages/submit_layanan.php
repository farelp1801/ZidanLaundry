<?php
include __DIR__ . '/../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $detail_layanan = isset($_POST['detail_layanan']) ? $_POST['detail_layanan'] : '';
    $harga_layanan = isset($_POST['harga_layanan']) ? $_POST['harga_layanan'] : '';

    if (!empty($detail_layanan) && !empty($harga_layanan)) {
        $insert_layanan = "INSERT INTO layanan (layanan, harga) VALUES ('$detail_layanan', '$harga_layanan')";
        if ($conn->query($insert_layanan) === TRUE) {
            echo json_encode(array('status' => 'success', 'message' => 'Layanan berhasil ditambahkan.'));
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'Error: ' . $insert_layanan . "<br>" . $conn->error));
        }
    } else {
        echo json_encode(array('status' => 'error', 'message' => 'Detail layanan dan harga tidak boleh kosong.'));
    }
}
?>
