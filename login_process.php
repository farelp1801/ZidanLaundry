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

// Predefined password
$predefined_password = 'admin';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selected_user = $_POST['username'];
    $password = $_POST['password'];
    
    if (isset($users[$selected_user]) && $password === $predefined_password) {
        // Set session variables
        $_SESSION['username'] = $users[$selected_user];
        $_SESSION['admin_code'] = $selected_user;
        
        header("Location: index.php");
        exit();
    } else {
        echo "Invalid username or password";
    }
}
?>
