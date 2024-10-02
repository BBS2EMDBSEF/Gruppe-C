-- phpMyAdmin SQL Dump
-- version 8.2.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 27. Feb 2022 um 16:49
-- Server-Version: 10.4.18-MariaDB
-- PHP-Version: 7.4.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `BBS_projekt`
--
CREATE DATABASE IF NOT EXISTS `BBS_Projekt` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `BBS_Projekt`;
-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `passwort` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `passwort`, `created_at`, `updated_at`) VALUES
(75, 'Borna', 'borna@borna.de', '$2y$10$mJ75vpei0M2ElJDZMwEOhu2LUu3Ng8MEHQPBqCXA5CRegaCnkeF0K', '2022-02-27 11:47:19', NULL),
(76, 'admin', 'admin@admin.de', '$2y$10$0bKqPZ80Uokt8Y8bTjKroup6rQGYO6PBMi8RbqaOa7B6SEClO7T7.', '2022-02-27 13:50:01', NULL);

--
-- Indizes der exportierten Tabellen
--

-- Indizes für die Tabelle `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT für Tabelle `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
