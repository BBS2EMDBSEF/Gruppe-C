-- Datenbank: `php_projekt`
--
CREATE DATABASE IF NOT EXISTS `php_projekt` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `php_projekt`;

-- --------------------------------------------------------
-- Tabellenstruktur für Tabelle `users`

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `nachname` varchar(255) NOT NULL,
  `vorname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `passwort` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `users`
--

INSERT INTO `users` (`id`, `nachname`, `vorname`, `email`, `passwort`) VALUES
(75, 'Borna', 'Ghazaleh', 'borna@borna.de', '$2y$10$mJ75vpei0M2ElJDZMwEOhu2LUu3Ng8MEHQPBqCXA5CRegaCnkeF0K'),
(76, 'admin', 'admin', 'admin@admin.de', '$2y$10$0bKqPZ80Uokt8Y8bTjKroup6rQGYO6PBMi8RbqaOa7B6SEClO7T7.');


-- Indizes für die Tabelle `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--

  CREATE TABLE `files` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) UNSIGNED NOT NULL,
  `filename` varchar(255) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
