<?php
include "koneksi.php";
cek_login();

$id = intval($_GET['id']);
$ok = mysqli_query($koneksi, "DELETE FROM barang WHERE id_barang = $id");

if ($ok) {
    header("Location: barang.php?pesan=Barang berhasil dihapus");
} else {
    // gagal karena barang sudah dipakai di transaksi (foreign key)
    header("Location: barang.php?error=Barang tidak bisa dihapus karena sudah ada di riwayat transaksi");
}
