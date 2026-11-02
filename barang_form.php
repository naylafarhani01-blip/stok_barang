<?php
$halaman = "barang";
include "header.php";

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$error = "";

// data awal (kosong kalau tambah baru)
$b = array("nama_barang" => "", "id_kategori" => "", "satuan" => "", "stok" => 0, "stok_minimum" => 5);
if ($id > 0) {
    $b = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM barang WHERE id_barang = $id"));
}

if (isset($_POST['simpan'])) {
    $nama = mysqli_real_escape_string($koneksi, trim($_POST['nama_barang']));
    $kategori = intval($_POST['id_kategori']);
    $satuan = mysqli_real_escape_string($koneksi, trim($_POST['satuan']));
    $stok = intval($_POST['stok']);
    $min = intval($_POST['stok_minimum']);

    if ($nama == "" || $kategori == 0 || $satuan == "") {
        $error = "Data wajib belum lengkap!";
    } else {
        if ($id > 0) {
            $sql = "UPDATE barang SET nama_barang='$nama', id_kategori=$kategori, satuan='$satuan',
                    stok=$stok, stok_minimum=$min WHERE id_barang=$id";
        } else {
            $sql = "INSERT INTO barang (id_kategori, nama_barang, satuan, stok, stok_minimum)
                    VALUES ($kategori, '$nama', '$satuan', $stok, $min)";
        }
        mysqli_query($koneksi, $sql);
        header("Location: barang.php?pesan=Data barang berhasil disimpan");
        exit;
    }
    // kalau error, tampilkan lagi isian tadi
    $b = array("nama_barang" => $_POST['nama_barang'], "id_kategori" => $kategori,
               "satuan" => $_POST['satuan'], "stok" => $stok, "stok_minimum" => $min);
}
$kategori_list = mysqli_query($koneksi, "SELECT * FROM kategori");
?>
<a class="kembali" href="barang.php">&larr; Kembali ke Data Barang</a>
<div style="max-width:620px;margin:0 auto">
<h1><?= $id > 0 ? "Ubah Barang" : "Tambah Barang Baru" ?></h1>
<p class="sub">Masukkan nama barang dan jumlah stok yang ada di toko dengan teliti.</p>

<div class="kartu">
  <?php if ($error != "") { ?><div class="error"><?= $error ?></div><?php } ?>
  <form method="post">
    <div class="judul-bagian"><span class="ikon-kecil">&#9635;</span> 1. Informasi Barang</div>
    <label>Nama Barang <span class="wajib">*</span></label>
    <input type="text" name="nama_barang" placeholder="Contoh: Beras Ramos 5 kg" value="<?= htmlspecialchars($b['nama_barang']) ?>">
    <div class="bantu">Tuliskan merek, ukuran, atau varian rasa jika ada.</div>

    <div class="dua">
      <div>
        <label>Kategori</label>
        <select name="id_kategori">
          <option value="0">-- Pilih kategori --</option>
          <?php while ($k = mysqli_fetch_assoc($kategori_list)) { ?>
            <option value="<?= $k['id_kategori'] ?>" <?= $k['id_kategori'] == $b['id_kategori'] ? "selected" : "" ?>><?= $k['nama_kategori'] ?></option>
          <?php } ?>
        </select>
      </div>
      <div>
        <label>Satuan</label>
        <select name="satuan">
          <?php foreach (array("pcs","botol","buah","bungkus","sak","pouch","kg","dus") as $st) { ?>
            <option value="<?= $st ?>" <?= $st == $b['satuan'] ? "selected" : "" ?>><?= $st ?></option>
          <?php } ?>
        </select>
      </div>
    </div>

    <hr class="garis">
    <div class="judul-bagian"><span class="ikon-kecil">&#9783;</span> 2. Catatan Jumlah Stok</div>
    <div class="dua">
      <div>
        <label>Jumlah Stok Saat Ini <span class="wajib">*</span></label>
        <div class="stepper"><button type="button" onclick="ubah('stok',-1)">&minus;</button>
          <input type="number" id="stok" name="stok" min="0" value="<?= $b['stok'] ?>">
          <button type="button" onclick="ubah('stok',1)">+</button></div>
        <div class="bantu">Total fisik barang yang tersedia saat ini di toko.</div>
      </div>
      <div>
        <label>Batas Peringatan Habis <span class="wajib">*</span></label>
        <div class="stepper"><button type="button" onclick="ubah('min',-1)">&minus;</button>
          <input type="number" id="min" name="stok_minimum" min="0" value="<?= $b['stok_minimum'] ?>">
          <button type="button" onclick="ubah('min',1)">+</button></div>
        <div class="bantu">Sistem akan memberi tahu jika stok sudah tersisa sebanyak ini.</div>
      </div>
    </div>

    <div class="baris-bawah">
      <a class="btn btn-abu" href="barang.php">Batal</a>
      <button class="btn" type="submit" name="simpan">&#10003; Simpan Barang</button>
    </div>
  </form>
</div>
</div>
<script>
function ubah(id, n) {
  var e = document.getElementById(id);
  e.value = Math.max(0, (parseInt(e.value) || 0) + n);
}
</script>
<?php include "footer.php"; ?>
