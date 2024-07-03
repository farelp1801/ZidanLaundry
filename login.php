<?php
session_start();
include 'config.php';

// Fetch usernames from the database
$query = "SELECT * FROM admin";
$result = mysqli_query($conn, $query);

$users = [];
while ($row = mysqli_fetch_assoc($result)) {
    $users[$row['kode_admin']] = $row['admin'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selected_user = $_POST['username'];
    
    if (isset($users[$selected_user])) {
        // Set session variables
        $_SESSION['username'] = $users[$selected_user];
        $_SESSION['admin_code'] = $selected_user;
        
        header("Location: index.php");
        exit();
    } else {
        echo "Invalid username";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="icon" href="ZidanLaundry.png" type="image/png">
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            background: url('ZidanBG.png') no-repeat center center fixed;
            background-size: cover;
            background-color: rgba(0, 0, 0, 0.8);
            background-blend-mode: darken;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-container {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            text-align: center;
            color: #000;
            width: 400px;
        }
        .login-container img {
            max-width: 100px;
            margin-bottom: 20px;
        }
        .login-container h2 {
            margin-bottom: 20px;
        }
        .login-container label {
            display: block;
            margin: 10px 0 5px;
            color: #000;
            text-align: left;
        }
        .login-container select,
        .login-container input,
        .login-container button {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #fff;
            color: #000;
            box-sizing: border-box;
        }
        .login-container button {
            border: none;
            background-color: #ff6600;
            color: #fff;
            cursor: pointer;
        }
        .login-container button:hover {
            background-color: #e65c00;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <img src="ZidanLaundry.png" alt="Logo">
        <h2>Login</h2>
        <form action="login_process.php" method="POST">
            <label for="username">Login sebagai:</label>
            <select id="username" name="username" required>
                <option value="">Pilih Username</option>
                <?php
                // Fetch usernames from the database
                $conn = mysqli_connect("localhost", "root", "", "zidanlaundry");
                $query = "SELECT * FROM admin";
                $result = mysqli_query($conn, $query);
                while ($row = mysqli_fetch_assoc($result)) {
                    echo '<option value="' . $row['kode_admin'] . '">' . $row['admin'] . '</option>';
                }
                mysqli_close($conn);
                ?>
            </select>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>
