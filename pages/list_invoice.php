<?php
include __DIR__ . '/../config.php';

// Ambil semua data invoice dengan join tabel pelanggan, layanan, dan jenis_pembayaran
$query_invoices = "
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
ORDER BY invoice.kode_invoice, invoice.id";
$result_invoices = $conn->query($query_invoices);

// Update status pengerjaan
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {
    $kode_invoice = $_POST['kode_invoice'];
    $status_pengerjaan = $_POST['status_pengerjaan'];

    $update_query = "UPDATE invoice SET id_pengerjaan='$status_pengerjaan' WHERE kode_invoice='$kode_invoice'";
    if ($conn->query($update_query) === TRUE) {
        echo json_encode(array('status' => 'success', 'message' => 'Status pengerjaan berhasil diubah.'));
        exit; // Menghentikan eksekusi PHP setelah mengirim respons Ajax
    } else {
        echo json_encode(array('status' => 'error', 'message' => 'Error updating record: ' . $conn->error));
        exit; // Menghentikan eksekusi PHP setelah mengirim respons Ajax
    }
}

// Update tanggal masuk dan keluar
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_dates'])) {
    $kode_invoice = $_POST['kode_invoice'];
    $tanggal_masuk = $_POST['tanggal_masuk'];
    $tanggal_keluar = $_POST['tanggal_keluar'];

    $update_query = "UPDATE invoice SET tanggal_masuk='$tanggal_masuk', tanggal_keluar='$tanggal_keluar' WHERE kode_invoice='$kode_invoice'";
    if ($conn->query($update_query) === TRUE) {
        echo json_encode(array('status' => 'success', 'message' => 'Tanggal masuk dan keluar berhasil diubah.'));
        exit; // Menghentikan eksekusi PHP setelah mengirim respons Ajax
    } else {
        echo json_encode(array('status' => 'error', 'message' => 'Error updating dates: ' . $conn->error));
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

<h1>Daftar Invoice</h1>
<table border="1">
    <tr>
        <th>Kode Invoice</th>
        <th>Tanggal Masuk</th>
        <th>Tanggal Keluar</th>
        <th>Nama Pelanggan</th>
        <th>Kontak Pelanggan</th>
        <th>Nama Layanan</th>
        <th>Harga Layanan</th>
        <th>Jumlah</th>
        <th>Jenis Pembayaran</th>
        <th>Status Pengerjaan</th>
        <th>Total Harga</th>
        <th>Aksi</th>
    </tr>
    <?php
    $current_invoice = null;
    $current_rowspan = 0;
    while ($row = $result_invoices->fetch_assoc()):
        if ($current_invoice !== $row['kode_invoice']):
            if ($current_invoice !== null):
                // Tutup tag <tr> untuk invoice sebelumnya
                echo "</tr>";
            endif;
            $current_invoice = $row['kode_invoice'];
            $current_rowspan = $conn->query("SELECT COUNT(*) AS count FROM invoice WHERE kode_invoice='$current_invoice'")->fetch_assoc()['count'];
    ?>
        <tr id="invoice_<?php echo $row['kode_invoice']; ?>">
            <td rowspan="<?php echo $current_rowspan; ?>"><?php echo $row['kode_invoice']; ?></td>
            <td rowspan="<?php echo $current_rowspan; ?>"><?php echo $row['tanggal_masuk']; ?></td>
            <td rowspan="<?php echo $current_rowspan; ?>"><?php echo $row['tanggal_keluar']; ?></td>
            <td rowspan="<?php echo $current_rowspan; ?>"><?php echo $row['nama_pelanggan']; ?></td>
            <td rowspan="<?php echo $current_rowspan; ?>"><?php echo $row['kontak_pelanggan']; ?></td>
            <td><?php echo $row['nama_layanan']; ?></td>
            <td><?php echo $row['harga_layanan']; ?></td>
            <td><?php echo $row['jumlah']; ?></td>
            <td rowspan="<?php echo $current_rowspan; ?>"><?php echo $row['jenis_pembayaran']; ?></td>
            <td rowspan="<?php echo $current_rowspan; ?>"><?php echo $row['status_pengerjaan']; ?></td>
            <td rowspan="<?php echo $current_rowspan; ?>">
                <?php
                $total_query = $conn->query("SELECT SUM(jumlah * layanan.harga) AS total_harga FROM invoice JOIN layanan ON invoice.id_layanan = layanan.id_layanan WHERE kode_invoice='$current_invoice'");
                $total_harga = $total_query->fetch_assoc()['total_harga'];
                echo $total_harga;
                ?>
            </td>
            <td rowspan="<?php echo $current_rowspan; ?>">
                <div class="action-buttons">
                    <button class="btn-update-status" data-invoice="<?php echo $row['kode_invoice']; ?>">Ubah Status</button>
                    <div class="status-options" style="display: none;">
                        <select name="status_pengerjaan">
                            <option value="1">Selesai</option>
                            <option value="2">Belum Selesai</option>
                        </select>
                        <button class="btn-submit-status">Simpan</button>
                    </div>
                    <button class="btn-update-dates" data-invoice="<?php echo $row['kode_invoice']; ?>">Atur Tanggal</button>
                    <div class="date-options" style="display: none;">
                        <label for="tanggal_masuk">Tanggal Masuk:</label>
                        <input type="date" id="tanggal_masuk" name="tanggal_masuk" value="<?php echo $row['tanggal_masuk']; ?>"><br>
                        <label for="tanggal_keluar">Tanggal Keluar:</label>
                        <input type="date" id="tanggal_keluar" name="tanggal_keluar" value="<?php echo $row['tanggal_keluar']; ?>"><br>
                        <button class="btn-submit-dates">Simpan</button>
                    </div>
                    <button class="btn-delete-invoice" data-invoice="<?php echo $row['kode_invoice']; ?>">Hapus</button>
                    <?php
// File: halaman_invoice.php

// Tambahkan tombol cetak invoice di kolom aksi
echo '<button class="btn-print-invoice" data-invoice="'.$row['kode_invoice'].'">Cetak</button>';
?>
<script>
$(document).ready(function() {
    // Event listener untuk tombol cetak invoice
    $('.btn-print-invoice').click(function() {
        var kode_invoice = $(this).data('invoice');
        window.open('pages/cetak_invoice.php?kode_invoice=' + kode_invoice, '_blank');
    });
});
</script>

                </div>
            </td>
        <?php else: ?>
            <tr>
                <td><?php echo $row['nama_layanan']; ?></td>
                <td><?php echo $row['harga_layanan']; ?></td>
                <td><?php echo $row['jumlah']; ?></td>
        <?php endif; ?>
    <?php endwhile; ?>
    </tr>
</table>

<script src="../js/jquery.min.js"></script>
<script>
$(document).ready(function() {
    // Ajax untuk mengubah status pengerjaan
    $('.btn-update-status').click(function() {
        var invoiceRow = $(this).closest('tr');
        var statusOptions = $(this).siblings('.status-options');
        statusOptions.slideToggle();

        $('.btn-submit-status').click(function() {
            var kode_invoice = invoiceRow.find('.btn-update-status').data('invoice');
            var status_pengerjaan = statusOptions.find('select[name="status_pengerjaan"]').val();
            $.ajax({
                type: 'POST',
                url: '<?php echo $_SERVER['PHP_SELF']; ?>',
                data: { kode_invoice: kode_invoice, status_pengerjaan: status_pengerjaan, update_status: true },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        alert(response.message);
                        invoiceRow.find('td:nth-child(10)').html(status_pengerjaan == 1 ? 'selesai' : 'belum selesai');
                        statusOptions.slideToggle();
                    } else {
                        alert(response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });
        });
    });

    // Ajax untuk mengubah tanggal masuk dan keluar
    $('.btn-update-dates').click(function() {
        var invoiceRow = $(this).closest('tr');
        var dateOptions = $(this).siblings('.date-options');
        dateOptions.slideToggle();

        $('.btn-submit-dates').click(function() {
            var kode_invoice = invoiceRow.find('.btn-update-dates').data('invoice');
            var tanggal_masuk = dateOptions.find('input[name="tanggal_masuk"]').val();
            var tanggal_keluar = dateOptions.find('input[name="tanggal_keluar"]').val();
            $.ajax({
                type: 'POST',
                url: '<?php echo $_SERVER['PHP_SELF']; ?>',
                data: { kode_invoice: kode_invoice, tanggal_masuk: tanggal_masuk, tanggal_keluar: tanggal_keluar, update_dates: true },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        alert(response.message);
                        invoiceRow.find('td:nth-child(2)').html(tanggal_masuk);
                        invoiceRow.find('td:nth-child(3)').html(tanggal_keluar);
                        dateOptions.slideToggle();
                    } else {
                        alert(response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });
        });
    });

    // Ajax untuk menghapus invoice
    $('.btn-delete-invoice').click(function() {
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
                        $('#invoice_' + kode_invoice).remove();
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
