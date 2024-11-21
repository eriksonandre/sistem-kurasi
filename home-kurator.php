<?php
// Pastikan pengguna sudah login sebagai admin
session_start();
if (!isset($_SESSION['level']) || $_SESSION['level'] !== 'Kurator') {
    header('Location: index.php'); // Arahkan ke halaman login jika bukan admin
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Kurator</title>
    <link rel="stylesheet" type="text/css" href="style-admin.css">
</head>
<body>
    <div class="container">
        <nav class="sidebar">
            <h2>Dashboard Admin</h2><br>
            <ul>
                <li><a href="home-kurator.php">Beranda</a></li>
                
                <li><a href="produk-perusahaan.php">Produk Perusahaan</a></li>
                <li><a href="sertifikat.php">List Sertifikat</a></li>
                <li><a href="logout.php">Keluar</a></li>
            </ul>
        </nav>
        <main class="content">
            <h1>Selamat Datang, <?php echo htmlspecialchars($_SESSION['nama']); ?></h1>
            <!-- Konten lainnya bisa ditambahkan di sini -->
        </main>
    </div>
</body>
</html>
