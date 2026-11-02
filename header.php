<?php
include "koneksi.php";
cek_login();
$q = mysqli_query($koneksi, "SELECT COUNT(*) AS n FROM barang WHERE stok <= stok_minimum");
$jumlah_peringatan = mysqli_fetch_assoc($q)['n'];
function aktif($nama) { global $halaman; return $halaman == $nama ? "aktif" : ""; }
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Inventura - Sistem Inventori Toko</title>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="menu">
  <div class="logo"><span class="logo-kotak"><?= logo_icon() ?></span> INVENTURA</div>
  <a class="tautan <?= aktif('dashboard') ?>" href="index.php">Dashboard</a>
  <a class="tautan <?= aktif('barang') ?>" href="barang.php">Data Barang</a>
  <a class="tautan <?= aktif('masuk') ?>" href="transaksi.php?jenis=Masuk">Barang Masuk</a>
  <a class="tautan <?= aktif('keluar') ?>" href="transaksi.php?jenis=Keluar">Barang Keluar</a>
  <a class="tautan <?= aktif('peringatan') ?>" href="peringatan.php">Peringatan Stok
    <?php if ($jumlah_peringatan > 0) { ?><span class="badge"><?= $jumlah_peringatan ?></span><?php } ?></a>
  <div class="kanan">
    <div class="avatar"><?= strtoupper(substr($_SESSION['nama_pegawai'], 0, 1)) ?></div>
    <div class="nama"><?= htmlspecialchars($_SESSION['nama_pegawai']) ?><small>Pegawai</small></div>
    <a class="btn-keluar" href="logout.php">&#x21AA; Keluar</a>
  </div>
</div>
<div class="isi">
