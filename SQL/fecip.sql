-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 17, 2025 at 09:16 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `fecip`
--

-- --------------------------------------------------------

--
-- Table structure for table `albuns`
--

CREATE TABLE `albuns` (
  `id` int(11) NOT NULL,
  `nome_album` varchar(255) DEFAULT NULL,
  `id_usuario` int(11) NOT NULL,
  `genero` varchar(255) DEFAULT NULL,
  `ano` int(11) NOT NULL,
  `descricao` text NOT NULL,
  `capa_album` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `faixas`
--

CREATE TABLE `faixas` (
  `id` int(11) NOT NULL,
  `id_album` int(11) DEFAULT NULL,
  `nome_musica` varchar(255) DEFAULT NULL,
  `caminho_audio` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `opensource`
--

CREATE TABLE `opensource` (
  `id` int(11) NOT NULL,
  `nome_musica` varchar(90) DEFAULT NULL,
  `nome_album` varchar(120) DEFAULT NULL,
  `nome_artista` varchar(90) DEFAULT NULL,
  `genero` varchar(50) DEFAULT NULL,
  `caminho_audio` varchar(255) DEFAULT NULL,
  `data_upload` datetime DEFAULT NULL,
  `capa_album` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `playlists`
--

CREATE TABLE `playlists` (
  `id` int(11) NOT NULL,
  `nome_playlist` varchar(100) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `data_criacao` datetime DEFAULT current_timestamp(),
  `caminho_imagem_playlist` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `playlist_faixas`
--

CREATE TABLE `playlist_faixas` (
  `id_playlist` int(11) NOT NULL,
  `id_faixa` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `senha` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `foto_perfil` varchar(255) DEFAULT NULL,
  `tipo_usuario` enum('comum','admin') NOT NULL,
  `estilo` char(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `albuns`
--
ALTER TABLE `albuns`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `faixas`
--
ALTER TABLE `faixas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_album` (`id_album`);

--
-- Indexes for table `opensource`
--
ALTER TABLE `opensource`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `playlists`
--
ALTER TABLE `playlists`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indexes for table `playlist_faixas`
--
ALTER TABLE `playlist_faixas`
  ADD PRIMARY KEY (`id_playlist`,`id_faixa`),
  ADD KEY `id_faixa` (`id_faixa`);

--
-- Indexes for table `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `albuns`
--
ALTER TABLE `albuns`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `faixas`
--
ALTER TABLE `faixas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `opensource`
--
ALTER TABLE `opensource`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `playlists`
--
ALTER TABLE `playlists`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `faixas`
--
ALTER TABLE `faixas`
  ADD CONSTRAINT `faixas_ibfk_1` FOREIGN KEY (`id_album`) REFERENCES `albuns` (`id`);

--
-- Constraints for table `playlists`
--
ALTER TABLE `playlists`
  ADD CONSTRAINT `playlists_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`);

--
-- Constraints for table `playlist_faixas`
--
ALTER TABLE `playlist_faixas`
  ADD CONSTRAINT `playlist_faixas_ibfk_1` FOREIGN KEY (`id_playlist`) REFERENCES `playlists` (`id`),
  ADD CONSTRAINT `playlist_faixas_ibfk_2` FOREIGN KEY (`id_faixa`) REFERENCES `faixas` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
