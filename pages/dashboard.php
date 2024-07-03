<?php
include __DIR__ . '/../config.php';

// Query untuk mengambil data ringkasan
$total_invoices = $conn->query("SELECT COUNT(*) AS total FROM invoice")->fetch_assoc()['total'];
$total_customers = $conn->query("SELECT COUNT(*) AS total FROM pelanggan")->fetch_assoc()['total'];
$total_services = $conn->query("SELECT COUNT(*) AS total FROM layanan")->fetch_assoc()['total'];

// Query untuk menghitung jumlah pesanan yang belum selesai
$total_pending_orders = $conn->query("SELECT COUNT(*) AS total FROM invoice JOIN pengerjaan ON invoice.id_pengerjaan = pengerjaan.id_pengerjaan WHERE pengerjaan.status_pengerjaan = 'belum selesai'")->fetch_assoc()['total'];

// Query untuk mendapatkan data pesanan yang belum selesai
$query_pending_orders = "
SELECT 
    invoice.kode_invoice, 
    invoice.tanggal_masuk, 
    pelanggan.nama_pelanggan, 
    layanan.layanan AS nama_layanan, 
    invoice.jumlah, 
    jenis_pembayaran.jenis_pembayaran, 
    pengerjaan.status_pengerjaan,
    admin.admin AS nama_admin
FROM invoice 
JOIN pelanggan ON invoice.id_pelanggan = pelanggan.id_pelanggan 
JOIN jenis_pembayaran ON invoice.kode_jenis_pembayaran = jenis_pembayaran.kode_jenis_pembayaran
JOIN layanan ON invoice.id_layanan = layanan.id_layanan
JOIN pengerjaan ON invoice.id_pengerjaan = pengerjaan.id_pengerjaan
JOIN admin ON invoice.kode_admin = admin.kode_admin
WHERE pengerjaan.status_pengerjaan = 'belum selesai'
ORDER BY invoice.kode_invoice";
$result_pending_orders = $conn->query($query_pending_orders);

// Query untuk menghitung jumlah pendapatan berdasarkan pesanan yang sudah selesai
$query_total_revenue = "
SELECT SUM(invoice.jumlah * layanan.harga) AS total
FROM invoice
JOIN layanan ON invoice.id_layanan = layanan.id_layanan
JOIN pengerjaan ON invoice.id_pengerjaan = pengerjaan.id_pengerjaan
WHERE pengerjaan.status_pengerjaan = 'selesai'
";
$total_revenue = $conn->query($query_total_revenue)->fetch_assoc()['total'];

// Query untuk menghitung jumlah pendapatan dalam 7 hari terakhir berdasarkan tanggal masuk
$date_seven_days_ago = date('Y-m-d', strtotime('-7 days'));
$query_revenue_last_7_days = "
SELECT SUM(invoice.jumlah * layanan.harga) AS total
FROM invoice
JOIN layanan ON invoice.id_layanan = layanan.id_layanan
JOIN pengerjaan ON invoice.id_pengerjaan = pengerjaan.id_pengerjaan
WHERE pengerjaan.status_pengerjaan = 'selesai'
AND invoice.tanggal_masuk >= '$date_seven_days_ago'
";
$total_revenue_last_7_days = $conn->query($query_revenue_last_7_days)->fetch_assoc()['total'];

// Update status pengerjaan dan tanggal keluar
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {
    $kode_invoice = $_POST['kode_invoice'];

    // Set status pengerjaan menjadi 'selesai' dan tanggal keluar menjadi hari ini
    $update_query = "UPDATE invoice SET id_pengerjaan='1', tanggal_keluar=CURDATE() WHERE kode_invoice='$kode_invoice'";
    if ($conn->query($update_query) === TRUE) {
        echo json_encode(array('status' => 'success', 'message' => 'Status pengerjaan berhasil diubah.', 'status_pengerjaan' => 'selesai'));
        exit; // Menghentikan eksekusi PHP setelah mengirim respons Ajax
    } else {
        echo json_encode(array('status' => 'error', 'message' => 'Error updating record: ' . $conn->error));
        exit; // Menghentikan eksekusi PHP setelah mengirim respons Ajax
    }
}

// Hapus invoice
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['hapus_invoice'])) {
    $kode_invoice = $_POST['kode_invoice'];

    $delete_query = "DELETE FROM invoice WHERE kode_invoice='$kode_invoice'";
    if ($conn->query($delete_query) === TRUE) {
        echo json_encode(array('status' => 'success', 'message' => 'Invoice berhasil dihapus.'));
        exit; // Menghentikan eksekusi PHP setelah mengirim respons Ajax
    } else {
        echo json_encode(array('status' => 'error', 'message' => 'Error deleting invoice: ' . $conn->error));
        exit; // Menghentikan eksekusi PHP setelah mengirim respons Ajax
    }
}
?>

<h1>Dashboard</h1>
<p>Total Pesanan: <?php echo $total_invoices; ?></p>
<p>Total Pelanggan: <?php echo $total_customers; ?></p>
<p>Total Layanan: <?php echo $total_services; ?></p>
<p>Total Pendapatan (Pesanan Selesai): Rp <?php echo number_format($total_revenue, 2); ?></p>
<p>Total Pendapatan (7 Hari Terakhir): Rp <?php echo number_format($total_revenue_last_7_days, 2); ?></p>
<p>Total Pesanan Belum Selesai: <?php echo $total_pending_orders; ?></p>

<h2>Pesanan yang Belum Selesai</h2>
<table border="1">
    <tr>
        <th>Kode Invoice</th>
        <th>Tanggal Masuk</th>
        <th>Nama Pelanggan</th>
        <th>Layanan</th>
        <th>Jumlah</th>
        <th>Jenis Pembayaran</th>
        <th>Status Pengerjaan</th>
        <th>Admin</th>
        <th>Aksi</th>
    </tr>
    <?php while ($row = $result_pending_orders->fetch_assoc()): ?>
    <tr>
        <td><?php echo $row['kode_invoice']; ?></td>
        <td><?php echo $row['tanggal_masuk']; ?></td>
        <td><?php echo $row['nama_pelanggan']; ?></td>
        <td><?php echo $row['nama_layanan']; ?></td>
        <td><?php echo $row['jumlah']; ?></td>
        <td><?php echo $row['jenis_pembayaran']; ?></td>
        <td id="status_<?php echo $row['kode_invoice']; ?>"><?php echo $row['status_pengerjaan']; ?></td>
        <td><?php echo $row['nama_admin']; ?></td>
        <td>
            <button class="btn-update-status" data-invoice="<?php echo $row['kode_invoice']; ?>">Ubah Status</button>
            <button class="btn-delete-invoice" data-invoice="<?php echo $row['kode_invoice']; ?>">Hapus</button>
            <a href="pages/cetak_invoice.php?kode_invoice=<?php echo $row['kode_invoice']; ?>" class="btn-cetak-invoice" target="_blank">Cetak Invoice</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

<!-- Modal untuk Ubah Status Pengerjaan -->
<div id="ubah-status-modal" style="display: none;">
    <h3>Ubah Status Pengerjaan</h3>
    <p>Apakah Anda yakin ingin menyelesaikan pesanan ini?</p>
    <button id="btn-ubah-status-confirm">Ya</button>
    <button id="btn-ubah-status-cancel">Batal</button>
</div>

<script src="../js/jquery.min.js"></script>
<script>
$(document).ready(function() {
    // Ajax untuk mengubah status pengerjaan
    $('.btn-update-status').click(function() {
        var kode_invoice = $(this).data('invoice');
        $('#ubah-status-modal').data('kode-invoice', kode_invoice).fadeIn();
    });

    // Konfirmasi ubah status pengerjaan
    $('#btn-ubah-status-confirm').click(function() {
        var kode_invoice = $('#ubah-status-modal').data('kode-invoice');
        $.ajax({
            type: 'POST',
            url: '<?php echo $_SERVER['PHP_SELF']; ?>',
            data: { kode_invoice: kode_invoice, update_status: true },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    alert(response.message);
                    $('#status_' + kode_invoice).html(response.status_pengerjaan);
                    $('#ubah-status-modal').fadeOut();
                } else {
                    alert(response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });
    });

    // Batal ubah status pengerjaan
    $('#btn-ubah-status-cancel').click(function() {
        $('#ubah-status-modal').fadeOut();
    });

    // Ajax untuk menghapus invoice
    $('.btn-delete-invoice').click(function() {
        var invoiceRow = $(this).closest('tr');
        var kode_invoice = $(this).data('invoice');
        if (confirm('Apakah Anda yakin ingin menghapus invoice ini?')) {
            $.ajax({
                type: 'POST',
                url: '<?php echo $_SERVER['PHP_SELF']; ?>',
                data: { kode_invoice: kode_invoice, hapus_invoice: true },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        alert(response.message);
                        invoiceRow.remove();
                    } else {
                        alert(response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });
        }
    });
});
</script>
