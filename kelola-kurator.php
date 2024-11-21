<?php
session_start();
if (!isset($_SESSION['level']) || $_SESSION['level'] !== 'Admin') {
    header('Location: index.php');
    exit();
}

require_once 'connection.php';

$kuratorQuery = "SELECT * FROM user WHERE level = 'Kurator'";
$result = $connection->query($kuratorQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Kurator</title>
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
                <li><a href="laporan.php">Laporan</a></li>
                <li><a href="logout.php">Keluar</a></li>
            </ul>
        </nav>
        <main class="content">
            <div class="header">
                <h1>Kelola Kurator</h1>
                <a href="tambah-kurator.php" class="btn-add">Tambah Kurator</a>
            </div>
            <div class="kurator-list">
                <?php if ($result->num_rows > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Username</th>
                                <th>Tanggal Terdaftar</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['nama']); ?></td>
                                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                                    <td><?php echo htmlspecialchars($row['tgl_terdaftar']); ?></td>
                                    <td>
                                        <a href="edit-kurator.php?id=<?php echo urlencode($row['username']); ?>" class="btn-edit">Edit</a>
                                        <a href="hapus-kurator.php?id=<?php echo urlencode($row['username']); ?>" class="btn-delete" onclick="return confirm('Apakah Anda yakin ingin menghapus kurator ini?');">Hapus</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>Tidak ada kurator yang tersedia.</p>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>
