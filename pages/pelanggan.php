<?php
include __DIR__ . '/../config.php';

// Handle AJAX requests for adding, editing, and deleting customers
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    switch ($action) {
        case 'add_new_customer':
            // Generate unique ID for new customer
            $result = $conn->query("SELECT MAX(id_pelanggan) AS max_id FROM pelanggan");
            $row = $result->fetch_assoc();
            $next_id = $row['max_id'] ? 'S' . sprintf('%03d', intval(substr($row['max_id'], 1)) + 1) : 'S001';

            // Prepare data
            $nama_pelanggan = $_POST['nama_pelanggan'];
            $kontak_pelanggan = $_POST['kontak_pelanggan'];
            
            // Insert new customer
            $sql = "INSERT INTO pelanggan (id_pelanggan, nama_pelanggan, kontak_pelanggan) VALUES ('$next_id', '$nama_pelanggan', '$kontak_pelanggan')";
            if ($conn->query($sql) === TRUE) {
                echo json_encode(['success' => true, 'message' => 'Pelanggan baru berhasil ditambahkan', 'id_pelanggan' => $next_id]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error: ' . $conn->error]);
            }
            break;
        case 'hapus_customer':
            $id_pelanggan = $_POST['id_pelanggan'];
            $sql = "DELETE FROM pelanggan WHERE id_pelanggan='$id_pelanggan'";
            if ($conn->query($sql) === TRUE) {
                echo json_encode(['success' => true, 'message' => 'Pelanggan berhasil dihapus']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error: ' . $conn->error]);
            }
            break;
        case 'edit_nama_pelanggan':
            $id_pelanggan = $_POST['id_pelanggan'];
            $nama_pelanggan = $_POST['nama_pelanggan'];
            $sql = "UPDATE pelanggan SET nama_pelanggan='$nama_pelanggan' WHERE id_pelanggan='$id_pelanggan'";
            if ($conn->query($sql) === TRUE) {
                echo json_encode(['success' => true, 'message' => 'Nama pelanggan berhasil diupdate']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error: ' . $conn->error]);
            }
            break;
        case 'edit_kontak_pelanggan':
            $id_pelanggan = $_POST['id_pelanggan'];
            $kontak_pelanggan = $_POST['kontak_pelanggan'];
            $sql = "UPDATE pelanggan SET kontak_pelanggan='$kontak_pelanggan' WHERE id_pelanggan='$id_pelanggan'";
            if ($conn->query($sql) === TRUE) {
                echo json_encode(['success' => true, 'message' => 'Kontak pelanggan berhasil diupdate']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error: ' . $conn->error]);
            }
            break;
    }
    exit;
}

// Fetch customer data from database
$result = $conn->query("SELECT * FROM pelanggan");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Daftar Pelanggan</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        /* CSS styles (existing styles) */
        .popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ccc;
            z-index: 1000;
        }
    </style>
    <script>
        function tambahPelanggan() {
            var nama_pelanggan = prompt('Masukkan nama pelanggan baru:');
            var kontak_pelanggan = prompt('Masukkan kontak pelanggan baru:');
            if (nama_pelanggan !== null && kontak_pelanggan !== null) {
                var xhr = new XMLHttpRequest();
                xhr.open('POST', '<?php echo $_SERVER['PHP_SELF']; ?>', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        var response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            var newRow = '<tr id="row-' + response.id_pelanggan + '">' +
                                '<td>' + response.id_pelanggan + '</td>' +
                                '<td onclick="editNamaPelanggan(\'' + response.id_pelanggan + '\', this)">' + nama_pelanggan + '</td>' +
                                '<td onclick="editKontakPelanggan(\'' + response.id_pelanggan + '\', this)">' + kontak_pelanggan + '</td>' +
                                '<td><button onclick="hapusPelanggan(\'' + response.id_pelanggan + '\')">Hapus</button></td>' +
                                '</tr>';
                            document.getElementById('pelanggan-table').innerHTML += newRow;
                            alert(response.message);
                        } else {
                            alert('Error: ' + response.message);
                        }
                    }
                };
                var formData = 'action=add_new_customer&nama_pelanggan=' + encodeURIComponent(nama_pelanggan) + '&kontak_pelanggan=' + encodeURIComponent(kontak_pelanggan);
                xhr.send(formData);
            }
        }

        function editNamaPelanggan(id_pelanggan, element) {
            var nama_pelanggan = prompt('Masukkan nama pelanggan baru:', element.innerText.trim());
            if (nama_pelanggan !== null) {
                var xhr = new XMLHttpRequest();
                xhr.open('POST', '<?php echo $_SERVER['PHP_SELF']; ?>', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        var response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            element.innerText = nama_pelanggan;
                            alert(response.message);
                        } else {
                            alert('Error: ' + response.message);
                        }
                    }
                };
                var formData = 'action=edit_nama_pelanggan&id_pelanggan=' + id_pelanggan + '&nama_pelanggan=' + encodeURIComponent(nama_pelanggan);
                xhr.send(formData);
            }
        }

        function editKontakPelanggan(id_pelanggan, element) {
            var kontak_pelanggan = prompt('Masukkan kontak pelanggan baru:', element.innerText.trim());
            if (kontak_pelanggan !== null) {
                var xhr = new XMLHttpRequest();
                xhr.open('POST', '<?php echo $_SERVER['PHP_SELF']; ?>', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        var response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            element.innerText = kontak_pelanggan;
                            alert(response.message);
                        } else {
                            alert('Error: ' + response.message);
                        }
                    }
                };
                var formData = 'action=edit_kontak_pelanggan&id_pelanggan=' + id_pelanggan + '&kontak_pelanggan=' + encodeURIComponent(kontak_pelanggan);
                xhr.send(formData);
            }
        }

        function hapusPelanggan(id_pelanggan) {
            if (confirm('Apakah Anda yakin ingin menghapus pelanggan ini?')) {
                var xhr = new XMLHttpRequest();
                xhr.open('POST', '<?php echo $_SERVER['PHP_SELF']; ?>', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        var response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            var row = document.getElementById('row-' + id_pelanggan);
                            row.parentNode.removeChild(row);
                            alert(response.message);
                        } else {
                            alert('Error: ' + response.message);
                        }
                    }
                };
                var formData = 'action=hapus_customer&id_pelanggan=' + id_pelanggan;
                xhr.send(formData);
            }
        }
    </script>
</head>
<body>
    <h1>Daftar Pelanggan</h1>
    <button onclick="tambahPelanggan()">Tambah Pelanggan</button>
    <table id="pelanggan-table">
        <tr>
            <th>ID Pelanggan</th>
            <th>Nama Pelanggan</th>
            <th>Kontak Pelanggan</th>
            <th>Aksi</th>
        </tr>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr id="row-<?php echo $row['id_pelanggan']; ?>">
            <td><?php echo $row['id_pelanggan']; ?></td>
            <td onclick="editNamaPelanggan('<?php echo $row['id_pelanggan']; ?>', this)"><?php echo $row['nama_pelanggan']; ?></td>
            <td onclick="editKontakPelanggan('<?php echo $row['id_pelanggan']; ?>', this)"><?php echo $row['kontak_pelanggan']; ?></td>
            <td>
                <button onclick="hapusPelanggan('<?php echo $row['id_pelanggan']; ?>')">Hapus</button>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
