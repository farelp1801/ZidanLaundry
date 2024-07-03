<?php
include __DIR__ . '/../config.php';

session_start();


// Handle AJAX requests for adding, editing, and deleting admins
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    switch ($action) {
        case 'add_new_admin':
            // Generate unique admin code
            $result = $conn->query("SELECT MAX(kode_admin) AS max_code FROM admin");
            $row = $result->fetch_assoc();
            $next_code = $row['max_code'] ? 'C' . sprintf('%02d', intval(substr($row['max_code'], 1)) + 1) : 'C01';

            // Prepare data
            $nama_admin = $_POST['nama_admin'];

            // Insert new admin
            $sql = "INSERT INTO admin (kode_admin, admin) VALUES ('$next_code', '$nama_admin')";
            if ($conn->query($sql) === TRUE) {
                echo json_encode(['success' => true, 'message' => 'Admin baru berhasil ditambahkan', 'kode_admin' => $next_code]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error: ' . $conn->error]);
            }
            break;
        case 'hapus_admin':
            $kode_admin = $_POST['kode_admin'];
            $sql = "DELETE FROM admin WHERE kode_admin='$kode_admin'";
            if ($conn->query($sql) === TRUE) {
                echo json_encode(['success' => true, 'message' => 'Admin berhasil dihapus']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error: ' . $conn->error]);
            }
            break;
        case 'edit_nama_admin':
            $kode_admin = $_POST['kode_admin'];
            $nama_admin = $_POST['nama_admin'];
            $sql = "UPDATE admin SET admin='$nama_admin' WHERE kode_admin='$kode_admin'";
            if ($conn->query($sql) === TRUE) {
                echo json_encode(['success' => true, 'message' => 'Nama admin berhasil diupdate']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error: ' . $conn->error]);
            }
            break;
    }
    exit;
}

// Fetch admin data from database
$result = $conn->query("SELECT * FROM admin");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Admin</title>
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
        function tambahAdmin() {
            var nama_admin = prompt('Masukkan nama admin baru:');
            if (nama_admin !== null) {
                var xhr = new XMLHttpRequest();
                xhr.open('POST', '<?php echo $_SERVER['PHP_SELF']; ?>', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        var response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            var newRow = '<tr id="row-' + response.kode_admin + '">' +
                                '<td>' + response.kode_admin + '</td>' +
                                '<td onclick="editNamaAdmin(\'' + response.kode_admin + '\', this)">' + nama_admin + '</td>' +
                                '<td><button onclick="hapusAdmin(\'' + response.kode_admin + '\')">Hapus</button></td>' +
                                '</tr>';
                            document.getElementById('admin-table').innerHTML += newRow;
                            alert(response.message);
                        } else {
                            alert('Error: ' + response.message);
                        }
                    }
                };
                var formData = 'action=add_new_admin&nama_admin=' + encodeURIComponent(nama_admin);
                xhr.send(formData);
            }
        }

        function editNamaAdmin(kode_admin, element) {
            var nama_admin = prompt('Masukkan nama admin baru:', element.innerText.trim());
            if (nama_admin !== null) {
                var xhr = new XMLHttpRequest();
                xhr.open('POST', '<?php echo $_SERVER['PHP_SELF']; ?>', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        var response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            element.innerText = nama_admin;
                            alert(response.message);
                        } else {
                            alert('Error: ' + response.message);
                        }
                    }
                };
                var formData = 'action=edit_nama_admin&kode_admin=' + kode_admin + '&nama_admin=' + encodeURIComponent(nama_admin);
                xhr.send(formData);
            }
        }

        function hapusAdmin(kode_admin) {
            if (confirm('Apakah Anda yakin ingin menghapus admin ini?')) {
                var xhr = new XMLHttpRequest();
                xhr.open('POST', '<?php echo $_SERVER['PHP_SELF']; ?>', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        var response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            var row = document.getElementById('row-' + kode_admin);
                            row.parentNode.removeChild(row);
                            alert(response.message);
                        } else {
                            alert('Error: ' + response.message);
                        }
                    }
                };
                var formData = 'action=hapus_admin&kode_admin=' + kode_admin;
                xhr.send(formData);
            }
        }
    </script>
</head>
<body>
    <h1>Daftar Admin</h1>
    <button onclick="tambahAdmin()">Tambah Admin</button>
    <table id="admin-table">
        <tr>
            <th>Kode Admin</th>
            <th>Nama Admin</th>
            <th>Aksi</th>
        </tr>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr id="row-<?php echo $row['kode_admin']; ?>">
            <td><?php echo $row['kode_admin']; ?></td>
            <td onclick="editNamaAdmin('<?php echo $row['kode_admin']; ?>', this)">
                <?php echo $row['admin']; if ($row['kode_admin'] === $_SESSION['admin_code']) echo " (anda)"; ?>
            </td>
            <td>
                <button onclick="hapusAdmin('<?php echo $row['kode_admin']; ?>')">Hapus</button>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
