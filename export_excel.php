<?php
session_start();
if (!isset($_SESSION['level']) || $_SESSION['level'] !== 'Admin') {
    header('Location: index.php'); // Arahkan ke halaman login jika bukan Admin
    exit();
}

// Koneksi ke database
require_once 'connection.php';

// Header untuk membuat file CSV
header("Content-Type: text/csv");
header("Content-Disposition: attachment; filename=daftar_sertifikat.csv");
header("Pragma: no-cache");
header("Expires: 0");

// Outputkan header kolom
echo "No,No. Sertifikat,Nama Produk,Nama Usaha,Nama Pemilik,Nilai\n";

// Query untuk mengambil data sertifikat
$query = "SELECT 
              s.kode_sertifikat, 
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

if ($result->num_rows > 0) {
    // Loop untuk output setiap baris data
    $no = 1;
    while ($row = $result->fetch_assoc()) {
        echo $no++ . "," . // Nomor urut
             $row['kode_sertifikat'] . "," .
             $row['nama_produk'] . "," .
             $row['nama_usaha'] . "," .
             $row['nama_pemilik'] . "," .
             ($row['nilai'] ?? 'Tidak ada nilai') . "\n";
    }
} else {
    echo "Tidak ada data sertifikat untuk diekspor.";
}



// Query untuk mengambil data sertifikat
$query = "SELECT 
              kode_usaha, 
              kode_produk, 
              brand, 
              kualitas_bahan,
              kerapihan,
              tampilan,
              harga_pasar,
              penilai,
              tanggal_penilaian
              FROM penilaian";
$query2 = "SELECT 
              kode_usaha, 
              kode_produk, 
              rasa, 
              packaging,
              harga,
              ketahanan,
              penilai,
              tanggal_penilaian
              FROM penilaian_food";

$result = $connection->query($query);
$result2 = $connection->query($query2);
$no = 1;
if ($result->num_rows > 0 || $result2->num_rows > 0) {
    echo "\nRINCIAN PENILAIAN\n";
    // Loop untuk output setiap baris data
    if($result->num_rows > 0){
     echo "\nNo,Kode Usaha,Kode Produk,Nilai Brand,Kualitas Bahan,Nilai Kerapihan,Nilai Tampilan,Nilai Harga Pasar,Penilai,Tanggal Penilaian\n";
        while ($row = $result->fetch_assoc()) {
            echo $no++ . "," . // Nomor urut
                 $row['kode_usaha'] . "," .
                 $row['kode_produk'] . "," .
                 $row['brand'] . "," .
                 $row['kualitas_bahan'] . "," .
                 $row['kerapihan'] . "," .
                 $row['tampilan'] . "," .
                 $row['harga_pasar'] . "," .
                 $row['penilai'] . "," .
                 $row['tanggal_penilaian'] . "," . "\n";
        }
    
    }
    if($result2->num_rows > 0){
     echo "\nNo,Kode Usaha,Kode Produk,Nilai Rasa,Nilai Packaging,Nilai Harga,Nilai Ketahanan,,Penilai,Tanggal Penilaian\n";
        while ($row = $result2->fetch_assoc()) {
            echo $no++ . "," . // Nomor urut
                 $row['kode_usaha'] . "," .
                 $row['kode_produk'] . "," .
                 $row['rasa'] . "," .
                 $row['packaging'] . "," .
                 $row['harga'] . "," .
                 $row['ketahanan'] . ",," .
                 $row['penilai'] . "," .
                 $row['tanggal_penilaian'] . "," . "\n";
        }
    }
    
} else {
    echo "Tidak ada data penilaian untuk diekspor.";
}
?>
