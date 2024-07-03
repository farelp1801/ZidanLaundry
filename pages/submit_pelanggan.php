<?php
include __DIR__ . '/../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_pelanggan = isset($_POST['nama_pelanggan']) ? $_POST['nama_pelanggan'] : '';

    if (!empty($nama_pelanggan)) {
        $insert_pelanggan = "INSERT INTO pelanggan (nama_pelanggan) VALUES ('$nama_pelanggan')";
        if ($conn->query($insert_pelanggan) === TRUE) {
            echo json_encode(array('status' => 'success', 'message' => 'Pelanggan berhasil ditambahkan.'));
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'Error: ' . $insert_pelanggan . "<br>" . $conn->error));
        }
    } else {
        echo json_encode(array('status' => 'error', 'message' => 'Nama pelanggan tidak boleh kosong.'));
    }
}
?>
