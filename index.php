<?php
$halaman = "dashboard";
include "header.php";

$jenis = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS n FROM barang"))['n'];
$masuk = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT IFNULL(SUM(d.jumlah),0) AS n FROM detail_transaksi d
    JOIN transaksi t ON d.id_transaksi = t.id_transaksi
    WHERE t.jenis_transaksi='Masuk' AND t.tanggal = CURDATE()"))['n'];
$keluar = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT IFNULL(SUM(d.jumlah),0) AS n FROM detail_transaksi d
    JOIN transaksi t ON d.id_transaksi = t.id_transaksi
    WHERE t.jenis_transaksi='Keluar' AND t.tanggal = CURDATE()"))['n'];
$mau_habis = $jumlah_peringatan;

$cari = isset($_GET['cari']) ? mysqli_real_escape_string($koneksi, $_GET['cari']) : "";
$hasil = mysqli_query($koneksi, "SELECT * FROM barang WHERE nama_barang LIKE '%$cari%' ORDER BY (stok <= stok_minimum) DESC, stok ASC LIMIT 10");
?>
<h1>Halo, <?= htmlspecialchars($_SESSION['nama_pegawai']) ?>! &#128075;</h1>
<p class="sub">Berikut catatan stok barang di toko hari ini.</p>

<div class="aksi-besar">
  <a class="biru" href="transaksi.php?jenis=Masuk">
    <div class="lingkar">+</div>
    <div><b>+ Catat Barang Masuk</b><span>Klik untuk tambah barang yang baru datang</span></div>
  </a>
  <a class="hijau" href="transaksi.php?jenis=Keluar">
    <div class="lingkar">&rarr;</div>
    <div><b>+ Catat Barang Keluar</b><span>Klik saat barang laku terjual atau dipindahkan</span></div>
  </a>
</div>

<div class="statistik">
  <div class="stat"><small>Total Barang di Toko</small><b><?= $jenis ?></b><span>Macam barang</span></div>
  <div class="stat hijau"><small>Barang Masuk Hari Ini</small><b>+<?= $masuk ?></b><span>Total bungkus/kaleng diterima</span></div>
  <div class="stat"><small>Barang Keluar Hari Ini</small><b><?= $keluar ?></b><span>Barang terjual keluar</span></div>
  <div class="stat kuning"><small>Barang Mau Habis</small><b><?= $mau_habis ?></b><span>Perlu segera dibeli lagi</span></div>
</div>

<?php if ($mau_habis > 0) { ?>
<div class="banner">&#9432; Ada <b><?= $mau_habis ?> barang</b> yang stoknya sedikit atau habis. Segera pesan kembali agar pembeli tidak kehabisan. <a href="peringatan.php">Lihat peringatan</a></div>
<?php } ?>

<div class="kartu" style="margin-top:14px">
  <form method="get" class="cari" style="justify-content:space-between">
    <div><b style="font-size:16px">Daftar Stok Barang</b><div class="bantu">Menampilkan barang yang paling perlu diperhatikan</div></div>
    <input type="text" name="cari" placeholder="Ketik nama barang..." value="<?= htmlspecialchars($cari) ?>">
  </form>
  <table>
    <tr><th>Nama Barang</th><th>Sisa Stok</th><th>Status</th><th style="text-align:right">Aksi</th></tr>
    <?php while ($b = mysqli_fetch_assoc($hasil)) { $s = status_stok($b['stok'], $b['stok_minimum']); ?>
    <tr>
      <td class="tebal"><?= htmlspecialchars($b['nama_barang']) ?></td>
      <td><b><?= $b['stok'] ?></b> <small style="display:inline"><?= $b['satuan'] ?></small></td>
      <td><span class="pill <?= $s[1] ?>"><?= $s[0] ?></span></td>
      <td style="text-align:right"><a class="btn btn-kecil btn-abu" href="transaksi.php?jenis=Masuk&id_barang=<?= $b['id_barang'] ?>">+ Tambah Stok</a></td>
    </tr>
    <?php } ?>
  </table>
</div>
<?php include "footer.php"; ?>
