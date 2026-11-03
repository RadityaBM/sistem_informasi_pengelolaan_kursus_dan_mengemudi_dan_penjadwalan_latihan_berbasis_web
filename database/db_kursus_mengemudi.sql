-- Database Sistem Informasi Pengelolaan Kursus Mengemudi (rancangan Modul 5)
-- Jalankan HANYA jika database dari Modul 5 belum ada. Password semua akun contoh: password123
DROP DATABASE IF EXISTS db_kursus_mengemudi;
CREATE DATABASE db_kursus_mengemudi CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE db_kursus_mengemudi;

CREATE TABLE pengguna (
  id_pengguna   INT AUTO_INCREMENT PRIMARY KEY,
  username      VARCHAR(50)  NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role          ENUM('admin','pelatih','siswa','owner') NOT NULL,
  status_aktif  BOOLEAN NOT NULL DEFAULT TRUE
) ENGINE=InnoDB;

CREATE TABLE siswa (
  id_siswa      INT AUTO_INCREMENT PRIMARY KEY,
  id_pengguna   INT NOT NULL UNIQUE,
  nama          VARCHAR(100) NOT NULL,
  nomor_telepon VARCHAR(20)  NOT NULL,
  alamat        TEXT NULL,
  CONSTRAINT fk_siswa_pengguna FOREIGN KEY (id_pengguna) REFERENCES pengguna (id_pengguna) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE pelatih (
  id_pelatih    INT AUTO_INCREMENT PRIMARY KEY,
  id_pengguna   INT NOT NULL UNIQUE,
  nama          VARCHAR(100) NOT NULL,
  nomor_telepon VARCHAR(20)  NOT NULL,
  status_aktif  BOOLEAN NOT NULL DEFAULT TRUE,
  CONSTRAINT fk_pelatih_pengguna FOREIGN KEY (id_pengguna) REFERENCES pengguna (id_pengguna) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE mobil (
  id_mobil     INT AUTO_INCREMENT PRIMARY KEY,
  plat_nomor   VARCHAR(15)  NOT NULL UNIQUE,
  merek_model  VARCHAR(100) NOT NULL,
  transmisi    ENUM('manual','matic') NOT NULL,
  status       ENUM('tersedia','perawatan') NOT NULL DEFAULT 'tersedia'
) ENGINE=InnoDB;

CREATE TABLE paket_kursus (
  id_paket     INT AUTO_INCREMENT PRIMARY KEY,
  nama_paket   VARCHAR(100) NOT NULL,
  transmisi    ENUM('manual','matic') NOT NULL,
  jumlah_sesi  INT NOT NULL,
  harga        DECIMAL(12,2) NOT NULL,
  status_aktif BOOLEAN NOT NULL DEFAULT TRUE,
  CONSTRAINT ck_paket_sesi  CHECK (jumlah_sesi > 0),
  CONSTRAINT ck_paket_harga CHECK (harga >= 0)
) ENGINE=InnoDB;

CREATE TABLE pendaftaran (
  id_pendaftaran INT AUTO_INCREMENT PRIMARY KEY,
  id_siswa       INT NOT NULL,
  id_paket       INT NOT NULL,
  tanggal_daftar DATE NOT NULL,
  total_sesi     INT NOT NULL,
  sesi_terpakai  INT NOT NULL DEFAULT 0,
  harga_dibayar  DECIMAL(12,2) NOT NULL,
  status         ENUM('aktif','selesai','nonaktif') NOT NULL DEFAULT 'aktif',
  CONSTRAINT fk_pendaftaran_siswa FOREIGN KEY (id_siswa) REFERENCES siswa (id_siswa) ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_pendaftaran_paket FOREIGN KEY (id_paket) REFERENCES paket_kursus (id_paket) ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT ck_pendaftaran_kuota CHECK (sesi_terpakai <= total_sesi)
) ENGINE=InnoDB;

CREATE TABLE slot_jadwal (
  id_slot     INT AUTO_INCREMENT PRIMARY KEY,
  id_pelatih  INT NOT NULL,
  id_mobil    INT NOT NULL,
  tanggal     DATE NOT NULL,
  jam_mulai   TIME NOT NULL,
  jam_selesai TIME NOT NULL,
  status      ENUM('tersedia','terpesan') NOT NULL DEFAULT 'tersedia',
  CONSTRAINT fk_slot_pelatih FOREIGN KEY (id_pelatih) REFERENCES pelatih (id_pelatih) ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_slot_mobil   FOREIGN KEY (id_mobil)   REFERENCES mobil (id_mobil)     ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT uq_slot_pelatih UNIQUE (id_pelatih, tanggal, jam_mulai),
  CONSTRAINT uq_slot_mobil   UNIQUE (id_mobil, tanggal, jam_mulai),
  CONSTRAINT ck_slot_jam CHECK (jam_selesai > jam_mulai)
) ENGINE=InnoDB;

CREATE TABLE booking (
  id_booking        INT AUTO_INCREMENT PRIMARY KEY,
  id_slot           INT NOT NULL,
  id_pendaftaran    INT NOT NULL,
  waktu_booking     DATETIME NOT NULL,
  status            ENUM('terjadwal','selesai','dibatalkan') NOT NULL DEFAULT 'terjadwal',
  waktu_selesai     DATETIME NULL,
  dikonfirmasi_oleh INT NULL,
  CONSTRAINT fk_booking_slot        FOREIGN KEY (id_slot)           REFERENCES slot_jadwal (id_slot)       ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_booking_pendaftaran FOREIGN KEY (id_pendaftaran)    REFERENCES pendaftaran (id_pendaftaran) ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_booking_konfirmasi  FOREIGN KEY (dikonfirmasi_oleh) REFERENCES pengguna (id_pengguna)       ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ===== DATA CONTOH =====
INSERT INTO pengguna (username, password_hash, role) VALUES
 ('admin',   '$2y$10$NBUOmkzY0Zf459N1s214X.hiOV1F21KQ4lWYxVctE5pYl06hUDCoq', 'admin'),
 ('owner',   '$2y$10$NBUOmkzY0Zf459N1s214X.hiOV1F21KQ4lWYxVctE5pYl06hUDCoq', 'owner'),
 ('pelatih1','$2y$10$NBUOmkzY0Zf459N1s214X.hiOV1F21KQ4lWYxVctE5pYl06hUDCoq', 'pelatih'),
 ('pelatih2','$2y$10$NBUOmkzY0Zf459N1s214X.hiOV1F21KQ4lWYxVctE5pYl06hUDCoq', 'pelatih'),
 ('siswa1',  '$2y$10$NBUOmkzY0Zf459N1s214X.hiOV1F21KQ4lWYxVctE5pYl06hUDCoq', 'siswa'),
 ('siswa2',  '$2y$10$NBUOmkzY0Zf459N1s214X.hiOV1F21KQ4lWYxVctE5pYl06hUDCoq', 'siswa');

INSERT INTO pelatih (id_pengguna, nama, nomor_telepon) VALUES
 (3, 'Pak Budi Santoso', '081234567801'),
 (4, 'Pak Andi Wijaya',  '081234567802');

INSERT INTO siswa (id_pengguna, nama, nomor_telepon, alamat) VALUES
 (5, 'Ahmad Fauzi',  '082111111111', 'Jl. Merdeka No. 10, Malang'),
 (6, 'Siti Aisyah',  '082222222222', NULL);

INSERT INTO mobil (plat_nomor, merek_model, transmisi, status) VALUES
 ('N 1234 AB', 'Toyota Agya',   'manual', 'tersedia'),
 ('N 5678 CD', 'Honda Brio',    'matic',  'tersedia'),
 ('N 9012 EF', 'Daihatsu Ayla', 'manual', 'perawatan');

INSERT INTO paket_kursus (nama_paket, transmisi, jumlah_sesi, harga) VALUES
 ('Manual Reguler 8 Sesi', 'manual', 8, 1000000.00),
 ('Manual Intensif 12 Sesi','manual', 12, 1450000.00),
 ('Matic Reguler 8 Sesi',  'matic',  8, 1200000.00);

INSERT INTO pendaftaran (id_siswa, id_paket, tanggal_daftar, total_sesi, sesi_terpakai, harga_dibayar, status) VALUES
 (1, 1, CURDATE(), 8, 0, 1000000.00, 'aktif'),
 (2, 3, CURDATE(), 8, 0, 1200000.00, 'aktif');

INSERT INTO slot_jadwal (id_pelatih, id_mobil, tanggal, jam_mulai, jam_selesai) VALUES
 (1, 1, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '08:00', '09:30'),
 (1, 1, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '10:00', '11:30'),
 (1, 1, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '13:00', '14:30'),
 (2, 2, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '08:00', '09:30'),
 (2, 2, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '10:00', '11:30'),
 (1, 1, DATE_ADD(CURDATE(), INTERVAL 2 DAY), '08:00', '09:30'),
 (2, 2, DATE_ADD(CURDATE(), INTERVAL 2 DAY), '09:00', '10:30');

-- Sesi hari ini yang sudah dimulai (agar fitur Konfirmasi Selesai dapat diuji kapan saja)
INSERT INTO slot_jadwal (id_pelatih, id_mobil, tanggal, jam_mulai, jam_selesai, status) VALUES
 (1, 1, CURDATE(), '00:01', '00:59', 'terpesan');
INSERT INTO booking (id_slot, id_pendaftaran, waktu_booking, status) VALUES
 (LAST_INSERT_ID(), 1, NOW(), 'terjadwal');
