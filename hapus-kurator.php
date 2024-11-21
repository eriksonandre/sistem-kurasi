<?php
session_start();
if (!isset($_SESSION['level']) || $_SESSION['level'] !== 'Admin') {
    header('Location: index.php');
    exit();
}

require_once 'connection.php';

if (isset($_GET['id'])) {
    $username = $_GET['id'];

    // Melindungi query dari SQL Injection
    $username = mysqli_real_escape_string($connection, $username);

    // Query untuk menghapus kurator berdasarkan username
    $deleteQuery = "DELETE FROM user WHERE username = '$username' AND level = 'Kurator'";

    if ($connection->query($deleteQuery) === TRUE) {
        echo "<script>
                alert('Kurator berhasil dihapus.');
                window.location.href = 'kelola-kurator.php';
              </script>";
    } else {
        echo "<script>
                alert('Gagal menghapus kurator.');
                window.location.href = 'kelola-kurator.php';
              </script>";
    }
} else {
    header('Location: kelola-kurator.php');
    exit();
}
?>
