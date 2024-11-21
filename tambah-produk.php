<?php
session_start();
if (!isset($_SESSION['level']) || $_SESSION['level'] !== 'Admin') {
    header('Location: index.php');
    exit();
}

// Koneksi ke database
require_once 'connection.php';
ob_start();

// Ambil kode_usaha dari URL
$kode_usaha = $_GET['kode_usaha'];

// Proses form ketika disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Mengambil data dari formulir
    $kode_usaha = $_GET['kode_usaha']; // Asumsi kode usaha diambil dari URL
    $nama_produk = $_POST['nama_produk'];
    $jenis_komoditi = $_POST['jenis_komoditi'];
    $harga = $_POST['harga'];
    $komposisi = $_POST['komposisi'];

    // Menangani upload gambar
    $foto_produk = NULL;
    if (isset($_FILES['foto_produk']) && $_FILES['foto_produk']['error'] == 0) {
        // Mendapatkan data gambar
        $foto_produk = file_get_contents($_FILES['foto_produk']['tmp_name']);
    }

    // Query untuk menyimpan data produk ke database
    $query = "INSERT INTO produk (kode_usaha, nama_produk, jenis_komoditi, foto_produk, harga, komposisi) 
              VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $connection->prepare($query);
    
    // Mengikat parameter
    $stmt->bind_param("ssssds", $kode_usaha, $nama_produk, $jenis_komoditi, $foto_produk, $harga, $komposisi);
    
    // Menjalankan query
    if ($stmt->execute()) {
        echo "Produk berhasil ditambahkan!";
        // Redirect ke halaman lihat perusahaan
        header("Location: lihat-perusahaan.php?kode_usaha=$kode_usaha");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk</title>
    <link rel="stylesheet" href="style-admin.css">
</head>
<body>
    <div class="container">
        <h1>Tambah Produk</h1>

        <form action="" method="POST" enctype="multipart/form-data">
            <label>Nama Produk:</label>
            <input type="text" name="nama_produk" required><br>

            <label>Jenis Komoditi:</label>
            <select name="jenis_komoditi" required>
                <option value="Fashion">Fashion</option>
                <option value="Food">Food</option>
                <option value="Craft">Craft</option>
            </select><br>

            <label>Harga:</label>
            <input type="number" name="harga" required><br>

            <label>Komposisi:</label>
            <input type="text" name="komposisi" required><br>

            <label for="foto_produk">Foto Produk</label>
            <input type="file" name="foto_produk" id="foto_produk" required>

            <button type="submit" class="btn-aksi">Tambah Produk</button>
        </form>

        <form action="lihat-perusahaan.php" method="get" style="margin-top: 20px;">
            <input type="hidden" name="kode_usaha" value="<?php echo htmlspecialchars($kode_usaha); ?>">
            <button type="submit" class="btn-aksi kembali">Batal</button>
        </form>
    </div>
</body>
</html>
