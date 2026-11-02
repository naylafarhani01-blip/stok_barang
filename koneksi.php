<?php
session_start();
mysqli_report(MYSQLI_REPORT_OFF);

$koneksi = mysqli_connect("localhost", "root", "Nayla261106", "db_umkm");
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// cek apakah sudah login
function cek_login() {
    if (!isset($_SESSION['id_pegawai'])) {
        header("Location: login.php");
        exit;
    }
}

// menentukan status stok
function status_stok($stok, $min) {
    if ($stok <= 0) return array("Habis", "habis");
    if ($stok <= $min) return array("Stok Menipis", "menipis");
    return array("Tersedia", "aman");
}

function logo_icon($ukuran = 18) {
    return '<svg width="'.$ukuran.'" height="'.$ukuran.'" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linejoin="round" stroke-linecap="round"><path d="M21 8l-9-5-9 5v8l9 5 9-5V8z"/><path d="M3 8l9 5 9-5M12 13v8"/></svg>';
}
function kode_barang($id) {
    return "BRG-" . str_pad($id, 3, "0", STR_PAD_LEFT);
}
?>
