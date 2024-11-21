<?php
session_start();
if (!isset($_SESSION['level']) || $_SESSION['level'] !== 'Kurator') {
    header('Location: index.php'); // Arahkan ke halaman login jika bukan kurator
    exit();
}

// Koneksi ke database
require_once 'connection.php';

// Jika form dipilih dan dikirim
$produk = [];
if (isset($_POST['komoditi'])) {
    $komoditi = $_POST['komoditi'];

    // Ambil produk berdasarkan komoditi yang dipilih dan yang belum ada di tabel penilaian atau penilaian_food
    $query_produk = "
        SELECT p.kode_produk, p.nama_produk, pu.nama_usaha, p.jenis_komoditi 
        FROM produk p
        JOIN pelaku_usaha pu ON p.kode_usaha = pu.kode_usaha
        LEFT JOIN penilaian pn ON p.kode_produk = pn.kode_produk AND p.kode_usaha = pn.kode_usaha
        LEFT JOIN penilaian_food pf ON p.kode_produk = pf.kode_produk AND p.kode_usaha = pf.kode_usaha
        WHERE p.jenis_komoditi = ?
        AND pn.kode_produk IS NULL
        AND pf.kode_produk IS NULL
    ";
    
    $stmt = $connection->prepare($query_produk);
    $stmt->bind_param("s", $komoditi);
    $stmt->execute();
    $result_produk = $stmt->get_result();

    // Ambil hasil produk
    while ($row = $result_produk->fetch_assoc()) {
        $produk[] = $row;
    }
}
?>

<?php if (isset($_GET['status'])): ?>
    <?php if ($_GET['status'] === 'success'): ?>
        <p style="color: green;">Penilaian berhasil disimpan!</p>
    <?php elseif ($_GET['status'] === 'error'): ?>
        <p style="color: red;">Terjadi kesalahan saat menyimpan penilaian. Silakan coba lagi.</p>
    <?php endif; ?>
<?php endif; ?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produk Perusahaan</title>
    <link rel="stylesheet" type="text/css" href="style-admin.css">
</head>
<body>
    <div class="container">
        <nav class="sidebar">
            <h2>Dashboard Kurator</h2><br>
            <ul>
                <li><a href="home-kurator.php">Beranda</a></li>
                <li><a href="produk-perusahaan.php">Produk Perusahaan</a></li>
                <li><a href="sertifikat.php">List Sertifikat</a></li>
                <li><a href="logout.php">Keluar</a></li>
            </ul>
        </nav>
        <main class="content">
            <h1>Produk Perusahaan</h1>

            <!-- Form untuk memilih komoditi -->
            <form action="produk-perusahaan.php" method="POST">
                <label for="komoditi">Pilih Komoditi:</label>
                <select name="komoditi" id="komoditi" required>
                    <option value="">-- Pilih Komoditi --</option>
                    <option value="Fashion">Fashion</option>
                    <option value="Food">Food</option>
                    <option value="Craft">Craft</option>
                </select>
                <button type="submit">Tampilkan</button>
            </form>

            <?php if (isset($produk) && count($produk) > 0): ?>
                <h3>Daftar Produk dengan Komoditi: <?php echo htmlspecialchars($komoditi); ?></h3>
                <table>
                    <thead>
                        <tr>
                            <th>Kode Produk</th>
                            <th>Nama Produk</th>
                            <th>Nama Usaha</th>
                            <th>Komoditi</th>
                            <th>Penilaian</th> <!-- Kolom Penilaian -->
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($produk as $p): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($p['kode_produk']); ?></td>
                                <td><?php echo htmlspecialchars($p['nama_produk']); ?></td>
                                <td><?php echo htmlspecialchars($p['nama_usaha']); ?></td>
                                <td><?php echo htmlspecialchars($p['jenis_komoditi']); ?></td>
                                <td>
                                    <!-- Tombol Penilaian -->
                                    <?php if ($p['jenis_komoditi'] == 'Food'): ?>
                                        <a href="penilaian-food.php?kode_produk=<?php echo $p['kode_produk']; ?>" class="btn-penilaian">Penilaian</a>
                                    <?php else: ?>
                                        <a href="penilaian.php?kode_produk=<?php echo $p['kode_produk']; ?>" class="btn-penilaian">Penilaian</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php elseif (isset($komoditi)): ?>
                <p>Tidak ada produk untuk komoditi "<?php echo htmlspecialchars($komoditi); ?>".</p>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>
