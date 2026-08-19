-- HK Garage Database Schema (Aruba & Local MySQL Compatible)
-- Note: Select your assigned database (e.g. Sql1940728_1) in phpMyAdmin before importing.


-- Admins Table
CREATE TABLE IF NOT EXISTS `admins` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Services Table
CREATE TABLE IF NOT EXISTS `services` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(150) NOT NULL,
  `description` TEXT,
  `duration` INT NOT NULL DEFAULT 30, -- duration in minutes
  `price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Appointments Table
CREATE TABLE IF NOT EXISTS `appointments` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `customer_name` VARCHAR(100) NOT NULL,
  `phone` VARCHAR(30) NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `vehicle_brand` VARCHAR(80) NOT NULL,
  `vehicle_model` VARCHAR(80) NOT NULL,
  `vehicle_registration` VARCHAR(30) NOT NULL,
  `service_id` INT NOT NULL,
  `booking_date` DATE NOT NULL,
  `booking_time` TIME NOT NULL,
  `status` ENUM('Pending', 'Confirmed', 'Completed', 'Cancelled') NOT NULL DEFAULT 'Pending',
  `notes` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`service_id`) REFERENCES `services`(`id`) ON DELETE CASCADE,
  -- Unique slot constraint: prevents double-booking at DB level (race condition protection)
  UNIQUE KEY `unique_slot` (`booking_date`, `booking_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- If upgrading existing table, add unique slot index:
-- ALTER TABLE `appointments` ADD UNIQUE KEY `unique_slot` (`booking_date`, `booking_time`);

-- News Table
CREATE TABLE IF NOT EXISTS `news` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT NOT NULL,
  `image` VARCHAR(500) DEFAULT NULL,
  `published_date` DATE NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


INSERT INTO `admins` (`id`, `name`, `email`, `password`) VALUES
(1, 'Admin HK Garage', 'admin@hkgarage.it', '$2y$12$OanMimcBRklGTwTEHCJR4uErOfy8yC/tOaEaQH8b9gCI4graA4jjy')
ON DUPLICATE KEY UPDATE `password`='$2y$12$OanMimcBRklGTwTEHCJR4uErOfy8yC/tOaEaQH8b9gCI4graA4jjy';

-- Seed Services
INSERT INTO `services` (`id`, `name`, `description`, `duration`, `price`, `active`) VALUES
(1, 'Riparazioni Meccaniche', 'Interventi su motore, freni, frizione e sospensioni con ricambi di qualità.', 60, 80.00, 1),
(2, 'Tagliandi e Manutenzione', 'Sostituzione olio, filtri, cinghie e controlli completi di sicurezza.', 90, 120.00, 1),
(3, 'Diagnosi Elettronica', 'Lettura centralina, individuazione spie ed errori elettrici ed elettronici.', 30, 45.00, 1),
(4, 'Cambio Olio e Filtri', 'Sostituzione olio motore sintetico e relativi filtri olio e aria.', 30, 65.00, 1),
(5, 'Controllo Freni e Sospensioni', 'Ispezione pastiglie, dischi freno e ammortizzatori con collaudo.', 45, 50.00, 1)
ON DUPLICATE KEY UPDATE `name`=`name`;

-- Seed Initial News
INSERT INTO `news` (`id`, `title`, `description`, `image`, `published_date`) VALUES
(1, 'Novità Revisioni Auto 2026', 'Scopri cosa cambia con le nuove normative sui controlli delle emissioni. Mettiti in regola prima della scadenza per evitare sanzioni.', 'https://images.unsplash.com/photo-1486006920555-c77dce18193b?auto=format&fit=crop&w=800&q=80', '2026-07-15'),
(2, 'Aria Condizionata: Quando ricaricare?', 'L\'estate è alle porte. L\'importanza di igienizzare l\'impianto e ricaricare il gas prima che inizino i veri caldi stagionali.', 'https://images.unsplash.com/photo-1517524008697-84bbe3c3fd98?auto=format&fit=crop&w=800&q=80', '2026-07-20'),
(3, 'Auto Elettriche e Ibride', 'Come cambia la manutenzione periodica per i veicoli di nuova generazione. La nostra officina è aggiornata per le nuove tecnologie.', 'https://images.unsplash.com/photo-1563720223185-11003d516935?auto=format&fit=crop&w=800&q=80', '2026-07-28')
ON DUPLICATE KEY UPDATE `title`=`title`;
