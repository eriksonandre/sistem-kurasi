<?php
session_start();
if (!isset($_SESSION['level']) || $_SESSION['level'] !== 'Admin') {
    header('Location: index.php');
    exit();
}

// Koneksi ke database
require_once 'connection.php';

$kode_usaha = $_GET['kode_usaha'];

// Mendapatkan data perusahaan berdasarkan kode_usaha
$query = "SELECT nama_usaha, nama_pemilik, nik, alamat, kelurahan, kecamatan, no_telp, perijinan_nib, perijinan_halal, perijinan_pirt, perijinan_md FROM pelaku_usaha WHERE kode_usaha = ?";
$stmt = $connection->prepare($query);
$stmt->bind_param("s", $kode_usaha);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

// Memproses form edit
// Memproses form edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_usaha = $_POST['nama_usaha'];
    $nama_pemilik = $_POST['nama_pemilik'];
    $nik = $_POST['nik'];
    $alamat = $_POST['alamat'];
    $kelurahan = $_POST['kelurahan'];
    $kecamatan = $_POST['kecamatan'];
    $no_telp = $_POST['no_telp'];
    
    // Cek apakah file baru diunggah dan gambar lama ada
    // Untuk gambar NIB
    if (isset($_FILES['gambar_nib']) && $_FILES['gambar_nib']['error'] === UPLOAD_ERR_OK) {
        // Baca file sebagai blob
        $gambar_nib = file_get_contents($_FILES['gambar_nib']['tmp_name']);
    } elseif (!isset($_FILES['gambar_nib']) || $_FILES['gambar_nib']['error'] !== UPLOAD_ERR_OK) {
        // Jika tidak ada file baru, gunakan gambar lama atau biarkan kosong jika tidak ada
        $gambar_nib = !empty($row['perijinan_nib']) ? $row['perijinan_nib'] : null;
    }

    // Untuk gambar Halal
    if (isset($_FILES['gambar_halal']) && $_FILES['gambar_halal']['error'] === UPLOAD_ERR_OK) {
        // Baca file sebagai blob
        $gambar_halal = file_get_contents($_FILES['gambar_halal']['tmp_name']);
    } elseif (!isset($_FILES['gambar_halal']) || $_FILES['gambar_halal']['error'] !== UPLOAD_ERR_OK) {
        // Jika tidak ada file baru, gunakan gambar lama atau biarkan kosong jika tidak ada
        $gambar_halal = !empty($row['perijinan_halal']) ? $row['perijinan_halal'] : null;
    }

    // Untuk gambar PIRT
    if (isset($_FILES['gambar_pirt']) && $_FILES['gambar_pirt']['error'] === UPLOAD_ERR_OK) {
        // Baca file sebagai blob
        $gambar_pirt = file_get_contents($_FILES['gambar_pirt']['tmp_name']);
    } elseif (!isset($_FILES['gambar_pirt']) || $_FILES['gambar_pirt']['error'] !== UPLOAD_ERR_OK) {
        // Jika tidak ada file baru, gunakan gambar lama atau biarkan kosong jika tidak ada
        $gambar_pirt = !empty($row['perijinan_pirt']) ? $row['perijinan_pirt'] : null;
    }

    // Untuk gambar MD
    if (isset($_FILES['gambar_md']) && $_FILES['gambar_md']['error'] === UPLOAD_ERR_OK) {
        // Baca file sebagai blob
        $gambar_md = file_get_contents($_FILES['gambar_md']['tmp_name']);
    } elseif (!isset($_FILES['gambar_md']) || $_FILES['gambar_md']['error'] !== UPLOAD_ERR_OK) {
        // Jika tidak ada file baru, gunakan gambar lama atau biarkan kosong jika tidak ada
        $gambar_md = !empty($row['perijinan_md']) ? $row['perijinan_md'] : null;
    }
    
    // Query update untuk memperbarui data
    $update_query = "UPDATE pelaku_usaha SET nama_usaha=?, nama_pemilik=?, nik=?, alamat=?, kelurahan=?, kecamatan=?, no_telp=?, perijinan_nib=?, perijinan_halal=?, perijinan_pirt=?, perijinan_md=? WHERE kode_usaha=?";
    $stmt = $connection->prepare($update_query);
    $stmt->bind_param("ssssssssssss", $nama_usaha, $nama_pemilik, $nik, $alamat, $kelurahan, $kecamatan, $no_telp, $gambar_nib, $gambar_halal, $gambar_pirt, $gambar_md, $kode_usaha);

    if ($stmt->execute()) {
        // Tampilkan notifikasi berhasil dan redirect
        echo "<script>
                alert('Data perusahaan berhasil diperbarui.');
                window.location.href = 'lihat-perusahaan.php?kode_usaha=$kode_usaha';
              </script>";
        exit;
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
    <title>Edit Perusahaan</title>
    <link rel="stylesheet" href="style-admin.css">
</head>
<body>
    <div class="container">
        <h1>Edit Perusahaan</h1>
        
        <form action="" method="POST" enctype="multipart/form-data">
            <label>Nama Usaha:</label>
            <input type="text" name="nama_usaha" value="<?php echo htmlspecialchars($row['nama_usaha']); ?>" required><br>

            <label>Nama Pemilik:</label>
            <input type="text" name="nama_pemilik" value="<?php echo htmlspecialchars($row['nama_pemilik']); ?>" required><br>

            <label>NIK:</label>
            <input type="text" name="nik" value="<?php echo htmlspecialchars($row['nik']); ?>" required><br>

            <label>Alamat:</label>
            <input type="text" name="alamat" value="<?php echo htmlspecialchars($row['alamat']); ?>" required><br>

            <label>Kelurahan:</label>
            <input type="text" name="kelurahan" value="<?php echo htmlspecialchars($row['kelurahan']); ?>" required><br>

            <label>Kecamatan:</label>
            <input type="text" name="kecamatan" value="<?php echo htmlspecialchars($row['kecamatan']); ?>" required><br>

            <label>No. Telepon:</label>
            <input type="text" name="no_telp" value="<?php echo htmlspecialchars($row['no_telp']); ?>" required><br>
            
            <label>Perijinan NIB:</label>
            <input type="file" id="gambar_nib" name="gambar_nib" accept="image/*"><br>
            
            <label>Perijinan halal:</label>
            <input type="file" name="gambar_halal" accept="image/*"><br>
            
            <label>Perijinan PIRT:</label>
            <input type="file" name="gambar_pirt" accept="image/*"><br>
            
            <label>Perijinan MD:</label>
            <input type="file" name="gambar_md" accept="image/*"><br>

            <button type="submit" class="btn-aksi">Simpan Perubahan</button>
        </form>

        <form action="lihat-perusahaan.php" method="get" style="margin-top: 20px;">
            <input type="hidden" name="kode_usaha" value="<?php echo $kode_usaha; ?>">
            <button type="submit" class="btn-aksi kembali">Batal</button>
        </form>
    </div>
</body>
</html>
