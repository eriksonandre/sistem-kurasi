<?php
session_start();
if (!isset($_SESSION['level']) || $_SESSION['level'] !== 'Kurator') {
    header('Location: index.php'); // Arahkan ke halaman login jika bukan kurator
    exit();
}

// Koneksi ke database
require_once 'connection.php';

// Ambil kode produk dari parameter URL
$kode_produk = $_GET['kode_produk'];

// Query untuk mendapatkan data produk berdasarkan kode_produk
$query = "SELECT p.kode_produk, p.nama_produk, p.jenis_komoditi, p.foto_produk, p.harga, p.komposisi, pu.kode_usaha, pu.nama_usaha 
          FROM produk p
          JOIN pelaku_usaha pu ON p.kode_usaha = pu.kode_usaha
          WHERE p.kode_produk = ?";
$stmt = $connection->prepare($query);
$stmt->bind_param("i", $kode_produk);
$stmt->execute();
$result = $stmt->get_result();

// Ambil hasil produk
$produk = $result->fetch_assoc();

if (!$produk) {
    echo "Produk tidak ditemukan.";
    exit();
}


$kode_usaha = $produk['kode_usaha'];
if($kode_usaha === null){
    echo "<script>console.log('kode usaha tidak ada');</script>";
}
//Dapatkan berapa banyak perijinan
//Membersihkan cahce
$connection->query("RESET QUERY CACHE");
$query = "
    SELECT perijinan_nib, perijinan_halal, perijinan_pirt, perijinan_md FROM pelaku_usaha WHERE kode_usaha = ?";
$stmt = $connection->prepare($query);
$stmt->bind_param("i", $kode_usaha);
$stmt->execute();
$result = $stmt->get_result();

$total_gambar = 0;
if($row = $result->fetch_assoc()){
    //Hitung jumlah kolom yang tidak kosong
    
    foreach(['perijinan_nib', 'perijinan_halal', 'perijinan_pirt', 'perijinan_md'] as $key){
        if(!empty($row[$key])){
            $total_gambar++;
        }
    }
}

echo "<script> console.log('". $total_gambar ."')</script>";
echo "<script> console.log('". $kode_usaha ."')</script>";

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penilaian Produk - Food</title>
    <link rel="stylesheet" href="style-admin.css">
    <style>
        /* Styling untuk form radio button */
        .radio-group {
            display: flex;
            gap: 10px;
        }

        .radio-group input[type="radio"] {
            margin-right: 5px;
        }

        /* Styling untuk form dan tombol */
        .form-group {
            margin-bottom: 20px;
        }

        button {
            margin-top: 20px;
            padding: 10px 20px;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <div class="container">
        <nav class="sidebar">
            <h2>Dashboard Kurator</h2><br>
            <ul>
                <li><a href="home-kurator.php">Beranda</a></li>
                <li><a href="produk-perusahaan.php">Produk Perusahaan</a></li>
                <li><a href="logout.php">Keluar</a></li>
            </ul>
        </nav>
        <main class="content">
            <h1>Penilaian Produk: <?php echo htmlspecialchars($produk['nama_produk']); ?></h1>

            <div class="produk-detail">
                <h2>Detail Produk</h2>
                <table>
                    <tr>
                        <th>Kode Usaha</th>
                        <td><?php echo htmlspecialchars($produk['kode_usaha']); ?></td>
                    </tr>
                    <tr>
                        <th>Kode Produk</th>
                        <td><?php echo htmlspecialchars($produk['kode_produk']); ?></td>
                    </tr>
                    <tr>
                        <th>Nama Produk</th>
                        <td><?php echo htmlspecialchars($produk['nama_produk']); ?></td>
                    </tr>
                    <tr>
                        <th>Jenis Komoditi</th>
                        <td><?php echo htmlspecialchars($produk['jenis_komoditi']); ?></td>
                    </tr>
                    <tr>
                        <th>Foto Produk</th>
                        <td>
                            <?php
                            if ($produk['foto_produk']) {
                                echo '<img src="data:image/jpeg;base64,' . base64_encode($produk['foto_produk']) . '" alt="foto_produk" width="500">';
                            } else {
                                echo "Tidak ada foto produk.";
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Harga</th>
                        <td><?php echo "Rp " . number_format($produk['harga'], 0, ',', '.'); ?></td>
                    </tr>
                    <tr>
                        <th>Komposisi</th>
                        <td><?php echo htmlspecialchars($produk['komposisi']); ?></td>
                    </tr>
                </table>
            </div>

            <h2>Berikan Penilaian</h2>
            <form action="proses-penilaian.php" method="POST" id="penilaian-form">
                <input type="hidden" name="kode_produk" value="<?php echo $produk['kode_produk']; ?>">
                <input type="hidden" name="kode_usaha" value="<?php echo $produk['kode_usaha']; ?>">

                <!-- Penilaian Brand -->
                <div class="form-group">
                    <label for="brand">Memiliki brand (tidak ada = 0):</label><br>
                    <div class="radio-group">
                        <label><input type="radio" name="brand" value="0" required>0</label>
                        <label><input type="radio" name="brand" value="1">1 </label>
                        <label><input type="radio" name="brand" value="2">2 </label>
                        <label><input type="radio" name="brand" value="3">3 </label>
                        <label><input type="radio" name="brand" value="4">4 </label>
                        <label><input type="radio" name="brand" value="5">5 </label>
                        <label><input type="radio" name="brand" value="6">6 </label>
                        <label><input type="radio" name="brand" value="7">7 </label>
                        <label><input type="radio" name="brand" value="8">8 </label>
                        <label><input type="radio" name="brand" value="9">9 </label>
                        <label><input type="radio" name="brand" value="10">10 </label>
                    </div>
                </div>

                <!-- Penilaian Bahan -->
                <div class="form-group">
                    <label for="bahan">Kualitas bahan:</label><br>
                    <div class="radio-group">
                        <label><input type="radio" name="bahan" value="1" required>1 </label>
                        <label><input type="radio" name="bahan" value="2">2 </label>
                        <label><input type="radio" name="bahan" value="3">3 </label>
                        <label><input type="radio" name="bahan" value="4">4 </label>
                        <label><input type="radio" name="bahan" value="5">5 </label>
                        <label><input type="radio" name="bahan" value="6">6 </label>
                        <label><input type="radio" name="bahan" value="7">7 </label>
                        <label><input type="radio" name="bahan" value="8">8 </label>
                        <label><input type="radio" name="bahan" value="9">9 </label>
                        <label><input type="radio" name="bahan" value="10">10 </label>
                    </div>
                </div>

                <!-- Penilaian jahitan -->
                <div class="form-group">
                    <label for="jahitan">Penilaian kerapihan jahitan:</label><br>
                    <div class="radio-group">
                        <label><input type="radio" name="jahitan" value="1" required>1 </label>
                        <label><input type="radio" name="jahitan" value="2">2 </label>
                        <label><input type="radio" name="jahitan" value="3">3 </label>
                        <label><input type="radio" name="jahitan" value="4">4 </label>
                        <label><input type="radio" name="jahitan" value="5">5 </label>
                        <label><input type="radio" name="jahitan" value="6">6 </label>
                        <label><input type="radio" name="jahitan" value="7">7 </label>
                        <label><input type="radio" name="jahitan" value="8">8 </label>
                        <label><input type="radio" name="jahitan" value="9">9 </label>
                        <label><input type="radio" name="jahitan" value="10">10 </label>
                    </div>
                </div>

                <!-- Penilaian Tampilan -->
                <div class="form-group">
                    <label for="tampilan">Penilaian tampilan:</label><br>
                    <div class="radio-group">
                        <label><input type="radio" name="tampilan" value="1" required>1 </label>
                        <label><input type="radio" name="tampilan" value="2">2 </label>
                        <label><input type="radio" name="tampilan" value="3">3 </label>
                        <label><input type="radio" name="tampilan" value="4">4 </label>
                        <label><input type="radio" name="tampilan" value="5">5 </label>
                        <label><input type="radio" name="tampilan" value="6">6 </label>
                        <label><input type="radio" name="tampilan" value="7">7 </label>
                        <label><input type="radio" name="tampilan" value="8">8 </label>
                        <label><input type="radio" name="tampilan" value="9">9 </label>
                        <label><input type="radio" name="tampilan" value="10">10 </label>
                    </div>
                </div>

                <!-- Penilaian Harga -->
                <div class="form-group">
                    <label for="harga">Penilaian harga:</label><br>
                    <div class="radio-group">
                        <label><input type="radio" name="harga" value="1" required>1 </label>
                        <label><input type="radio" name="harga" value="2">2 </label>
                        <label><input type="radio" name="harga" value="3">3 </label>
                        <label><input type="radio" name="harga" value="4">4 </label>
                        <label><input type="radio" name="harga" value="5">5 </label>
                        <label><input type="radio" name="harga" value="6">6 </label>
                        <label><input type="radio" name="harga" value="7">7 </label>
                        <label><input type="radio" name="harga" value="8">8 </label>
                        <label><input type="radio" name="harga" value="9">9 </label>
                        <label><input type="radio" name="harga" value="10">10 </label>
                    </div>
                </div>

                <input type="hidden" name="nilai_akhir" id="nilai_akhir_input">
                <input type="hidden" name="penilai" id="penilai" value="<?php echo $_SESSION['nama']; ?>">

                <button type="submit">Kirim Penilaian</button>
            </form>

            <!-- Nilai Rata-Rata dan Penilaian Akhir -->
            <div id="hasil-penilaian">
                <h2>Nilai Akhir: <span id="nilai-akhir">-</span></h2>
            </div>
        </main>
    </div>

    <script>
        // Menghitung nilai rata-rata berdasarkan pilihan
        const brandValue = document.getElementsByName('brand');
        const bahanValue = document.getElementsByName('bahan');
        const jahitanValue = document.getElementsByName('jahitan');
        const tampilanValue = document.getElementsByName('tampilan');
        const hargaValue = document.getElementsByName('harga');
    
        
        // Fungsi untuk menghitung skor dari pilihan
        function calculateAverage() {
        let totalScore = <?php echo $total_gambar;?> * 2.5;
        let totalItems = 1;
        //console.log(totalScore);
        
        const criteria = ['brand', 'bahan', 'jahitan', 'tampilan', 'harga'];
        //let total = 0;
        //let count = 0;
    
        // Loop melalui kriteria
        criteria.forEach(criteriaName => {
            const values = document.getElementsByName(criteriaName);
            for (let i = 0; i < values.length; i++) {
                if (values[i].checked) {
                    totalScore += parseInt(values[i].value);
                    totalItems++;
                    console.log("Total nilai: " + totalScore);
                    console.log("Total item: " + totalItems);
                    console.log("Rata-rata: " + totalScore/totalItems);
                    console.log("\n")
                    break;
                }
            }
        });

            if (totalItems > 0) {
                const average = totalScore / totalItems;
                let nilai;
                if (average >= 9) {
                    nilai = 'A';
                } else if (average >= 7) {
                    nilai = 'B';
                } else {
                    nilai = 'C';
                }

                document.getElementById("nilai-akhir").innerText = nilai;
                document.getElementById("nilai_akhir_input").value = nilai;
            }
        }

        // Memperbarui nilai saat pilihan berubah
        const allInputs = document.querySelectorAll('input[type="radio"]');
        allInputs.forEach(input => input.addEventListener('change', calculateAverage));
    </script>
</body>
</html>
