<?php
include __DIR__ . '/../config.php';

// Function untuk menghasilkan ID layanan baru
function generateNewServiceID() {
    global $conn;

    // Query untuk mendapatkan ID layanan terakhir
    $query_last_id = "SELECT id_layanan FROM layanan ORDER BY id_layanan DESC LIMIT 1";
    $result = $conn->query($query_last_id);

    if ($result->num_rows > 0) {
        $last_id = $result->fetch_assoc()['id_layanan'];
        // Extract nomor urut dari ID terakhir
        preg_match('/(\d+)$/', $last_id, $matches);
        $next_number = intval($matches[0]) + 1;
        // Formatkan ID baru
        $new_id = 'L' . sprintf('%03d', $next_number);
    } else {
        // Jika tidak ada data, mulai dari L001
        $new_id = 'L001';
    }

    return $new_id;
}

// Tambah layanan baru ke dalam tabel layanan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_new_service'])) {
    $id_layanan = isset($_POST['id_layanan']) ? $_POST['id_layanan'] : generateNewServiceID();
    $layanan = isset($_POST['layanan']) ? $_POST['layanan'] : '';
    $harga = isset($_POST['harga']) ? $_POST['harga'] : '';

    // Validasi input kosong
    if (empty($layanan) || empty($harga)) {
        echo "Semua field harus diisi.";
    } else {
        // Periksa duplikasi id_layanan
        $check_duplicate_query = "SELECT id_layanan FROM layanan WHERE id_layanan = '$id_layanan'";
        $result_duplicate = $conn->query($check_duplicate_query);

        if ($result_duplicate->num_rows > 0) {
            echo "ID Layanan sudah ada dalam database.";
        } else {
            // Insert ke database
            $sql = "INSERT INTO layanan (id_layanan, layanan, harga) VALUES ('$id_layanan', '$layanan', '$harga')";

            if ($conn->query($sql) === TRUE) {
                echo "Layanan berhasil ditambahkan";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        }
    }
}

// Hapus layanan dari tabel layanan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hapus_service'])) {
    $id_layanan = $_POST['id_layanan'];

    $sql = "DELETE FROM layanan WHERE id_layanan='$id_layanan'";

    if ($conn->query($sql) === TRUE) {
        echo "Layanan berhasil dihapus";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Edit layanan di tabel layanan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_service'])) {
    $id_layanan = $_POST['id_layanan'];
    $layanan = isset($_POST['layanan']) ? $_POST['layanan'] : '';
    $harga = isset($_POST['harga']) ? $_POST['harga'] : '';

    // Validasi input kosong
    if (empty($layanan) || empty($harga)) {
        echo "Semua field harus diisi.";
    } else {
        // Update layanan
        $sql = "UPDATE layanan SET layanan='$layanan', harga='$harga' WHERE id_layanan='$id_layanan'";

        if ($conn->query($sql) === TRUE) {
            echo "Layanan berhasil diubah";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

// Ambil data layanan dari database
$result = $conn->query("SELECT * FROM layanan");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tambah dan Kelola Layanan</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        .edit-form {
            display: none;
            border: 1px solid #ccc;
            padding: 10px;
            margin-top: 10px;
        }
        .edit-form input[type="text"], .edit-form input[type="number"] {
            width: 100%;
            margin-bottom: 10px;
        }
        .edit-btns {
            display: flex;
        }
        .edit-btns button {
            margin-right: 10px;
        }
        .btn-simpan {
            background-color: #4CAF50;
            border: none;
            color: white;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
            border-radius: 5px;
        }
        .btn-simpan:hover {
            background-color: #45a049;
        }
        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            z-index: 999;
        }
        .popup {
            display: none;
            position: fixed;
            background-color: white;
            padding: 20px;
            border: 1px solid #ccc;
            width: 400px;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            z-index: 1000;
            text-align: center;
        }
        .popup h2 {
            margin-top: 0;
        }
        .popup button {
            background-color: #4CAF50;
            border: none;
            color: white;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin-top: 10px;
            cursor: pointer;
            border-radius: 5px;
        }
        .popup button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <h1>Daftar Layanan</h1>
    <button onclick="tampilkanFormTambahLayanan()">Tambah Layanan Baru</button>
    <table id="layanan-table">
        <tr>
            <th>ID Layanan</th>
            <th>Layanan</th>
            <th>Harga</th>
            <th>Aksi</th>
        </tr>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr id="row-<?php echo $row['id_layanan']; ?>">
            <td><?php echo $row['id_layanan']; ?></td>
            <td><?php echo $row['layanan']; ?></td>
            <td><?php echo $row['harga']; ?></td>
            <td class="edit-btns">
                <button onclick="editLayanan('<?php echo $row['id_layanan']; ?>')">Edit</button>
                <button onclick="hapusLayanan('<?php echo $row['id_layanan']; ?>')">Hapus</button>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    <div id="form-tambah-layanan" class="popup">
        <h2>Tambah Layanan Baru</h2>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <input type="text" id="id_layanan" name="id_layanan" placeholder="ID Layanan" value="<?php echo generateNewServiceID(); ?>" required><br>
            <input type="text" id="layanan" name="layanan" placeholder="Layanan" required><br>
            <input type="number" id="harga" name="harga" placeholder="Harga" required><br>
            <input type="submit" name="add_new_service" value="Simpan" class="btn-simpan">
        </form>
    </div>

    <div id="edit-popup" class="popup">
        <h2>Edit Layanan</h2>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <input type="hidden" id="edit-id-layanan" name="id_layanan">
            <input type="text" id="edit-layanan" name="layanan" required><br>
            <input type="number" id="edit-harga" name="harga" required><br>
            <div class="edit-btns">
                <button type="submit" name="edit_service" class="btn-simpan">Simpan</button>
                <button type="button" onclick="tutupEditForm()" class="btn-simpan">Batal</button>
            </div>
        </form>
    </div>

    <div id="overlay" class="overlay"></div>

    <script>
        function tampilkanFormTambahLayanan() {
            document.getElementById('form-tambah-layanan').style.display = 'block';
            document.getElementById('overlay').style.display = 'block';
        }

        function editLayanan(id) {
            var layanan = document.getElementById('row-' + id).cells[1].innerHTML;
            var harga = document.getElementById('row-' + id).cells[2].innerHTML;

            document.getElementById('edit-id-layanan').value = id;
            document.getElementById('edit-layanan').value = layanan;
            document.getElementById('edit-harga').value = harga;

            document.getElementById('edit-popup').style.display = 'block';
            document.getElementById('overlay').style.display = 'block';
        }

        function tutupEditForm() {
            document.getElementById('edit-popup').style.display = 'none';
            document.getElementById('overlay').style.display = 'none';
        }

        function hapusLayanan(id) {
            if (confirm('Anda yakin ingin menghapus layanan ini?')) {
                var xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        document.getElementById('row-' + id).remove();
                        alert(this.responseText);
                    }
                };
                xhttp.open('POST', '<?php echo $_SERVER['PHP_SELF']; ?>', true);
                xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                xhttp.send('id_layanan=' + id + '&hapus_service=1');
            }
        }

        document.getElementById('overlay').addEventListener('click', function() {
            document.getElementById('form-tambah-layanan').style.display = 'none';
            document.getElementById('edit-popup').style.display = 'none';
            this.style.display = 'none';
        });
    </script>
</body>
</html>
