-- Jalankan ini di HeidiSQL kalau database sudah terlanjur ada (data lama).
-- Semua pegawai jadi punya password: password
USE db_monitoring_stok;
UPDATE pegawai SET password = '$2y$10$uiztlkt2gPzmmN/N/wucxufbsBSsaVX78N3xLjg/Tu7TMAg/tYziW';
