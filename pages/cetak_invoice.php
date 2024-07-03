<?php
include __DIR__ . '/../config.php';

if (!isset($_GET['kode_invoice'])) {
    die('Kode invoice tidak ditemukan.');
}

$kode_invoice = $_GET['kode_invoice'];
$query_invoice = "
SELECT 
    invoice.kode_invoice, 
    invoice.tanggal_masuk, 
    invoice.tanggal_keluar, 
    pelanggan.nama_pelanggan, 
    pelanggan.kontak_pelanggan, 
    invoice.jumlah, 
    layanan.layanan AS nama_layanan, 
    layanan.harga AS harga_layanan, 
    jenis_pembayaran.jenis_pembayaran, 
    pengerjaan.status_pengerjaan 
FROM invoice 
JOIN pelanggan ON invoice.id_pelanggan = pelanggan.id_pelanggan 
JOIN jenis_pembayaran ON invoice.kode_jenis_pembayaran = jenis_pembayaran.kode_jenis_pembayaran
JOIN layanan ON invoice.id_layanan = layanan.id_layanan
JOIN pengerjaan ON invoice.id_pengerjaan = pengerjaan.id_pengerjaan
WHERE invoice.kode_invoice = '$kode_invoice'";

$result_invoice = $conn->query($query_invoice);
$invoice_data = $result_invoice->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Invoice</title>
    <link rel="icon" type="image/png" href="ZidanLaundry.png">
    <style>
        body {
            background-color: #f2f2f2;
            font-family: Arial, sans-serif;
        }
        .container {
            width: 70%;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
        }
        .header img {
            width: 100px;
        }
        .header h1 {
            margin: 10px 0;
            font-weight: bold;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .contact-buttons {
            text-align: center;
            margin: 20px 0;
        }
        .contact-buttons a {
            margin: 0 10px;
            text-decoration: none;
            color: #fff;
            padding: 10px 20px;
            border-radius: 5px;
        }
        .contact-buttons a.telp { background-color: #28a745; }
        .contact-buttons a.wa { background-color: #25d366; }
        .contact-buttons a.ig { background-color: #c13584; }
        .details, .invoice-table {
            margin: 20px 0;
        }
        .details table, .invoice-table table {
            width: 100%;
            border-collapse: collapse;
        }
        .details td, .invoice-table th, .invoice-table td {
            padding: 10px;
            border: 1px solid #ddd;
        }
        .invoice-table th {
            background-color: #f8f8f8;
        }
        .status-box {
            text-align: center;
            margin: 20px 0;
        }
        .status-box .status {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 50px;
            color: #fff;
            background-color: #28a745;
        }
        .action-buttons {
            text-align: center;
            margin-top: 20px;
        }
        .action-buttons a {
            margin: 0 10px;
            text-decoration: none;
            color: #fff;
            padding: 10px 20px;
            border-radius: 5px;
        }
        .action-buttons a.cetak { background-color: #007bff; }
        .action-buttons a.bagikan { background-color: #ffc107; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="ZidanLaundry.png" alt="Logo Zidan Laundry">
            <h1>Zidan Laundry</h1>
            <p>Laundry_Kampus Unand</p>
            <p> </p>
            </p>Kiloan & Satuan 🧺</p>
            </p>Pakaian, Sepatu, Tas, Selimut, Bedcover, Handuk dll</p>
            </p>Melayani Antar Jemput 🛵</p>
            </p>Dijamin Bersih, Wangi dan Rapi✨</p>
</p> </p>
</p>Jl. Dr. Moh. Hatta l, Kampus Unand, depan Mesjid As-syuhada-, Padang, Indonesia 25176</p>
        </div>
        <div class="contact-buttons">
            <a href="tel:+6281277658151" class="telp">Telepon</a>
            <a href="https://wa.me/6281277658151" target="_blank" class="wa">WhatsApp</a>
            <a href="https://instagram.com/Zidan_Laundry" target="_blank" class="ig">Instagram</a>
        </div>
        <div class="details">
            <table>
                <tr>
                    <td>Nama: <?php echo $invoice_data['nama_pelanggan']; ?></td>
                    <td>No. HP: <?php echo $invoice_data['kontak_pelanggan']; ?></td>
                </tr>
                <tr>
                    <td>Tanggal Masuk: <?php echo $invoice_data['tanggal_masuk']; ?></td>
                    <td>Tanggal Keluar: <?php echo $invoice_data['tanggal_keluar']; ?></td>
                </tr>
                <tr>
                    <td colspan="2">Kode Invoice: <?php echo $invoice_data['kode_invoice']; ?></td>
                </tr>
            </table>
        </div>
        <div class="invoice-table">
            <table>
                <tr>
                    <th>No</th>
                    <th>Pelayanan</th>
                    <th>Jumlah/Berat Cucian</th>
                    <th>Harga sat/KG</th>
                    <th>Jumlah (Rp)</th>
                </tr>
                <?php
                $result_services = $conn->query($query_invoice);
                $no = 1;
                $total_harga = 0;
                while ($row = $result_services->fetch_assoc()) {
                    $jumlah_harga = $row['jumlah'] * $row['harga_layanan'];
                    $total_harga += $jumlah_harga;
                    echo "<tr>";
                    echo "<td>" . $no++ . "</td>";
                    echo "<td>" . $row['nama_layanan'] . "</td>";
                    echo "<td>" . $row['jumlah'] . "</td>";
                    echo "<td>" . $row['harga_layanan'] . "</td>";
                    echo "<td>" . $jumlah_harga . "</td>";
                    echo "</tr>";
                }
                ?>
                <tr>
                    <td colspan="4" style="text-align: right;">Jumlah</td>
                    <td><?php echo $total_harga; ?></td>
                </tr>
            </table>
        </div>
        <div class="action-buttons">
            <a href="javascript:void(0)" onclick="window.print();" class="cetak">Cetak</a>
            <a href="#" onclick="copyToClipboard('<?php echo '' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>'); alert('Link invoice telah disalin ke clipboard.');" class="bagikan">Bagikan</a>
        </div>
    </div>

    <script>
        function copyToClipboard(text) {
            var dummy = document.createElement("textarea");
            document.body.appendChild(dummy);
            dummy.value = text;
            dummy.select();
            document.execCommand("copy");
            document.body.removeChild(dummy);
        }
    </script>
</body>
</html>
