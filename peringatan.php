<?php
$halaman = "peringatan";
include "header.php";

$hasil = mysqli_query($koneksi, "SELECT * FROM barang WHERE stok <= stok_minimum ORDER BY stok ASC");
$n = mysqli_num_rows($hasil);
$daftar = array();
while ($b = mysqli_fetch_assoc($hasil)) { $daftar[] = $b; }

// barang yang dipilih untuk panel Tambah Stok Cepat
$pilih = isset($_GET['id_barang']) ? intval($_GET['id_barang']) : 0;
$dipilih = null;
foreach ($daftar as $b) { if ($b['id_barang'] == $pilih) { $dipilih = $b; } }
if ($dipilih == null && $n > 0) { $dipilih = $daftar[0]; }
?>
<div class="baris-bawah" style="margin:0 0 14px">
  <a class="kembali" style="margin:0" href="index.php">&larr; Kembali ke Dashboard</a>
  <span class="centang">&#9679; Sinkronisasi otomatis aktif</span>
</div>

<?php if ($n > 0) { ?>
<div class="banner-biru">
  <div class="segitiga">!</div>
  <div><b>Perhatian: Ada <?= $n ?> barang yang stoknya menipis atau habis!</b>
    <small>Segera beli atau tambah stok ke pemasok agar pembeli di toko tidak kehabisan.</small></div>
  <span class="pill pill-merah">&#9679; Perlu Tindakan</span>
</div>
<?php } else { ?>
<div class="sukses">Semua stok barang masih aman.</div>
<?php } ?>

<div class="dua-kolom">
  <div>
    <div class="judul-tabel" style="margin-top:0">
      <div><h3>Daftar Barang Segera Dipesan</h3><div class="bantu">Pilih tombol tambah stok untuk mengisi kuantitas terbaru.</div></div>
      <span><?= $n ?> Produk Kritis</span>
    </div>
    <div class="kartu" style="padding:6px">
      <table>
        <tr><th>Nama Barang</th><th>Sisa Stok</th><th>Batas Minimum</th><th>Status</th><th>Aksi Cepat</th></tr>
        <?php foreach ($daftar as $b) { $s = status_stok($b['stok'], $b['stok_minimum']); ?>
        <tr>
          <td class="tebal"><?= htmlspecialchars($b['nama_barang']) ?><small>SKU: <?= kode_barang($b['id_barang']) ?></small></td>
          <td class="<?= $b['stok'] == 0 ? 'merah-teks' : 'tebal' ?>"><?= $b['stok'] ?><small><?= $b['satuan'] ?></small></td>
          <td>Min <?= $b['stok_minimum'] ?><small><?= $b['satuan'] ?></small></td>
          <td><span class="pill <?= $b['stok'] == 0 ? 'pill-merah' : 'pill-biru' ?>">&#9679; <?= $b['stok'] == 0 ? 'Habis' : 'Menipis' ?></span></td>
          <td><a class="btn btn-kecil" href="peringatan.php?id_barang=<?= $b['id_barang'] ?>">+ Tambah Stok</a></td>
        </tr>
        <?php } ?>
      </table>
    </div>
    <div class="bantuan">
      <div><b>&#10067; Bagaimana batas dihitung?</b>Sistem memberi peringatan otomatis saat sisa barang berada di bawah batas minimum toko.</div>
      <div><b>&#128666; Pemasok Langganan</b>Stok bisa langsung diorder melalui kontak pemasok yang tersimpan di sistem.</div>
    </div>
  </div>

  <?php if ($dipilih != null) { ?>
  <div class="panel">
    <h3>+ Tambah Stok Cepat</h3>
    <small class="bantu">Barang yang Dipilih:</small>
    <div class="terpilih"><b><?= htmlspecialchars($dipilih['nama_barang']) ?></b>
      <div class="bantu">Stok sekarang: <b><?= $dipilih['stok'] ?> <?= $dipilih['satuan'] ?></b></div></div>
    <form method="post" action="transaksi.php?jenis=Masuk">
      <input type="hidden" name="id_barang" value="<?= $dipilih['id_barang'] ?>">
      <input type="hidden" name="tanggal" value="<?= date('Y-m-d') ?>">
      <input type="hidden" name="keterangan" value="Tambah stok cepat">
      <label style="margin-top:6px">Jumlah yang Ingin Ditambahkan:</label>
      <input type="number" id="tambah" name="jumlah" min="1" value="10" oninput="hitung()">
      <div class="bantu">Gunakan angka bulat sesuai satuan fisik barang.</div>
      <div class="hasil-hitung">
        <small>Hasil Perhitungan Otomatis:</small><br>
        Stok setelah ditambah: <b id="hasil"></b>
        <div class="centang" id="catatan-hasil" style="margin-top:4px"></div>
      </div>
      <button class="btn btn-penuh" type="submit" name="simpan">&#128190; Simpan Stok</button>
    </form>
  </div>
  <script>
  var stokSekarang = <?= $dipilih['stok'] ?>, minimum = <?= $dipilih['stok_minimum'] ?>, satuan = "<?= $dipilih['satuan'] ?>";
  function hitung() {
    var t = parseInt(document.getElementById('tambah').value) || 0;
    var h = stokSekarang + t;
    document.getElementById('hasil').textContent = h + " " + satuan;
    document.getElementById('catatan-hasil').textContent = h > minimum ? "\u2713 Stok akan kembali aman (di atas batas minimum " + minimum + ")" : "Stok masih di bawah batas minimum (" + minimum + ")";
  }
  hitung();
  </script>
  <?php } ?>
</div>
<?php include "footer.php"; ?>
