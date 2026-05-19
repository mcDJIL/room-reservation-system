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

-- =====================================================
-- SEED DATA
-- =====================================================

INSERT INTO `users` (`id`, `password`, `name`, `email`, `role`) VALUES
(1, '$2y$10$seedadminhash0000000000000000000000000000000000000', 'Admin Utama', 'admin@rentroom.test', 'admin'),
(2, '$2y$10$seeduserhash00000000000000000000000000000000000000', 'Dina Prameswari', 'dina.prameswari@rentroom.test', 'user'),
(3, '$2y$10$seeduserhash00000000000000000000000000000000000001', 'Bima Pratama', 'bima.pratama@rentroom.test', 'user'),
(4, '$2y$10$seeduserhash00000000000000000000000000000000000002', 'Salsabila Rahman', 'salsabila.rahman@rentroom.test', 'user'),
(5, '$2y$10$seeduserhash00000000000000000000000000000000000003', 'Fahri Maulana', 'fahri.maulana@rentroom.test', 'user'),
(6, '$2y$10$seeduserhash00000000000000000000000000000000000004', 'Nabila Zahra', 'nabila.zahra@rentroom.test', 'user'),
(7, '$2y$10$seeduserhash00000000000000000000000000000000000005', 'Rizky Ananda', 'rizky.ananda@rentroom.test', 'user'),
(8, '$2y$10$seeduserhash00000000000000000000000000000000000006', 'Maya Pertiwi', 'maya.pertiwi@rentroom.test', 'user'),
(9, '$2y$10$seeduserhash00000000000000000000000000000000000007', 'Yoga Saputra', 'yoga.saputra@rentroom.test', 'user'),
(10, '$2y$10$seeduserhash00000000000000000000000000000000000008', 'Intan Kurnia', 'intan.kurnia@rentroom.test', 'user'),
(11, '$2y$10$seeduserhash00000000000000000000000000000000000009', 'Reza Akbar', 'reza.akbar@rentroom.test', 'user'),
(12, '$2y$10$seeduserhash00000000000000000000000000000000000010', 'Aulia Putri', 'aulia.putri@rentroom.test', 'user');

INSERT INTO `buildings` (`id`, `name`) VALUES
(1, 'Gedung Pascasarjana'),
(2, 'Gedung D4'),
(3, 'Gedung Rektorat'),
(4, 'Gedung Laboratorium'),
(5, 'Gedung Perpustakaan'),
(6, 'Gedung Serbaguna');

INSERT INTO `rooms` (`id`, `building_id`, `room_name`, `capacity`, `facility`, `short_description`, `detail_description`, `is_active`) VALUES
(1, 1, 'Auditorium Lt. 6', 100, 'Projector, AC, Sound System, Wireless Microphone', 'Ruang auditorium representatif untuk acara besar.', 'Auditorium utama di lantai 6 yang cocok untuk seminar, workshop, dan acara resmi kampus.', 1),
(2, 1, 'Ruang Sidang Pascasarjana', 30, 'AC, Meja Oval, Projector, Whiteboard', 'Ruang sidang untuk diskusi akademik.', 'Ruang ini biasa dipakai untuk sidang tesis, rapat pembimbing, dan presentasi formal.', 1),
(3, 2, 'Ruang Kelas D4-101', 45, 'AC, Projector, Whiteboard', 'Kelas reguler untuk perkuliahan aktif.', 'Kelas modern dengan pencahayaan baik dan layout nyaman untuk kegiatan belajar mengajar.', 1),
(4, 2, 'Mini Teater D4', 80, 'AC, Projector Besar, Sound System', 'Mini teater untuk presentasi dan pelatihan.', 'Cocok untuk pemutaran video, training, dan presentasi yang membutuhkan tampilan layar lebar.', 1),
(5, 3, 'Ruang Rapat Rektorat', 20, 'Meja Rapat, AC, Monitor TV', 'Ruang rapat eksekutif.', 'Ruangan privat untuk koordinasi pimpinan, rapat terbatas, dan pembahasan kebijakan.', 1),
(6, 3, 'Aula Rektorat', 120, 'AC, Projector, Kursi Auditorium, Sound System', 'Aula serbaguna untuk agenda resmi.', 'Digunakan untuk acara universitas, sosialisasi program, dan penyambutan tamu.', 1),
(7, 4, 'Laboratorium Komputer 1', 40, 'PC, AC, Projector, LAN', 'Lab komputer untuk praktikum.', 'Dilengkapi komputer dan jaringan lokal untuk praktik dan ujian berbasis komputer.', 1),
(8, 4, 'Laboratorium Komputer 2', 36, 'PC, AC, Projector, LAN', 'Lab komputer cadangan.', 'Ruang pendukung untuk kelas praktik dan kegiatan ujian jika kapasitas lab utama penuh.', 1),
(9, 4, 'Laboratorium Multimedia', 30, 'PC Editing, AC, Speaker, Kamera', 'Lab untuk produksi konten.', 'Laboratorium untuk editing video, desain grafis, dan produksi media pembelajaran.', 1),
(10, 5, 'Ruang Baca Senyap', 25, 'AC, Meja Individual, Lampu Baca', 'Ruang tenang untuk membaca dan belajar.', 'Area hening bagi mahasiswa yang membutuhkan fokus tinggi dan suasana belajar yang nyaman.', 1),
(11, 5, 'Ruang Diskusi Perpustakaan', 18, 'AC, Papan Tulis, Sofa', 'Ruang diskusi kelompok kecil.', 'Ruang fleksibel untuk diskusi kelompok, kerja tim, dan presentasi singkat.', 1),
(12, 5, 'Ruang Seminar Perpustakaan', 60, 'AC, Projector, Sound System', 'Ruang seminar yang terintegrasi dengan perpustakaan.', 'Disiapkan untuk acara literasi, seminar mini, dan workshop akademik.', 1),
(13, 6, 'Hall Serbaguna Utama', 200, 'AC, Panggung, Sound System, Projector', 'Hall besar untuk event kampus.', 'Digunakan untuk wisuda, pentas seni, seminar nasional, dan acara besar lainnya.', 1),
(14, 6, 'Ruang Serbaguna B', 50, 'AC, Projector, Kursi Lipat', 'Ruang fleksibel untuk berbagai kegiatan.', 'Cocok untuk pelatihan, sosialisasi, dan kegiatan organisasi mahasiswa.', 1),
(15, 6, 'Ruang Workshop Kreatif', 35, 'Meja Workshop, AC, LCD Projector', 'Ruang praktikum non-teknis.', 'Dipakai untuk workshop desain, kegiatan komunitas, dan pelatihan singkat.', 1),
(16, 2, 'Ruang Kelas D4-202', 40, 'AC, Projector, Whiteboard', 'Kelas tambahan di lantai 2.', 'Ruang cadangan untuk jadwal kuliah dan kelas seminar.', 1),
(17, 3, 'Ruang Interview', 12, 'AC, Sofa, Meja Kecil', 'Ruang untuk wawancara dan konsultasi.', 'Menunjang proses interview, konseling, atau pertemuan dua arah yang privat.', 0),
(18, 4, 'Ruang Server', 8, 'AC Presisi, Rak Server, UPS', 'Ruang operasional teknis.', 'Ruang terbatas untuk perangkat jaringan dan infrastruktur TI kampus.', 1);

INSERT INTO `reservations` (`id`, `user_id`, `room_id`, `reservation_date`, `start_hour`, `end_hour`, `reason`, `status`, `approved_by`, `created_at`) VALUES
(1, 2, 1, '2026-05-20', '08:00:00', '11:00:00', 'Seminar proposal tugas akhir.', 'approved', 1, '2026-05-18 09:15:00'),
(2, 3, 4, '2026-05-20', '13:00:00', '15:00:00', 'Pelatihan presentasi untuk kelas.', 'waiting', NULL, '2026-05-18 10:00:00'),
(3, 4, 5, '2026-05-21', '09:00:00', '11:30:00', 'Rapat koordinasi himpunan.', 'approved', 1, '2026-05-18 11:20:00'),
(4, 5, 7, '2026-05-21', '10:00:00', '12:00:00', 'Praktikum pemrograman web.', 'waiting', NULL, '2026-05-18 12:30:00'),
(5, 6, 10, '2026-05-22', '08:00:00', '10:00:00', 'Belajar kelompok sebelum ujian.', 'approved', 1, '2026-05-18 13:10:00'),
(6, 7, 13, '2026-05-22', '13:00:00', '16:00:00', 'Kegiatan organisasi mahasiswa.', 'waiting', NULL, '2026-05-18 14:05:00'),
(7, 8, 2, '2026-05-23', '09:30:00', '12:00:00', 'Sidang tesis semester berjalan.', 'rejected', 1, '2026-05-18 15:00:00'),
(8, 9, 11, '2026-05-23', '10:00:00', '12:00:00', 'Diskusi kelompok tugas riset.', 'approved', 1, '2026-05-18 15:25:00'),
(9, 10, 6, '2026-05-24', '08:00:00', '11:00:00', 'Workshop literasi digital.', 'waiting', NULL, '2026-05-18 16:00:00'),
(10, 11, 12, '2026-05-24', '13:00:00', '15:00:00', 'Seminar komunitas membaca.', 'approved', 1, '2026-05-18 16:40:00'),
(11, 12, 14, '2026-05-25', '09:00:00', '11:00:00', 'Pelatihan soft skill.', 'waiting', NULL, '2026-05-18 17:20:00'),
(12, 2, 3, '2026-05-25', '13:00:00', '15:30:00', 'Perkuliahan pengganti.', 'approved', 1, '2026-05-18 18:10:00'),
(13, 3, 8, '2026-05-26', '08:00:00', '10:00:00', 'Ujian praktik komputer.', 'waiting', NULL, '2026-05-18 18:50:00'),
(14, 4, 9, '2026-05-26', '10:30:00', '12:00:00', 'Editing konten pembelajaran.', 'approved', 1, '2026-05-18 19:10:00'),
(15, 5, 15, '2026-05-27', '08:30:00', '11:30:00', 'Workshop desain poster.', 'waiting', NULL, '2026-05-18 19:45:00'),
(16, 6, 16, '2026-05-27', '13:00:00', '15:00:00', 'Kelas tambahan minggu ini.', 'approved', 1, '2026-05-18 20:20:00'),
(17, 7, 17, '2026-05-28', '09:00:00', '10:00:00', 'Interview beasiswa internal.', 'rejected', 1, '2026-05-18 20:55:00'),
(18, 8, 18, '2026-05-28', '10:00:00', '12:00:00', 'Maintenance jaringan kampus.', 'approved', 1, '2026-05-18 21:15:00'),
(19, 9, 1, '2026-05-29', '08:00:00', '10:00:00', 'Simulasi presentasi final.', 'waiting', NULL, '2026-05-18 21:45:00'),
(20, 10, 4, '2026-05-29', '13:00:00', '16:00:00', 'Sosialisasi program kemahasiswaan.', 'approved', 1, '2026-05-18 22:05:00'),
(21, 11, 6, '2026-05-30', '08:30:00', '11:30:00', 'Acara alumni dan sharing session.', 'waiting', NULL, '2026-05-18 22:30:00'),
(22, 12, 12, '2026-05-30', '13:00:00', '15:30:00', 'Seminar literasi digital tahap 2.', 'approved', 1, '2026-05-18 22:55:00'),
(23, 2, 13, '2026-05-31', '09:00:00', '12:00:00', 'Latihan acara kampus.', 'waiting', NULL, '2026-05-18 23:20:00'),
(24, 3, 14, '2026-05-31', '13:00:00', '15:00:00', 'Rapat panitia kegiatan.', 'approved', 1, '2026-05-18 23:40:00');

INSERT INTO `room_photos` (`id`, `room_id`, `photo`, `is_primary`) VALUES
(1, 1, 'rooms/auditorium-lt6-1.jpg', 1),
(2, 1, 'rooms/auditorium-lt6-2.jpg', 0),
(3, 1, 'rooms/auditorium-lt6-3.jpg', 0),
(4, 2, 'rooms/ruang-sidang-pascasarjana-1.jpg', 1),
(5, 2, 'rooms/ruang-sidang-pascasarjana-2.jpg', 0),
(6, 3, 'rooms/kelas-d4-101-1.jpg', 1),
(7, 3, 'rooms/kelas-d4-101-2.jpg', 0),
(8, 4, 'rooms/mini-teater-d4-1.jpg', 1),
(9, 4, 'rooms/mini-teater-d4-2.jpg', 0),
(10, 5, 'rooms/ruang-rapat-rektorat-1.jpg', 1),
(11, 5, 'rooms/ruang-rapat-rektorat-2.jpg', 0),
(12, 6, 'rooms/aula-rektorat-1.jpg', 1),
(13, 6, 'rooms/aula-rektorat-2.jpg', 0),
(14, 7, 'rooms/lab-komputer-1-1.jpg', 1),
(15, 7, 'rooms/lab-komputer-1-2.jpg', 0),
(16, 8, 'rooms/lab-komputer-2-1.jpg', 1),
(17, 9, 'rooms/lab-multimedia-1.jpg', 1),
(18, 10, 'rooms/ruang-baca-senyap-1.jpg', 1),
(19, 11, 'rooms/ruang-diskusi-perpustakaan-1.jpg', 1),
(20, 12, 'rooms/ruang-seminar-perpustakaan-1.jpg', 1),
(21, 13, 'rooms/hall-serbaguna-utama-1.jpg', 1),
(22, 13, 'rooms/hall-serbaguna-utama-2.jpg', 0),
(23, 14, 'rooms/ruang-serbaguna-b-1.jpg', 1),
(24, 15, 'rooms/ruang-workshop-kreatif-1.jpg', 1),
(25, 16, 'rooms/kelas-d4-202-1.jpg', 1),
(26, 17, 'rooms/ruang-interview-1.jpg', 1),
(27, 18, 'rooms/ruang-server-1.jpg', 1);