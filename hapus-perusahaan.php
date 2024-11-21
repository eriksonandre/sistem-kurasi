<?php
session_start();

// Cek apakah pengguna memiliki level Admin
if (!isset($_SESSION['level']) || $_SESSION['level'] !== 'Admin') {
    header('Location: index.php');
    exit();
}

// Koneksi ke database
require_once 'connection.php';

// Memastikan kode usaha ada di URL
if (isset($_GET['kode_usaha'])) {
    $kode_usaha = $_GET['kode_usaha'];

    // Mulai transaksi untuk memastikan penghapusan berjalan lancar
    $connection->begin_transaction();

    try {
        // Query untuk menghapus semua penilaian terkait dengan perusahaan
        $query_hapus_penilaian = "DELETE FROM penilaian WHERE kode_usaha = ?";
        $stmt_penilaian = $connection->prepare($query_hapus_penilaian);
        $stmt_penilaian->bind_param("s", $kode_usaha);
        $stmt_penilaian->execute();

        // Query untuk menghapus semua penilaian food terkait dengan perusahaan
        $query_hapus_penilaian_food = "DELETE FROM penilaian_food WHERE kode_usaha = ?";
        $stmt_penilaian_food = $connection->prepare($query_hapus_penilaian_food);
        $stmt_penilaian_food->bind_param("s", $kode_usaha);
        $stmt_penilaian_food->execute();


        // Query untuk menghapus semua sertifikat terkait dengan perusahaan
        $query_hapus_sertifikat = "DELETE FROM sertifikat WHERE kode_usaha = ?";
        $stmt_sertifikat = $connection->prepare($query_hapus_sertifikat);
        $stmt_sertifikat->bind_param("s", $kode_usaha);
        $stmt_sertifikat->execute();


        // Query untuk menghapus semua produk terkait dengan perusahaan
        $query_hapus_produk = "DELETE FROM produk WHERE kode_usaha = ?";
        $stmt_produk = $connection->prepare($query_hapus_produk);
        $stmt_produk->bind_param("s", $kode_usaha);
        $stmt_produk->execute();

        // Query untuk menghapus perusahaan
        $query_hapus_perusahaan = "DELETE FROM pelaku_usaha WHERE kode_usaha = ?";
        $stmt_perusahaan = $connection->prepare($query_hapus_perusahaan);
        $stmt_perusahaan->bind_param("s", $kode_usaha);
        $stmt_perusahaan->execute();

        // Jika semua query berhasil, commit transaksi
        $connection->commit();

        // Arahkan kembali ke halaman list perusahaan dengan pesan sukses
        header('Location: list-perusahaan.php?status=sukses');
        exit();
    } catch (Exception $e) {
        // Jika terjadi error, rollback transaksi
        $connection->rollback();

        // Tampilkan error dan arahkan ke halaman list perusahaan dengan pesan gagal
        echo "Terjadi kesalahan: " . $e->getMessage();
        header('Location: list-perusahaan.php?status=gagal');
        exit();
    }
} else {
    echo "Kode usaha tidak ditemukan.";
    exit();
}
?>
