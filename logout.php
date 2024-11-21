<?php
session_start(); // Memulai sesi

// Hapus semua variabel sesi
$_SESSION = array();


// Akhiri sesi
session_destroy();
header('Location: index.php');
exit();
?>
