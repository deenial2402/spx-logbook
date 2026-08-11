CREATE DATABASE IF NOT EXISTS `spx_logbook` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `spx_logbook`;

-- Tabel Pengguna (Admin & Super Admin)
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('Admin', 'Super Admin') NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert Default Admin (Password hashed via Password Verify PHP)
INSERT INTO `users` (`username`, `password`, `role`) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.X2g14z/.q', 'Admin'), -- pass: 1234
('superadmin', '$2y$10$e.wFvS.U8qX7A7Lp5N9o0e.J2PZ3X4Y5Z6A7B8C9D0E1F2G3H4I5J', 'Super Admin'); -- pass: super123

-- Tabel Master Asset
CREATE TABLE IF NOT EXISTS `asset_master` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `asset_id` VARCHAR(50) NOT NULL UNIQUE,
  `sn` VARCHAR(100) NOT NULL UNIQUE,
  `name` VARCHAR(100) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO `asset_master` (`asset_id`, `sn`, `name`) VALUES
('AST-001', 'PDA-00123', 'PDA HoneyWell 01'),
('AST-002', 'PDA-00456', 'PDA HoneyWell 02'),
('AST-003', 'PDA-00789', 'PDA Zebra 01');

-- Tabel Logs Transaksi Asset
CREATE TABLE IF NOT EXISTS `asset_logs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nama` VARCHAR(100) NOT NULL,
  `ops_id` VARCHAR(50) NOT NULL,
  `sn_asset` VARCHAR(100) NOT NULL,
  `status` ENUM('Pinjam', 'Kembali') NOT NULL,
  `photo_pinjam` VARCHAR(255) DEFAULT NULL,
  `photo_kembali` VARCHAR(255) DEFAULT NULL,
  `date_pinjam` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `date_kembali` TIMESTAMP NULL DEFAULT NULL
);