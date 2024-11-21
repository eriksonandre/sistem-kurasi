<?php
	include("connection.php");
	ob_start();
	session_start();

	if($_SERVER["REQUEST_METHOD"] == "POST")
	{
		$username = $_POST['username'];
		$password = $_POST['password'];

		if(empty($username) || empty($password))
		{
			echo "<script>console.log('Username atau Password kosong');</script>";
			echo "<script>alert('Username atau Password tidak boleh kosong');</script>";
		}else
		{
			$sql = "SELECT * FROM user WHERE username = '$username'";
			$result = mysqli_query($connection, $sql);
			//Cek apakah ada hasilnya
			if(mysqli_num_rows($result) > 0){
				//Pisahkan hasil sesuai kolom
				$row = mysqli_fetch_assoc($result);	
				
				if($row['password'] == md5($password))
				{
					echo "<script> console.log('Berhasil login');</script>";

					echo "<script> alert('Berhasil login');</script>";

					$_SESSION['username'] = $row['username'];
					$_SESSION['level'] = $row['level'];
					$_SESSION['nama'] = $row['nama'];
					$level = $row['level'];
					
					if($level == "Admin")
					{
						header("Location: home-admin.php");
                        exit();

					}else if($level == "Kurator")
					{
						header("Location: home-kurator.php");
                        exit();

					}
					exit();
				}else
				{
					echo "<script> console.log('Tidak berhasil login');</script>";
					//echo $row['username'] . " " . $row['password'];
					echo "<script> alert('Tidak berhasil login');</script>";
				}
			}else{
				echo "<script> console.log('Tidak ada data user');</script>";
				echo "<script> alert('Akun tidak ada');</script>";
			}
		}
	}
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Login</title>
	<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
	<div class="login-container">
		<h1>Login</h1>
		<form action="index.php" method="POST">
			<label for="usm">Username:</label>
			<input type="text" id="usm" name="username">
			
			<label for="pw">Password:</label>
			<input type="password" id="pw" name="password">
			
			<input type="submit" name="login" value="Login">
		</form>
	</div>
</body>
</html>
