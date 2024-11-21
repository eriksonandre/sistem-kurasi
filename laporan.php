<?php
session_start();
if (!isset($_SESSION['level']) || $_SESSION['level'] !== 'Admin') {
    header('Location: index.php'); // Arahkan ke halaman login jika bukan kurator
    exit();
}

// Koneksi ke database
require_once 'connection.php';

// Query untuk mengambil data sertifikat beserta nilai dari penilaian atau penilaian_food
$query = "SELECT 
              s.kode_sertifikat, 
              s.kode_usaha, 
              s.kode_produk, 
              s.foto_sertifikat, 
              p.nama_produk, 
              pu.nama_usaha, 
              pu.nama_pemilik,
              COALESCE(pn.nilai, pf.nilai) AS nilai
          FROM sertifikat s
          JOIN produk p ON s.kode_produk = p.kode_produk
          JOIN pelaku_usaha pu ON s.kode_usaha = pu.kode_usaha
          LEFT JOIN penilaian pn ON s.kode_usaha = pn.kode_usaha AND s.kode_produk = pn.kode_produk
          LEFT JOIN penilaian_food pf ON s.kode_usaha = pf.kode_usaha AND s.kode_produk = pf.kode_produk";

$result = $connection->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Sertifikat</title>
    <link rel="stylesheet" type="text/css" href="style-admin.css">
    <style>
        /* CSS untuk modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            overflow: auto;
        }

        .modal-content {
            margin: 15% auto;
            display: block;
            max-width: 80%;
            max-height: 80%;
        }

        .close {
            position: absolute;
            top: 10px;
            right: 25px;
            color: white;
            font-size: 35px;
            font-weight: bold;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <nav class="sidebar">
            <h2>Dashboard Kurator</h2><br>
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
            <h1>Daftar Sertifikat</h1>
            
            <!-- Tombol Ekspor ke Excel -->
            <form action="export_excel.php" method="post">
                <button type="submit">Export to Excel</button>
            </form>

            <?php if ($result->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>No. Sertifikat</th>
                            <th>Nama Produk</th>
                            <th>Nama Usaha</th>
                            <th>Nama Pemilik</th>
                            <th>Gambar Sertifikat</th>
                            <th>Nilai</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['kode_sertifikat']); ?></td>
                                <td><?php echo htmlspecialchars($row['nama_produk']); ?></td>
                                <td><?php echo htmlspecialchars($row['nama_usaha']); ?></td>
                                <td><?php echo htmlspecialchars($row['nama_pemilik']); ?></td>
                                <td>
                                    <?php if (!empty($row['foto_sertifikat'])): ?>
                                        <!-- Tampilkan gambar sertifikat -->
                                        <img src="data:image/png;base64,<?php echo base64_encode($row['foto_sertifikat']); ?>" 
                                             alt="Sertifikat" width="150" height="auto"
                                             onclick="openModal('<?php echo base64_encode($row['foto_sertifikat']); ?>')">
                                    <?php else: ?>
                                        <p>Gambar tidak tersedia</p>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($row['nilai'] ?? 'Tidak ada nilai'); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Tidak ada data sertifikat yang tersedia.</p>
            <?php endif; ?>
        </main>
    </div>

    <!-- Modal untuk menampilkan gambar secara full screen -->
    <div id="myModal" class="modal">
        <span class="close" onclick="closeModal()">&times;</span>
        <img class="modal-content" id="modalImage">
    </div>

    <script>
        // Fungsi untuk membuka modal dan menampilkan gambar
        function openModal(imgData) {
            var modal = document.getElementById("myModal");
            var modalImg = document.getElementById("modalImage");
            modal.style.display = "block";
            modalImg.src = "data:image/png;base64," + imgData;
        }

        // Fungsi untuk menutup modal
        function closeModal() {
            var modal = document.getElementById("myModal");
            modal.style.display = "none";
        }
    </script>
</body>
</html>
