-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 25, 2018 at 12:27 PM
-- Server version: 5.7.22-0ubuntu0.16.04.1
-- PHP Version: 7.0.30-0ubuntu0.16.04.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `moje_sprawy`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(1) NOT NULL,
  `login` varchar(10) NOT NULL,
  `haslo` varchar(255) NOT NULL,
  `utworzone` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `jrwa`
--

CREATE TABLE `jrwa` (
  `id` int(11) NOT NULL,
  `numer` varchar(4) NOT NULL,
  `opis` varchar(255) NOT NULL,
  `utworzone` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `podmioty`
--

CREATE TABLE `podmioty` (
  `id` int(11) NOT NULL,
  `nazwa` varchar(255) NOT NULL,
  `adres_1` varchar(255) NOT NULL,
  `adres_2` varchar(255) NOT NULL,
  `utworzone` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `pracownicy`
--

CREATE TABLE `pracownicy` (
  `id` int(11) NOT NULL,
  `imie` varchar(255) NOT NULL,
  `nazwisko` varchar(255) NOT NULL,
  `login` varchar(255) NOT NULL,
  `haslo` varchar(255) NOT NULL,
  `zmiana_hasla` datetime NOT NULL,
  `poziom` int(1) NOT NULL,
  `aktywny` int(1) NOT NULL,
  `utworzone` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `przychodzace`
--

CREATE TABLE `przychodzace` (
  `id` int(11) NOT NULL,
  `nr_rejestru` int(6) NOT NULL,
  `znak` varchar(40) NOT NULL,
  `data_pisma` date NOT NULL,
  `data_wplywu` date NOT NULL,
  `dotyczy` text NOT NULL,
  `id_podmiot` int(11) NOT NULL,
  `czy_faktura` int(1) NOT NULL,
  `id_pracownik` int(11) NOT NULL,
  `liczba_zalacznikow` int(2) NOT NULL,
  `kwota` float NOT NULL,
  `nr_rejestru_faktur` int(5) NOT NULL,
  `utworzone` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `login` (`login`);

--
-- Indexes for table `jrwa`
--
ALTER TABLE `jrwa`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `numer` (`numer`);

--
-- Indexes for table `podmioty`
--
ALTER TABLE `podmioty`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pracownicy`
--
ALTER TABLE `pracownicy`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `login` (`login`);

--
-- Indexes for table `przychodzace`
--
ALTER TABLE `przychodzace`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(1) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `jrwa`
--
ALTER TABLE `jrwa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=189;
--
-- AUTO_INCREMENT for table `podmioty`
--
ALTER TABLE `podmioty`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
--
-- AUTO_INCREMENT for table `pracownicy`
--
ALTER TABLE `pracownicy`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `przychodzace`
--
ALTER TABLE `przychodzace`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
