<?php
session_start();
if (!isset($_SESSION['level']) || $_SESSION['level'] !== 'Admin') {
    header('Location: index.php');
    exit();
}

// Koneksi ke database
require_once 'connection.php';

// Mengambil data perusahaan
$query = "SELECT kode_usaha, nama_usaha FROM pelaku_usaha";
$result = $connection->query($query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List Perusahaan</title>
    <link rel="stylesheet" href="style-admin.css">
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
                <li><a href="laporan.php">Laporan</a></li>
                <li><a href="logout.php">Keluar</a></li>
            </ul>
        </nav>

        <main class="content">
            <h1>List Perusahaan</h1>

            <?php if ($result->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Kode Usaha</th>
                            <th>Nama Usaha</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['kode_usaha']; ?></td>
                                <td><?php echo $row['nama_usaha']; ?></td>
                                <td>
                                    <!-- Tombol Lihat -->
                                    <form action="lihat-perusahaan.php" method="GET" style="display:inline;">
                                        <input type="hidden" name="kode_usaha" value="<?php echo $row['kode_usaha']; ?>">
                                        <button type="submit" class="btn-aksi">Lihat</button>
                                    </form>
                                    
                                    <!-- Tombol Hapus (menggunakan form) -->
                                    <form action="hapus-perusahaan.php" method="GET" style="display:inline;">
                                        <input type="hidden" name="kode_usaha" value="<?php echo $row['kode_usaha']; ?>">
                                        <button type="submit" class="btn-hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus perusahaan ini?')">Hapus</button>
                                    </form>


                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Tidak ada perusahaan yang terdaftar.</p>
            <?php endif; ?>
        </main>
    </div>

    <script src="script.js"></script>
</body>
</html>
