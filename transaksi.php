<?php
include "header.php";

// halaman ini dipakai untuk Barang Masuk dan Barang Keluar
$jenis = (isset($_GET['jenis']) && $_GET['jenis'] == "Keluar") ? "Keluar" : "Masuk";
$halaman = strtolower($jenis);
$pilih = isset($_GET['id_barang']) ? intval($_GET['id_barang']) : 0;
$pesan = "";
$error = "";

if (isset($_POST['simpan'])) {
    $id_barang = intval($_POST['id_barang']);
    $jumlah = intval($_POST['jumlah']);
    $tanggal = mysqli_real_escape_string($koneksi, $_POST['tanggal']);
    $ket = mysqli_real_escape_string($koneksi, $_POST['keterangan']);
    $pilih = $id_barang;

    $barang = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM barang WHERE id_barang = $id_barang"));

    if (!$barang || $jumlah <= 0 || $tanggal == "") {
        $error = "Barang, jumlah, dan tanggal wajib diisi dengan benar.";
    } elseif ($jenis == "Keluar" && $jumlah > $barang['stok']) {
        $error = "Jumlah melebihi stok! Stok tersedia: " . $barang['stok'];
    } else {
        $id_pegawai = $_SESSION['id_pegawai'];
        // 1. simpan transaksi
        mysqli_query($koneksi, "INSERT INTO transaksi (id_pegawai, tanggal, jenis_transaksi, keterangan)
                                VALUES ($id_pegawai, '$tanggal', '$jenis', '$ket')");
        $id_transaksi = mysqli_insert_id($koneksi);
        // 2. simpan detail transaksi
        mysqli_query($koneksi, "INSERT INTO detail_transaksi (id_transaksi, id_barang, jumlah)
                                VALUES ($id_transaksi, $id_barang, $jumlah)");
        // 3. update stok otomatis
        if ($jenis == "Masuk") {
            mysqli_query($koneksi, "UPDATE barang SET stok = stok + $jumlah WHERE id_barang = $id_barang");
            $stok_baru = $barang['stok'] + $jumlah;
        } else {
            mysqli_query($koneksi, "UPDATE barang SET stok = stok - $jumlah WHERE id_barang = $id_barang");
            $stok_baru = $barang['stok'] - $jumlah;
        }
        $pesan = "Barang " . strtolower($jenis) . " berhasil disimpan. Stok " . $barang['nama_barang'] . " sekarang: $stok_baru " . $barang['satuan'] . ".";
        $pilih = 0;
    }
}

$daftar = mysqli_query($koneksi, "SELECT * FROM barang ORDER BY nama_barang");
$riwayat = mysqli_query($koneksi, "SELECT t.tanggal, b.nama_barang, b.satuan, d.jumlah, t.keterangan, p.nama_pegawai
    FROM transaksi t
    JOIN detail_transaksi d ON t.id_transaksi = d.id_transaksi
    JOIN barang b ON d.id_barang = b.id_barang
    JOIN pegawai p ON t.id_pegawai = p.id_pegawai
    WHERE t.jenis_transaksi = '$jenis'
    ORDER BY t.id_transaksi DESC LIMIT 5");
$total_transaksi = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS n FROM transaksi WHERE jenis_transaksi='$jenis'"))['n'];
?>
<?php if ($jenis == "Keluar") { ?><small style="color:#1d4ed8;font-weight:700">&#9654; AKTIVITAS TOKO</small><?php } ?>
<h1>Catat Barang <?= $jenis ?></h1>
<p class="sub"><?= $jenis == "Masuk" ? "Catat stok barang yang baru dibeli atau baru datang dari pemasok." : "Catat barang yang terjual atau keluar dari toko secara rapi dan akurat." ?></p>

<?php if ($pesan != "") { ?><div class="sukses">&#10003; <?= $pesan ?></div><?php } ?>
<?php if ($error != "") { ?><div class="error"><?= $error ?></div><?php } ?>

<div class="kartu input-biru">
  <?php if ($jenis == "Keluar") { ?>
    <div class="kepala-form"><span class="ikon-kecil">&#9783;</span> Formulir Pengurangan Stok <span class="tag-wajib">Wajib Diisi</span></div>
  <?php } ?>
  <form method="post">
    <label style="display:flex;justify-content:space-between">
      <span>Pilih Barang <span class="wajib">*</span></span>
      <span id="info-stok" style="text-transform:none;font-weight:400;color:#64748b">Pilih barang untuk melihat stok</span></label>
    <select name="id_barang" id="pilih-barang" onchange="tampilStok()">
      <option value="0" data-stok="" data-satuan="">-- Pilih nama barang dari stok --</option>
      <?php while ($b = mysqli_fetch_assoc($daftar)) { ?>
        <option value="<?= $b['id_barang'] ?>" data-stok="<?= $b['stok'] ?>" data-satuan="<?= $b['satuan'] ?>" <?= $b['id_barang'] == $pilih ? "selected" : "" ?>>
          <?= htmlspecialchars($b['nama_barang']) ?>
        </option>
      <?php } ?>
    </select>

    <div class="dua">
      <div>
        <label>Jumlah yang <?= $jenis ?> <span class="wajib">*</span></label>
        <input type="number" name="jumlah" min="1" placeholder="Contoh: 20">
      </div>
      <div>
        <label>Tanggal <?= $jenis ?> <span class="wajib">*</span></label>
        <input type="date" name="tanggal" value="<?= date('Y-m-d') ?>">
      </div>
    </div>

    <?php if ($jenis == "Keluar") { ?>
      <label>Keperluan / Keterangan <span class="wajib">*</span></label>
      <select name="keterangan">
        <option>Penjualan Toko (Langsung ke pembeli)</option>
        <option>Pesanan Pelanggan</option>
        <option>Barang Rusak / Kadaluarsa</option>
        <option>Pemakaian Sendiri</option>
      </select>
      <div class="catatan"><b>&#9432; Catatan Penting:</b> Jumlah keluar tidak boleh melebihi stok yang ada di toko. Stok akan otomatis terpotong begitu Anda menekan tombol simpan.</div>
      <div style="text-align:right"><button class="btn btn-tua" type="submit" name="simpan">&#10003; Simpan Barang Keluar</button></div>
    <?php } else { ?>
      <label>Catatan / Dari Mana <small style="text-transform:none;font-weight:400">(Opsional)</small></label>
      <input type="text" name="keterangan" placeholder="Contoh: Beli dari Toko Grosir Jaya">
      <div class="baris-bawah">
        <span class="centang">&#10003; Stok di toko akan otomatis bertambah setelah disimpan.</span>
        <button class="btn" type="submit" name="simpan">&#128190; Simpan Barang Masuk</button>
      </div>
    <?php } ?>
  </form>
</div>

<div class="judul-tabel">
  <h3>Riwayat Barang <?= $jenis ?> Terakhir</h3>
  <span>Total Dicatat: <?= $total_transaksi ?> Transaksi</span>
</div>
<div class="kartu" style="padding:6px">
<table>
  <tr><th>Tanggal</th><th>Nama Barang</th><th>Jumlah <?= $jenis ?></th><th>Keterangan</th><th>Dicatat Oleh</th></tr>
  <?php while ($r = mysqli_fetch_assoc($riwayat)) { ?>
  <tr>
    <td><?= date('d/m/Y', strtotime($r['tanggal'])) ?></td>
    <td class="tebal"><?= htmlspecialchars($r['nama_barang']) ?></td>
    <td class="tebal"><?= $jenis == "Masuk" ? "+" : "-" ?><?= $r['jumlah'] ?> <?= $r['satuan'] ?></td>
    <td><?= htmlspecialchars($r['keterangan']) ?></td>
    <td><?= htmlspecialchars($r['nama_pegawai']) ?></td>
  </tr>
  <?php } ?>
</table>
</div>
<script>
function tampilStok() {
  var o = document.getElementById('pilih-barang').selectedOptions[0];
  var info = document.getElementById('info-stok');
  if (o.dataset.stok === "") { info.textContent = "Pilih barang untuk melihat stok"; }
  else { info.textContent = "Stok tersedia: " + o.dataset.stok + " " + o.dataset.satuan; }
}
tampilStok();
</script>
<?php include "footer.php"; ?>
