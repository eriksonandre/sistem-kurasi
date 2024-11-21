<?php
session_start();

// Cek apakah nilai_akhir ada dalam POST
if (isset($_POST['nilai_akhir'])) {
    // Menyimpan nilai akhir dalam session
    $_SESSION['nilai_akhir'] = $_POST['nilai_akhir'];

    // Anda bisa mengembalikan respon jika perlu
    echo json_encode(['status' => 'success', 'nilai_akhir' => $_SESSION['nilai_akhir']]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Nilai tidak diterima']);
}
?>
