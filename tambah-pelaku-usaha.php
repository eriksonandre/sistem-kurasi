<?php
session_start();
if (!isset($_SESSION['level']) || $_SESSION['level'] !== 'Admin') {
    header('Location: index.php');
    exit();
}

// Koneksi ke database
require_once 'connection.php';

// Fungsi untuk menghasilkan kode usaha unik
function generateUniqueCode($length = 8) {
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $code = '';
    for ($i = 0; $i < $length; $i++) {
        $code .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $code;
}

// Fungsi untuk memeriksa apakah kode usaha sudah ada di database
function isUniqueCode($code, $connection) {
    $query = "SELECT COUNT(*) AS count FROM pelaku_usaha WHERE kode_usaha = '$code'";
    $result = $connection->query($query);
    $row = $result->fetch_assoc();
    return $row['count'] == 0;  // Jika 0 berarti kode usaha unik
}

// Inisialisasi variabel untuk pesan kesalahan atau sukses
$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Proses untuk menghasilkan kode usaha unik
    do {
        $kode_usaha = generateUniqueCode(8); // Generate kode usaha unik
    } while (!isUniqueCode($kode_usaha, $connection)); // Periksa apakah kode usaha unik

    $nama_usaha = $_POST['nama_usaha'];
    $nama_pemilik = $_POST['nama_pemilik'];
    $nik = $_POST['nik'];
    $alamat = $_POST['alamat'];
    $kelurahan = $_POST['kelurahan'];
    $kecamatan = $_POST['kecamatan'];
    $no_telp = $_POST['no_telp'];

    // Validasi file gambar KTP
    $ktp = $_FILES['ktp']['tmp_name'];
    $ktp_content = addslashes(file_get_contents($ktp));

    // Validasi file gambar Perijinan
    $perijinan_nib = $_FILES['perijinan_nib']['tmp_name'];
    $perijinan_nib_content = addslashes(file_get_contents($perijinan_nib));
    $perijinan_halal = $_FILES['perijinan_halal']['tmp_name'];
    $perijinan_halal_content = addslashes(file_get_contents($perijinan_halal));
    $perijinan_pirt = $_FILES['perijinan_pirt']['tmp_name'];
    $perijinan_pirt_content = addslashes(file_get_contents($perijinan_pirt));
    $perijinan_md = $_FILES['perijinan_md']['tmp_name'];
    $perijinan_md_content = addslashes(file_get_contents($perijinan_md));

    // Validasi input
    if (empty($nama_usaha) || empty($nama_pemilik) || empty($nik) || empty($alamat) || empty($kelurahan) || empty($kecamatan) || empty($ktp)) {
        $error = "Semua field wajib diisi.";
    }  elseif (!preg_match("/^[a-zA-Z\s]+$/", $nama_pemilik)) {
        $error = "Nama pemilik hanya boleh mengandung huruf.";
    } elseif (strlen($nik) != 16 || !ctype_digit($nik)) {
        $error = "NIK harus berupa 16 angka.";
    }elseif (!ctype_digit($no_telp)) {
        $error = "Nomor telepon harus berupa angka.";
    } else {
        // Query untuk menambahkan data pelaku usaha ke dalam tabel
        $query = "INSERT INTO pelaku_usaha (kode_usaha, nama_usaha, nama_pemilik, nik, alamat, kelurahan, kecamatan, no_telp, ktp, perijinan_nib, perijinan_halal, perijinan_pirt, perijinan_md)
                  VALUES ('$kode_usaha', '$nama_usaha', '$nama_pemilik', '$nik', '$alamat', '$kelurahan', '$kecamatan', '$no_telp', '$ktp_content', '$perijinan_nib_content', '$perijinan_halal_content', '$perijinan_pirt_content', '$perijinan_md_content')";

        if ($connection->query($query) === TRUE) {
            $success = "Pelaku usaha berhasil ditambahkan dengan kode usaha: $kode_usaha.";
        } else {
            $error = "Error: " . $connection->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Pelaku Usaha</title>
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
            <h1>Tambah Pelaku Usaha</h1>
            
            <?php if ($error): ?>
                <p style="color: red; font-weight: bold;"><?php echo $error; ?></p>
            <?php elseif ($success): ?>
                <p style="color: green; font-weight: bold;"><?php echo $success; ?></p>
            <?php endif; ?>

            <form action="" method="POST" enctype="multipart/form-data" class="form-tambah">
                <label for="nama_usaha">Nama Usaha:</label>
                <input type="text" id="nama_usaha" name="nama_usaha" required pattern="[a-zA-Z\s]+" title="Nama usaha hanya boleh mengandung huruf.">

                <label for="nama_pemilik">Nama Pemilik:</label>
                <input type="text" id="nama_pemilik" name="nama_pemilik" required pattern="[a-zA-Z\s]+" title="Nama pemilik hanya boleh mengandung huruf.">

                <label for="nik">NIK:</label>
                <input type="text" id="nik" name="nik" maxlength="16" required pattern="\d{16}" title="NIK harus berupa 16 angka.">

                <label for="alamat">Alamat:</label>
                <input type="text" id="alamat" name="alamat" required>

                <label for="kelurahan">Kelurahan:</label>
                <input type="text" id="kelurahan" name="kelurahan" required>

                <label for="kecamatan">Kecamatan:</label>
                <input type="text" id="kecamatan" name="kecamatan" required>

                <label for="no_telp">No. Telepon:</label>
                <input type="text" id="no_telp" name="no_telp" maxlength="13" required>

                <label for="ktp">Upload KTP:</label>
                <input type="file" id="ktp" name="ktp" accept="image/*" required>

                <label for="perijinan_nib">Upload Perijinan NIB:</label>
                <input type="file" id="perijinan_nib" name="perijinan_nib" accept="image/*">
                <label for="perijinan_halal">Upload Perijinan Halal:</label>
                <input type="file" id="perijinan_halal" name="perijinan_halal" accept="image/*">
                <label for="perijinan_pirt">Upload Perijinan PIRT:</label>
                <input type="file" id="perijinan_pirt" name="perijinan_pirt" accept="image/*">
                <label for="perijinan_md">Upload Perijinan MD:</label>
                <input type="file" id="perijinan_md" name="perijinan_md" accept="image/*">

                <button type="submit" class="btn-submit">Tambah Pelaku Usaha</button>
            </form>
        </main>
    </div>

    <!-- Optional JavaScript -->
    <script src="script.js"></script>
</body>
</html>
