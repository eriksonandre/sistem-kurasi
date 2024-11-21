<?php
session_start();
if (!isset($_SESSION['level']) || $_SESSION['level'] !== 'Admin') {
    header('Location: index.php');
    exit();
}

require_once 'connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = mysqli_real_escape_string($connection, $_POST['nama']);
    $username = mysqli_real_escape_string($connection, $_POST['username']);
    $password = mysqli_real_escape_string($connection, md5($_POST['password']));
    $level = 'Kurator';

    // Query untuk menambahkan kurator baru ke database
    $query = "INSERT INTO user (nama, username, password, level) VALUES ('$nama', '$username', '$password', '$level')";

    if ($connection->query($query) === TRUE) {
        echo "<script>
                alert('Kurator berhasil ditambahkan.');
                window.location.href = 'kelola-kurator.php';
              </script>";
    } else {
        echo "<script>
                alert('Gagal menambahkan kurator.');
              </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Kurator</title>
    <link rel="stylesheet" type="text/css" href="style-admin.css">
</head>
<body>
    <div class="container">
        <nav class="sidebar">
            <h2>Dashboard Admin</h2><br>
            <ul>
                <li><a href="home-admin.php">Beranda</a></li>
                <li><a href="kelola-kurator.php">Kelola Kurator</a></li>
                <li><a href="tambah-pelaku-usaha.php">Tambah Pelaku Usaha</a></li>
                <li><a href="list-perusahaan.php">List Perusahaan</a></li>
                <li><a href="#">Laporan</a></li>
                <li><a href="logout.php">Keluar</a></li>
            </ul>
        </nav>
        <main class="content">
            <div class="header">
                <h1>Tambah Kurator</h1>
            </div>
            <div class="form-container">
                <form action="tambah-kurator.php" method="POST">
                    <label for="nama">Nama:</label>
                    <input type="text" id="nama" name="nama" required>

                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required>

                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>

                    <button type="submit" class="btn-submit">Tambah Kurator</button>
                    <a href="kelola-kurator.php" class="btn-back">Kembali</a>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
