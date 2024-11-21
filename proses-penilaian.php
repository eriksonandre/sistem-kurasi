<?php
session_start();
if (!isset($_SESSION['level']) || $_SESSION['level'] !== 'Kurator') {
    header('Location: index.php');
    exit();
}

// Koneksi ke database
require_once 'connection.php';

// Fungsi untuk membuat kode sertifikat unik
function generateUniqueCode($connection) {
    $isUnique = false;
    $code = '';

    while (!$isUnique) {
        $code = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8)) . "/Kurasi-Produk/Makasar/" . date("Y");
        $query = "SELECT * FROM sertifikat WHERE kode_sertifikat = ?";
        $stmt = $connection->prepare($query);
        $stmt->bind_param("s", $code);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 0) {
            $isUnique = true;
        }
    }
    return $code;
}

// Ambil data dari form
$kode_produk = $_POST['kode_produk'];
$kode_usaha = $_POST['kode_usaha'];
$brand = $_POST['brand'];
$bahan = $_POST['bahan'];
$jahitan = $_POST['jahitan'];
$tampilan = $_POST['tampilan'];
$harga = $_POST['harga'];
$nilai = $_POST['nilai_akhir'];
$penilai = $_POST['penilai'];

// Ambil data produk dan usaha untuk sertifikat
$query_produk = "SELECT nama_produk FROM produk WHERE kode_produk = ?";
$stmt_produk = $connection->prepare($query_produk);
$stmt_produk->bind_param("i", $kode_produk);
$stmt_produk->execute();
$result_produk = $stmt_produk->get_result();
$produk = $result_produk->fetch_assoc();

$query_usaha = "SELECT nama_usaha, nama_pemilik FROM pelaku_usaha WHERE kode_usaha = ?";
$stmt_usaha = $connection->prepare($query_usaha);
$stmt_usaha->bind_param("s", $kode_usaha);
$stmt_usaha->execute();
$result_usaha = $stmt_usaha->get_result();
$usaha = $result_usaha->fetch_assoc();

// Simpan penilaian ke dalam database
$query = "INSERT INTO penilaian (kode_produk, kode_usaha, brand, kualitas_bahan, kerapihan, tampilan, harga_pasar, nilai, penilai)
          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $connection->prepare($query);
$stmt->bind_param("issssssss", $kode_produk, $kode_usaha, $brand, $bahan, $jahitan, $tampilan, $harga, $nilai, $penilai);

if ($stmt->execute()) {
    // Buat kode sertifikat unik
    $kode_sertifikat = generateUniqueCode($connection);
    
    // Buat tanggal penilaian
    $tanggal_penilaian = date("Y-m-d");

    // Membuat gambar sertifikat
    $image = imagecreatefrompng('sertifikat-kosong.png');
    $text_color = imagecolorallocate($image, 0, 0, 0);
    $font_path = __DIR__ . '/Arial.ttf'; // Path ke file font yang ingin digunakan

    // Ukuran gambar
    $image_width = imagesx($image);
    $image_height = imagesy($image);

    // Fungsi untuk menulis teks rata tengah
    function centerText($image, $text, $font_path, $font_size, $y_offset, $color) {
        global $image_width;
        $bbox = imagettfbbox($font_size, 0, $font_path, $text);
        $text_width = $bbox[2] - $bbox[0];
        $x = ($image_width - $text_width) / 2;
        imagettftext($image, $font_size, 0, $x, $y_offset, $color, $font_path, $text);
    }

    // Tambahkan teks ke gambar sertifikat dengan posisi tengah
    $image_width = 2000;
$image_height = 1414;

// Menambahkan jarak antar teks
$distance_between_titles = 50; // Menambahkan jarak 50px antara dua teks

// Menambahkan teks pertama "HASIL KURASI PRODUK" di tengah gambar
centerText($image, "HASIL KURASI PRODUK", $font_path, 70, $image_height / 4 + 35, $text_color);

// Menambahkan teks kedua "No. Sertifikat" dengan jarak vertikal
centerText($image, "No. Sertifikat: " . $kode_sertifikat, $font_path, 25, ($image_height / 4) + 70 + 35 +35, $text_color);

// Menambahkan teks lainnya dengan jarak antar baris yang konsisten
centerText($image, "Dengan ini menyatakan bahwa produk:", $font_path, 25, ($image_height / 4) + 120 + $distance_between_titles * 2 + 35, $text_color);
centerText($image, $produk['nama_produk'], $font_path, 55, ($image_height / 4) + 190 + $distance_between_titles * 3 + 35, $text_color); // Diturunkan
centerText($image, "dari usaha " . $usaha['nama_usaha'] . " dengan pemilik " . $usaha['nama_pemilik'], $font_path, 25, ($image_height / 4) + 250 + $distance_between_titles * 4 + 35, $text_color);
centerText($image, "Telah terkurasi oleh Kecamatan Makasar dengan hasil kurasi: " . $nilai, $font_path, 25, ($image_height / 4) + 310 + $distance_between_titles * 4 + 20 +35, $text_color); // Diturunkan
centerText($image, "Dikeluarkan berdasarkan indikator-indikator penilaian", $font_path, 25, ($image_height / 4) + 360 + $distance_between_titles * 4 + 30 + 35, $text_color);
centerText($image, "produk yang telah dikurasi pada tanggal " . $tanggal_penilaian, $font_path, 25, ($image_height / 4) + 400 + $distance_between_titles * 4 + 40 +35, $text_color);

    // Output gambar ke buffer dan simpan dalam format BLOB
    ob_start();
    imagepng($image);
    $image_data = ob_get_contents();
    ob_end_clean();

    // Simpan sertifikat ke dalam tabel sertifikat
    $query_sertifikat = "INSERT INTO sertifikat (kode_sertifikat, kode_usaha, kode_produk, foto_sertifikat) VALUES (?, ?, ?, ?)";
    $stmt_sertifikat = $connection->prepare($query_sertifikat);
    $stmt_sertifikat->bind_param("ssis", $kode_sertifikat, $kode_usaha, $kode_produk, $image_data);

    if ($stmt_sertifikat->execute()) {
        header("Location: produk-perusahaan.php?status=success");
    } else {
        header("Location: produk-perusahaan.php?status=error");
    }

    imagedestroy($image);
} else {
    header("Location: produk-perusahaan.php?status=error");
}

?>
