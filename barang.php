<?php
$halaman = "barang";
include "header.php";

$cari = isset($_GET['cari']) ? mysqli_real_escape_string($koneksi, $_GET['cari']) : "";
$id_kat = isset($_GET['kategori']) ? intval($_GET['kategori']) : 0;

$where = "WHERE b.nama_barang LIKE '%$cari%'";
if ($id_kat > 0) { $where .= " AND b.id_kategori = $id_kat"; }

$hasil = mysqli_query($koneksi, "SELECT b.*, k.nama_kategori FROM barang b
    JOIN kategori k ON b.id_kategori = k.id_kategori $where ORDER BY b.id_barang");
$jumlah = mysqli_num_rows($hasil);
$kategori = mysqli_query($koneksi, "SELECT * FROM kategori");
?>
<div class="baris-bawah" style="margin:0 0 16px">
  <div><h1>Daftar Barang</h1><p class="sub" style="margin:0">Semua barang yang tersimpan di toko Anda.</p></div>
  <a class="btn" href="barang_form.php">+ Tambah Barang Baru</a>
</div>

<?php if (isset($_GET['pesan'])) { ?><div class="sukses"><?= htmlspecialchars($_GET['pesan']) ?></div><?php } ?>
<?php if (isset($_GET['error'])) { ?><div class="error"><?= htmlspecialchars($_GET['error']) ?></div><?php } ?>

<div class="kartu">
  <form method="get" class="cari">
    <input type="text" name="cari" placeholder="Cari nama barang..." value="<?= htmlspecialchars($cari) ?>">
    <?php if ($id_kat > 0) { ?><input type="hidden" name="kategori" value="<?= $id_kat ?>"><?php } ?>
    <button class="btn btn-abu" type="submit">Cari</button>
    <div class="filter">KATEGORI:
      <a class="chip <?= $id_kat == 0 ? 'aktif' : '' ?>" href="barang.php">Semua</a>
      <?php while ($k = mysqli_fetch_assoc($kategori)) { ?>
        <a class="chip <?= $id_kat == $k['id_kategori'] ? 'aktif' : '' ?>" href="barang.php?kategori=<?= $k['id_kategori'] ?>"><?= $k['nama_kategori'] ?></a>
      <?php } ?>
    </div>
  </form>

  <table>
    <tr><th>No</th><th>Kode</th><th>Nama Barang</th><th>Kategori</th><th>Jumlah Stok</th><th>Status</th><th>Pilihan</th></tr>
    <?php $no = 1; while ($b = mysqli_fetch_assoc($hasil)) { $s = status_stok($b['stok'], $b['stok_minimum']); ?>
    <tr>
      <td><?= $no++ ?></td>
      <td class="kode"><?= kode_barang($b['id_barang']) ?></td>
      <td class="tebal"><?= htmlspecialchars($b['nama_barang']) ?></td>
      <td><?= $b['nama_kategori'] ?></td>
      <td class="tebal"><?= $b['stok'] ?> <?= $b['satuan'] ?></td>
      <td><span class="pill <?= $s[1] ?>"><?= $s[0] ?></span></td>
      <td>
        <a class="btn-edit" href="barang_form.php?id=<?= $b['id_barang'] ?>">Edit</a>
        <a class="btn-hapus" href="barang_hapus.php?id=<?= $b['id_barang'] ?>" onclick="return confirm('Yakin hapus barang ini?')">Hapus</a>
      </td>
    </tr>
    <?php } ?>
  </table>
  <div class="halaman">
    <span>Menampilkan <b><?= $jumlah ?></b> barang</span>
    <div class="nomor"><span>Sebelumnya</span><span class="aktif">1</span><span>Selanjutnya</span></div>
  </div>
</div>
<?php include "footer.php"; ?>
