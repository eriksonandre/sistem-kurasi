<?php
session_start();
if (!isset($_SESSION['level']) || $_SESSION['level'] !== 'Admin') {
    header('Location: index.php');
    exit();
}

require_once 'connection.php';

if (isset($_GET['id'])) {
    $username = $_GET['id'];
    
    // Query untuk mengambil data kurator berdasarkan username
    $query = "SELECT * FROM user WHERE username = '$username' AND level = 'Kurator'";
    $result = $connection->query($query);

    if ($result->num_rows > 0) {
        $kurator = $result->fetch_assoc();
    } else {
        echo "<script>
                alert('Kurator tidak ditemukan.');
                window.location.href = 'kelola-kurator.php';
              </script>";
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = mysqli_real_escape_string($connection, $_POST['nama']);
    $username = mysqli_real_escape_string($connection, $_POST['username']);
    $newPassword = $_POST['password'];

    // Cek apakah password baru diisi
    if (!empty($newPassword)) {
        // Jika diisi, enkripsi password baru
        $password = mysqli_real_escape_string($connection, md5($newPassword));
    } else {
        // Jika tidak diisi, gunakan password lama dari database
        $password = $kurator['password'];
    }

    // Query untuk mengupdate data kurator
    $updateQuery = "UPDATE user SET nama = '$nama', password = '$password' WHERE username = '$username' AND level = 'Kurator'";

    if ($connection->query($updateQuery) === TRUE) {
        echo "<script>
                alert('Kurator berhasil diperbarui.');
                window.location.href = 'kelola-kurator.php';
              </script>";
    } else {
        echo "<script>
                alert('Gagal memperbarui kurator.');
              </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Kurator</title>
    <link rel="stylesheet" type="text/css" href="style-admin.css">
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
                <li><a href="#">Laporan</a></li>
                <li><a href="logout.php">Keluar</a></li>
            </ul>
        </nav>
        <main class="content">
            <div class="header">
                <h1>Edit Kurator</h1>
            </div>
            <div class="form-container">
                <form action="edit-kurator.php?id=<?php echo urlencode($kurator['username']); ?>" method="POST">
                    <label for="nama">Nama:</label>
                    <input type="text" id="nama" name="nama" value="<?php echo htmlspecialchars($kurator['nama']); ?>" required>

                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($kurator['username']); ?>" readonly>

                    <label for="password">Password Baru (biarkan kosong jika tidak ingin mengubah):</label>
                    <input type="password" id="password" name="password">

                    <button type="submit" class="btn-submit">Perbarui Kurator</button>
                    <a href="kelola-kurator.php" class="btn-back">Kembali</a>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
