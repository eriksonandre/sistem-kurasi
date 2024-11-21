<?php
session_start();
if (!isset($_SESSION['level']) || $_SESSION['level'] !== 'Admin') {
    header('Location: index.php');
    exit();
}

// Koneksi ke database
require_once 'connection.php';

// Mengambil kode usaha dari URL
$kode_usaha = $_GET['kode_usaha'];

// Query untuk mengambil data perusahaan berdasarkan kode_usaha
$query_perusahaan = "SELECT kode_usaha, nama_usaha, nama_pemilik, nik, alamat, kelurahan, kecamatan, ktp, perijinan_nib, perijinan_halal, perijinan_pirt, perijinan_md, no_telp FROM pelaku_usaha WHERE kode_usaha = ?";
$stmt_perusahaan = $connection->prepare($query_perusahaan);
$stmt_perusahaan->bind_param("s", $kode_usaha);
$stmt_perusahaan->execute();
$result_perusahaan = $stmt_perusahaan->get_result();

if ($result_perusahaan->num_rows > 0) {
    $perusahaan = $result_perusahaan->fetch_assoc();
} else {
    echo "Perusahaan tidak ditemukan.";
    exit;
}

// Query untuk mendapatkan daftar produk berdasarkan kode_usaha
$query_produk = "SELECT kode_produk, nama_produk, jenis_komoditi, harga, komposisi, foto_produk FROM produk WHERE kode_usaha = ?";
$stmt_produk = $connection->prepare($query_produk);
$stmt_produk->bind_param("s", $kode_usaha);
$stmt_produk->execute();
$result_produk = $stmt_produk->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Perusahaan</title>
    <link rel="stylesheet" href="style-admin.css">
    
    <style>
        /* CSS untuk modal */
        .modal {
            display: none; /* Modal disembunyikan secara default */
            position: fixed;
            z-index: 1; /* Di atas konten lainnya */
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7); /* Latar belakang gelap */
            overflow: auto; /* Menambahkan scroll jika diperlukan */
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

        <!-- Tombol Kembali -->
            <form action="list-perusahaan.php" method="get">
                <button type="submit" class="btn-aksi">Kembali</button>
            </form>
            <h1>Detail Perusahaan</h1>
        <main class="content">


            <!-- Detail Perusahaan -->
            <table>
                <tr><th>Kode Usaha</th><td><?php echo htmlspecialchars($perusahaan['kode_usaha']); ?></td></tr>
                <tr><th>Nama Usaha</th><td><?php echo htmlspecialchars($perusahaan['nama_usaha']); ?></td></tr>
                <tr><th>Nama Pemilik</th><td><?php echo htmlspecialchars($perusahaan['nama_pemilik']); ?></td></tr>
                <tr><th>NIK</th><td><?php echo htmlspecialchars($perusahaan['nik']); ?></td></tr>
                <tr><th>Alamat</th><td><?php echo htmlspecialchars($perusahaan['alamat']); ?></td></tr>
                <tr><th>Kelurahan</th><td><?php echo htmlspecialchars($perusahaan['kelurahan']); ?></td></tr>
                <tr><th>Kecamatan</th><td><?php echo htmlspecialchars($perusahaan['kecamatan']); ?></td></tr>
                <tr><th>No. Telepon</th><td><?php echo htmlspecialchars($perusahaan['no_telp']); ?></td></tr>
                <tr>
                    <th>Foto KTP</th>
                    <td>
                        <?php
                        if ($perusahaan['ktp']) {
                            echo '<img src="data:image/jpeg;base64,' . base64_encode($perusahaan['ktp']) . '" alt="KTP" width="200" height="auto" style="cursor:pointer;" onclick="openModal(\'data:image/jpeg;base64,' . base64_encode($perusahaan['ktp']) . '\')">';
                        } else {
                            echo "Tidak ada foto KTP.";
                        }
                        ?>
                    </td>
                </tr>

                <tr>
                    <th>Foto Perijinan NIB</th>
                    <td>
                        <?php
                        if ($perusahaan['perijinan_nib']) {
                            echo '<img src="data:image/jpeg;base64,' . base64_encode($perusahaan['perijinan_nib']) . '" alt="Perijinan NIB" width="200" height="auto" style="cursor:pointer;" onclick="openModal(\'data:image/jpeg;base64,' . base64_encode($perusahaan['perijinan_nib']) . '\')">';
                        } else {
                            echo "Tidak ada foto perijinan.";
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <th>Foto Perijinan Halal</th>
                    <td>
                        <?php
                        if ($perusahaan['perijinan_halal']) {
                            echo '<img src="data:image/jpeg;base64,' . base64_encode($perusahaan['perijinan_halal']) . '" alt="Perijinan Halal" width="200" height="auto" style="cursor:pointer;" onclick="openModal(\'data:image/jpeg;base64,' . base64_encode($perusahaan['perijinan_halal']) . '\')">';
                        } else {
                            echo "Tidak ada foto perijinan.";
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <th>Foto Perijinan PIRT</th>
                    <td>
                        <?php
                        if ($perusahaan['perijinan_pirt']) {
                            echo '<img src="data:image/jpeg;base64,' . base64_encode($perusahaan['perijinan_pirt']) . '" alt="Perijinan PIRT" width="200" height="auto" style="cursor:pointer;" onclick="openModal(\'data:image/jpeg;base64,' . base64_encode($perusahaan['perijinan_pirt']) . '\')">';
                        } else {
                            echo "Tidak ada foto perijinan.";
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <th>Foto Perijinan MD</th>
                    <td>
                        <?php
                        if ($perusahaan['perijinan_md']) {
                            echo '<img src="data:image/jpeg;base64,' . base64_encode($perusahaan['perijinan_md']) . '" alt="Perijinan MD" width="200" height="auto" style="cursor:pointer;" onclick="openModal(\'data:image/jpeg;base64,' . base64_encode($perusahaan['perijinan_md']) . '\')">';
                        } else {
                            echo "Tidak ada foto perijinan.";
                        }
                        ?>
                    </td>
                </tr>
            </table>

            <br>

            <!-- Tombol Edit Perusahaan -->
            <form action="edit-perusahaan.php" method="get">
                <input type="hidden" name="kode_usaha" value="<?php echo htmlspecialchars($perusahaan['kode_usaha']); ?>">
                <button type="submit" class="btn-aksi">Edit Perusahaan</button>
            </form>

            

            <!-- Daftar Produk -->
            <h2>Daftar Produk</h2>
            <?php if ($result_produk->num_rows > 0): ?>
                <table>
                    <tr>
                        <th>Nama Produk</th>
                        <th>Jenis Komoditi</th>
                        <th>Harga</th>
                        <th>Komposisi</th>
                        <th>Foto Produk</th>
                        <th>Aksi</th>
                    </tr>
                    <?php while ($produk = $result_produk->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($produk['nama_produk']); ?></td>
                            <td><?php echo htmlspecialchars($produk['jenis_komoditi']); ?></td>
                            <td><?php echo htmlspecialchars($produk['harga']); ?></td>
                            <td><?php echo htmlspecialchars($produk['komposisi']); ?></td>
                            <td>
                                <?php
                                if ($produk['foto_produk']) {
                                    echo '<img src="data:image/jpeg;base64,' . base64_encode($produk['foto_produk']) . '" alt="Foto Produk" width="100">';
                                } else {
                                    echo "Tidak ada foto.";
                                }
                                ?>
                            </td>
                            <td>
                                <!-- Tombol Hapus Produk -->
                                <form action="hapus-produk.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="kode_usaha" value="<?php echo htmlspecialchars($kode_usaha); ?>">
                                    <input type="hidden" name="kode_produk" value="<?php echo htmlspecialchars($produk['kode_produk']); ?>">
                                    <button type="submit" class="btn-aksi" onclick="return confirm('Yakin ingin menghapus produk ini?')">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            <?php else: ?>
                <p>Belum ada produk untuk perusahaan ini.</p>
            <?php endif; ?>

            <!-- Tombol Tambah Produk -->
            <form action="tambah-produk.php" method="get">
                <input type="hidden" name="kode_usaha" value="<?php echo htmlspecialchars($kode_usaha); ?>">
                <button type="submit" class="btn-aksi">Tambah Produk</button>
            </form>

        </main>
    </div>
    
    <div id="imageModal" class="modal">
        <span class="close" onclick="closeModal()">&times;</span>
        <img id="modalImage" class="modal-content">
    </div>
    
    <script>
    // Fungsi untuk membuka modal
        function openModal(imageSrc) {
            var modal = document.getElementById("imageModal");
            var modalImage = document.getElementById("modalImage");
            modal.style.display = "block";
            modalImage.src = imageSrc;
        }
    
        // Fungsi untuk menutup modal
        function closeModal() {
            var modal = document.getElementById("imageModal");
            modal.style.display = "none";
        }
    </script>

</body>
</html>
