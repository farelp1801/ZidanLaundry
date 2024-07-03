<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Zidan Laundry</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" type="image/png" href="ZidanLaundry.png">
    <script src="js/jquery.min.js"></script>
    <script src="js/script.js"></script>
    <script>
        $(document).ready(function() {
            // Fungsi untuk memperbarui konten saat tab diubah
            $('.tab-link').on('click', function() {
                var tab = $(this).data('tab');
                $('.tab-content').removeClass('active');
                $('#' + tab).addClass('active');
                if (tab === 'dashboard') {
                    $('#dashboard-content').load('pages/dashboard.php');
                } else if (tab === 'add-invoice') {
                    $('#add-invoice-content').load('pages/add_invoice.php');
                } else if (tab === 'list-invoice') {
                    $('#list-invoice-content').load('pages/list_invoice.php');
                } else if (tab === 'add-service') {
                    $('#add-service-content').load('pages/add_service.php');
                } else if (tab === 'list-customers') {
                    $('#list-customers-content').load('pages/pelanggan.php');
                } else if (tab === 'list-admins') {
                    $('#list-admins-content').load('pages/daftar_admin.php');
                }
            });

            // Contoh pembaruan secara otomatis setelah menambahkan invoice
            $('#form-add-invoice').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    type: 'POST',
                    url: 'pages/add_invoice.php',
                    data: $(this).serialize(),
                    success: function(response) {
                        $('#add-invoice-content').load('pages/add_invoice.php');
                    }
                });
            });

            // Contoh pembaruan secara otomatis setelah menambahkan layanan
            $('#form-add-service').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    type: 'POST',
                    url: 'pages/add_service.php',
                    data: $(this).serialize(),
                    success: function(response) {
                        $('#add-service-content').load('pages/add_service.php');
                    }
                });
            });

            // Fungsi untuk memperbarui waktu secara live
            function updateTime() {
                var now = new Date();
                var days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                var months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                var day = days[now.getDay()];
                var date = now.getDate();
                var month = months[now.getMonth()];
                var year = now.getFullYear();
                var hours = now.getHours();
                var minutes = now.getMinutes();
                var seconds = now.getSeconds();

                // Tambahkan leading zero ke menit dan detik jika kurang dari 10
                if (minutes < 10) {
                    minutes = '0' + minutes;
                }
                if (seconds < 10) {
                    seconds = '0' + seconds;
                }

                // Format waktu dan tanggal
                var timeString = day + ', ' + date + ' ' + month + ' ' + year + ' ' + hours + ':' + minutes + ':' + seconds;
                $('#current-time').text(timeString);

                // Memperbarui setiap detik
                setTimeout(updateTime, 1000);
            }

            // Panggil fungsi untuk pertama kali
            updateTime();
        });
    </script>
    <style>
        .header {
            width: 95%;
            display: flex;
            align-items: left;
            justify-content: space-between;
            padding: 10px;
            background-color: #f1f1f1;
            position: relative;
        }
        .header p {
            margin: 0;
            line-height: 1.5; /* Tinggi baris untuk menjaga jarak */
        }
        .header .login-info {
            margin-right: 10px; /* Jarak antara login info dan waktu */
        }
        .header a {
            text-decoration: none;
            color: #007BFF;
            font-weight: bold;
        }
    </style>
</head>
<body>
<div class="header">
    <div class="login-info">
        <?php if (isset($_SESSION['username'])): ?>
            <p>Anda login sebagai <?= $_SESSION['username']; ?> (<?= $_SESSION['admin_code']; ?>)</p>
        <?php endif; ?>
    </div>
    <div>
        <p id="current-time"></p>
        <?php if (isset($_SESSION['username'])): ?>
            <a href="logout.php">Logout</a>
        <?php endif; ?>
    </div>
</div>

<h1>Selamat Datang di Zidan Laundry</h1>
<nav>
    <ul class="tabs">
        <li class="tab-link current" data-tab="dashboard">Dashboard</li>
        <li class="tab-link" data-tab="add-invoice">Tambah Pesanan</li>
        <li class="tab-link" data-tab="list-invoice">Riwayat Pesanan</li>
        <li class="tab-link" data-tab="add-service">Daftar Layanan</li>
        <li class="tab-link" data-tab="list-customers">Daftar Pelanggan</li>
        <li class="tab-link" data-tab="list-admins">Daftar Admin</li>
    </ul>
</nav>

<div id="dashboard" class="tab-content active">
    <div id="dashboard-content">
        <?php include 'pages/dashboard.php'; ?>
    </div>
</div>
<div id="add-invoice" class="tab-content">
    <div id="add-invoice-content">
        <?php include 'pages/add_invoice.php'; ?>
    </div>
</div>
<div id="list-invoice" class="tab-content">
    <div id="list-invoice-content">
        <?php include 'pages/list_invoice.php'; ?>
    </div>
</div>
<div id="add-service" class="tab-content">
    <div id="add-service-content">
        <?php include 'pages/add_service.php'; ?>
    </div>
</div>
<div id="list-customers" class="tab-content">
    <div id="list-customers-content">
        <?php include 'pages/pelanggan.php'; ?>
    </div>
</div>
<div id="list-admins" class="tab-content">
    <div id="list-admins-content">
        <?php include 'pages/daftar_admin.php'; ?>
    </div>
</div>

</body>
</html>
