<?php
include __DIR__ . '/../config.php';

// Mulai session untuk mengambil nilai session
session_start();

// Ambil daftar pelanggan dari database
$query_pelanggan = "SELECT id_pelanggan, nama_pelanggan FROM pelanggan";
$result_pelanggan = $conn->query($query_pelanggan);

// Ambil daftar layanan dari database
$query_layanan = "SELECT id_layanan, layanan FROM layanan";
$result_layanan = $conn->query($query_layanan);

// Ambil kode admin dari session atau login
$kode_admin = isset($_SESSION['admin_code']) ? $_SESSION['admin_code'] : '';

// Insert new invoice
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tanggal_masuk = date('Y-m-d');
    $id_pelanggan = isset($_POST['id_pelanggan']) ? $_POST['id_pelanggan'] : '';
    $layanan_data = isset($_POST['layanan_data']) ? $_POST['layanan_data'] : [];
    $kode_jenis_pembayaran = isset($_POST['kode_jenis_pembayaran']) ? $_POST['kode_jenis_pembayaran'] : '';
    $kode_admin = isset($_POST['kode_admin']) ? $_POST['kode_admin'] : '';
    $id_pengerjaan = 2; // 2 untuk belum selesai

    // Generate kode invoice otomatis
    $query_generate = "SELECT CONCAT('A', LPAD(IFNULL(SUBSTRING(MAX(kode_invoice), 2) + 1, 1), 3, '0')) AS new_kode FROM invoice";
    $result_generate = $conn->query($query_generate);
    $row_generate = $result_generate->fetch_assoc();
    $kode_invoice = $row_generate['new_kode'];

    // Insert data into invoice table for each layanan
    foreach ($layanan_data as $layanan) {
        $id_layanan = $layanan['id_layanan'];
        $jumlah = $layanan['jumlah'];
        
        if (!empty($id_pelanggan) && !empty($jumlah) && !empty($id_layanan) && !empty($kode_jenis_pembayaran) && !empty($kode_admin)) {
            $insert_query = "INSERT INTO invoice (kode_invoice, tanggal_masuk, id_pelanggan, jumlah, id_layanan, kode_jenis_pembayaran, kode_admin, id_pengerjaan)
                            VALUES ('$kode_invoice', '$tanggal_masuk', '$id_pelanggan', '$jumlah', '$id_layanan', '$kode_jenis_pembayaran', '$kode_admin', '$id_pengerjaan')";

            if ($conn->query($insert_query) !== TRUE) {
                echo "Error: " . $insert_query . "<br>" . $conn->error;
            }
        } else {
            echo "Semua field harus diisi.";
        }
    }

    echo '<script>alert("Invoice berhasil ditambahkan.");</script>';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Invoice Baru</title>
    <style>
        .layanan-item {
            background-color: #f0f0f0;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
        }

        .layanan-item:nth-child(even) {
            background-color: #e0e0e0;
        }
    </style>
</head>
<body>
    <h1>Tambah Invoice Baru</h1>
    <form id="invoice-form" method="post" action="">
        <!-- Tanggal masuk secara default hari ini -->
        <input type="hidden" name="tanggal_masuk" value="<?php echo date('Y-m-d'); ?>">

        <label for="id_pelanggan">Pelanggan:</label>
        <select name="id_pelanggan" id="id_pelanggan">
            <?php while ($row = $result_pelanggan->fetch_assoc()): ?>
                <option value="<?php echo $row['id_pelanggan']; ?>"><?php echo $row['nama_pelanggan']; ?></option>
            <?php endwhile; ?>
        </select><br><br>

        <div id="layanan-container">
            <div class="layanan-item">
                <label for="id_layanan">Layanan:</label>
                <select name="layanan_data[0][id_layanan]" required>
                    <?php 
                    $result_layanan = $conn->query($query_layanan);
                    while ($row = $result_layanan->fetch_assoc()): ?>
                        <option value="<?php echo $row['id_layanan']; ?>"><?php echo $row['layanan']; ?></option>
                    <?php endwhile; ?>
                </select><br><br>

                <label for="jumlah">Jumlah:</label>
                <input type="text" name="layanan_data[0][jumlah]" required><br><br>
            </div>
        </div>

        <button type="button" onclick="addLayanan()">Tambah Layanan</button><br><br>

        <label for="kode_jenis_pembayaran">Jenis Pembayaran:</label>
        <select name="kode_jenis_pembayaran" id="kode_jenis_pembayaran">
            <option value="T01">Tunai</option>
            <option value="T02">Transfer Bank</option>
        </select><br><br>

        <input type="hidden" name="kode_admin" value="<?php echo $kode_admin; ?>">

        <input type="submit" value="Simpan">
    </form>

    <script>
    let layananIndex = 1;

    function addLayanan() {
        const layananContainer = document.getElementById('layanan-container');
        const newLayanan = document.createElement('div');
        newLayanan.classList.add('layanan-item');

        newLayanan.innerHTML = `
            <label for="id_layanan">Layanan:</label>
            <select name="layanan_data[${layananIndex}][id_layanan]" required>
                <?php 
                $result_layanan = $conn->query($query_layanan);
                while ($row = $result_layanan->fetch_assoc()): ?>
                    <option value="<?php echo $row['id_layanan']; ?>"><?php echo $row['layanan']; ?></option>
                <?php endwhile; ?>
            </select><br><br>

            <label for="jumlah">Jumlah:</label>
            <input type="text" name="layanan_data[${layananIndex}][jumlah]" required><br><br>
        `;

        layananContainer.appendChild(newLayanan);
        layananIndex++;
    }

    document.getElementById('invoice-form').addEventListener('submit', function(event) {
        event.preventDefault(); // Prevent form submission
        const formData = new FormData(this);
        fetch('', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            if (data.includes('Invoice berhasil ditambahkan')) {
                alert('Invoice berhasil ditambahkan.');
            } else {
                alert('Terjadi kesalahan saat menambahkan invoice.');
            }
        })
        .catch(error => {
            console.log('Error:', error);
            alert('Terjadi kesalahan saat menambahkan invoice.');
        });
    });
    </script>
</body>
</html>
