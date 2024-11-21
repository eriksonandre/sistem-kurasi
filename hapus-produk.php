<?php
session_start();

// Pastikan hanya admin yang bisa mengakses halaman ini
if (!isset($_SESSION['level']) || $_SESSION['level'] !== 'Admin') {
    header('Location: index.php');
    exit();
}

// Koneksi ke database
require_once 'connection.php';

// Mengambil kode usaha dan kode produk dari form
if (isset($_POST['kode_usaha'], $_POST['kode_produk'])) {
    $kode_usaha = $_POST['kode_usaha'];
    $kode_produk = $_POST['kode_produk'];

    // Query untuk menghapus semua penilaian terkait dengan perusahaan
    $query_hapus_penilaian = "DELETE FROM penilaian WHERE kode_usaha = ? AND kode_produk = ?";
    $stmt_penilaian = $connection->prepare($query_hapus_penilaian);
    $stmt_penilaian->bind_param("si", $kode_usaha, $kode_produk);
    $stmt_penilaian->execute();

    // Query untuk menghapus semua penilaian terkait dengan perusahaan
    $query_hapus_penilaian = "DELETE FROM penilaian_food WHERE kode_usaha = ? AND kode_produk = ?";
    $stmt_penilaian = $connection->prepare($query_hapus_penilaian);
    $stmt_penilaian->bind_param("si", $kode_usaha, $kode_produk);
    $stmt_penilaian->execute();

    // Query untuk menghapus semua sertifikat terkait dengan perusahaan
    $query_hapus_sertifikat = "DELETE FROM sertifikat WHERE kode_usaha = ? AND kode_produk = ?";
    $stmt_sertifikat = $connection->prepare($query_hapus_sertifikat);
    $stmt_sertifikat->bind_param("si", $kode_usaha, $kode_produk);
    $stmt_sertifikat->execute();

    // Query untuk menghapus produk berdasarkan kode_usaha dan kode_produk
    $query_hapus_produk = "DELETE FROM produk WHERE kode_usaha = ? AND kode_produk = ?";
    $stmt_hapus_produk = $connection->prepare($query_hapus_produk);
    $stmt_hapus_produk->bind_param("ss", $kode_usaha, $kode_produk);

    if ($stmt_hapus_produk->execute()) {
        // Redirect kembali ke halaman list perusahaan setelah penghapusan berhasil
        header("Location: lihat-perusahaan.php?kode_usaha=" . urlencode($kode_usaha));
        exit();
    } else {
        echo "Terjadi kesalahan saat menghapus produk.";
    }
} else {
    echo "Data tidak valid.";
}
?>
