<?php
include "koneksi.php";
$error = "";

if (isset($_POST['masuk'])) {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = $_POST['password'];

    $q = mysqli_query($koneksi, "SELECT * FROM pegawai WHERE username = '$username'");
    $user = mysqli_fetch_assoc($q);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['id_pegawai'] = $user['id_pegawai'];
        $_SESSION['nama_pegawai'] = $user['nama_pegawai'];
        header("Location: index.php");
        exit;
    } else {
        $error = "Nama pengguna atau kata sandi salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login - Inventura</title>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="latar-login">
  <div class="kotak-login">
    <div class="logo-kotak"><?= logo_icon(24) ?></div>
    <h1>Masuk ke Inventura</h1>
    <p class="sub">Masukkan akun Anda untuk mulai mengelola stok toko.</p>
    <?php if ($error != "") { ?><div class="error"><?= $error ?></div><?php } ?>
    <form method="post">
      <label>Nama Pengguna <span class="wajib">*</span></label>
      <div class="kolom-ikon"><span>&#128100;</span>
        <input type="text" name="username" placeholder="Masukkan nama pengguna" required></div>
      <label>Kata Sandi <span class="wajib">*</span></label>
      <div class="kolom-ikon"><span>&#128274;</span>
        <input type="password" name="password" placeholder="Masukkan kata sandi" required></div>
      <div class="baris-ingat">
        <label style="margin:0;text-transform:none;font-weight:500"><input type="checkbox"> Ingat saya</label>
        <a href="#" onclick="alert('Silakan hubungi Pemilik Toko untuk reset kata sandi.');return false;">Lupa kata sandi?</a>
      </div>
      <button class="btn btn-penuh" type="submit" name="masuk">Masuk</button>
    </form>
    <div class="bantuan-login">Butuh bantuan? <a href="#" onclick="alert('Hubungi Pemilik Toko.');return false;">Hubungi Pemilik Toko</a></div>
  </div>
  <p class="footer">&copy; 2026 INVENTURA. Sistem Inventori Toko.</p>
</div>
</body>
</html>
