CREATE TABLE `users` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `password` varchar(255) NOT NULL,
  `name` varchar(100),
  `email` varchar(100),
  `role` enum('admin','user') NOT NULL
);

CREATE TABLE `buildings` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(100) NOT NULL COMMENT 'Contoh: Gedung D4, Gedung D3'
);

CREATE TABLE `rooms` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `building_id` int NOT NULL COMMENT 'Menghubungkan ruangan ke gedungnya',
  `room_name` varchar(100) NOT NULL,
  `capacity` int,
  `facility` text,
  `short_description` text,
  `detail_description` text,
  `is_active` tinyint DEFAULT 1 COMMENT 'Untuk fitur Soft Delete'
);

CREATE TABLE `reservations` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `user_id` int,
  `room_id` int,
  `reservation_date` date NOT NULL,
  `start_hour` time NOT NULL,
  `end_hour` time NOT NULL,
  `reason` text,
  `status` enum('waiting','approved','rejected') DEFAULT 'waiting',
  `approved_by` int COMMENT 'Fitur Audit Trail',
  `created_at` timestamp DEFAULT (now())
);

CREATE TABLE `room_photos` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `room_id` int NOT NULL,
  `photo` varchar(255),
  `is_primary` int
);

ALTER TABLE `users` COMMENT = 'Tabel untuk login Admin dan Mahasiswa/Dosen';

ALTER TABLE `rooms` ADD FOREIGN KEY (`building_id`) REFERENCES `buildings` (`id`);

ALTER TABLE `reservations` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

ALTER TABLE `reservations` ADD FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`);

ALTER TABLE `reservations` ADD FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`);

ALTER TABLE `room_photos` ADD FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`);
