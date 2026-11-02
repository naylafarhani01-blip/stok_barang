CREATE DATABASE IF NOT EXISTS db_monitoring_stok;
USE db_monitoring_stok;

CREATE TABLE kategori (
  id_kategori INT AUTO_INCREMENT PRIMARY KEY,
  nama_kategori VARCHAR(100) NOT NULL
);
CREATE TABLE barang (
  id_barang INT AUTO_INCREMENT PRIMARY KEY,
  id_kategori INT NOT NULL,
  nama_barang VARCHAR(100) NOT NULL,
  satuan VARCHAR(20) NOT NULL,
  stok INT NOT NULL,
  stok_minimum INT NOT NULL,
  FOREIGN KEY (id_kategori) REFERENCES kategori(id_kategori)
);
CREATE TABLE pegawai (
  id_pegawai INT AUTO_INCREMENT PRIMARY KEY,
  nama_pegawai VARCHAR(100) NOT NULL,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL
);
CREATE TABLE transaksi (
  id_transaksi INT AUTO_INCREMENT PRIMARY KEY,
  id_pegawai INT NOT NULL,
  tanggal DATE NOT NULL,
  jenis_transaksi VARCHAR(10) NOT NULL,
  keterangan VARCHAR(255) NULL,
  FOREIGN KEY (id_pegawai) REFERENCES pegawai(id_pegawai)
);
CREATE TABLE detail_transaksi (
  id_detail INT AUTO_INCREMENT PRIMARY KEY,
  id_transaksi INT NOT NULL,
  id_barang INT NOT NULL,
  jumlah INT NOT NULL,
  FOREIGN KEY (id_transaksi) REFERENCES transaksi(id_transaksi),
  FOREIGN KEY (id_barang) REFERENCES barang(id_barang)
);

INSERT INTO kategori VALUES (1,'Makanan'),(2,'Minuman'),(3,'Alat Tulis'),(4,'Kebutuhan Rumah'),(5,'Perawatan Tubuh');

INSERT INTO barang VALUES
(1,1,'Mi Instan','pcs',100,20),
(2,2,'Air Mineral','botol',80,15),
(3,3,'Buku Tulis','buah',0,10),
(4,4,'Sabun Cuci','pcs',4,5),
(5,5,'Sabun Mandi','pcs',40,10);

-- password semua akun: password
INSERT INTO pegawai VALUES
(1,'Andi','andi','$2y$10$uiztlkt2gPzmmN/N/wucxufbsBSsaVX78N3xLjg/Tu7TMAg/tYziW'),
(2,'Budi','budi','$2y$10$uiztlkt2gPzmmN/N/wucxufbsBSsaVX78N3xLjg/Tu7TMAg/tYziW');

INSERT INTO transaksi VALUES
(1,1,CURDATE(),'Masuk','Stok awal'),
(2,2,CURDATE(),'Keluar','Pengeluaran barang');
INSERT INTO detail_transaksi VALUES (1,1,1,20),(2,2,2,5);
