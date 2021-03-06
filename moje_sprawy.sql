-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 23, 2018 at 12:15 PM
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
  `id` int(11) NOT NULL,
  `login` varchar(10) NOT NULL,
  `haslo` varchar(255) NOT NULL,
  `utworzone` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `decyzje`
--

CREATE TABLE `decyzje` (
  `id` int(11) NOT NULL,
  `id_wychodzace` int(11) NOT NULL,
  `numer` varchar(255) NOT NULL,
  `dotyczy` text NOT NULL,
  `id_jrwa` int(11) NOT NULL,
  `utworzone` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `inne`
--

CREATE TABLE `inne` (
  `id` int(11) NOT NULL,
  `id_sprawa` int(11) NOT NULL,
  `rodzaj` varchar(255) NOT NULL,
  `dotyczy` text NOT NULL,
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
-- Table structure for table `metryka`
--

CREATE TABLE `metryka` (
  `id` int(11) NOT NULL,
  `id_sprawa` int(11) NOT NULL,
  `id_pracownik` int(11) NOT NULL,
  `czynnosc` varchar(255) NOT NULL,
  `rodzaj_dokumentu` int(1) NOT NULL,
  `id_dokument` int(11) NOT NULL,
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
-- Table structure for table `postanowienia`
--

CREATE TABLE `postanowienia` (
  `id` int(11) NOT NULL,
  `id_wychodzace` int(11) NOT NULL,
  `numer` varchar(255) NOT NULL,
  `dotyczy` text NOT NULL,
  `id_jrwa` int(11) NOT NULL,
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
  `przedrostek` varchar(255) NOT NULL,
  `przyrostek` varchar(255) NOT NULL,
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

-- --------------------------------------------------------

--
-- Table structure for table `przychodzace_adacta`
--

CREATE TABLE `przychodzace_adacta` (
  `id_przychodzace` int(11) NOT NULL,
  `id_jrwa` int(11) NOT NULL,
  `utworzone` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `sprawy`
--

CREATE TABLE `sprawy` (
  `id` int(11) NOT NULL,
  `znak` varchar(20) NOT NULL,
  `temat` text NOT NULL,
  `id_jrwa` int(11) NOT NULL,
  `zakonczona` datetime DEFAULT NULL,
  `utworzone` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ustawienia`
--

CREATE TABLE `ustawienia` (
  `id` int(11) NOT NULL,
  `waznosc_hasla` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `wychodzace`
--

CREATE TABLE `wychodzace` (
  `id` int(11) NOT NULL,
  `id_sprawa` int(11) NOT NULL,
  `id_podmiot` int(11) NOT NULL,
  `data_wyjscia` date DEFAULT NULL,
  `sposob_wyjscia` int(11) NOT NULL DEFAULT '0',
  `dotyczy` text NOT NULL,
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
-- Indexes for table `decyzje`
--
ALTER TABLE `decyzje`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inne`
--
ALTER TABLE `inne`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jrwa`
--
ALTER TABLE `jrwa`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `numer` (`numer`);

--
-- Indexes for table `metryka`
--
ALTER TABLE `metryka`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `podmioty`
--
ALTER TABLE `podmioty`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `postanowienia`
--
ALTER TABLE `postanowienia`
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
-- Indexes for table `przychodzace_adacta`
--
ALTER TABLE `przychodzace_adacta`
  ADD UNIQUE KEY `utworzone` (`utworzone`);

--
-- Indexes for table `sprawy`
--
ALTER TABLE `sprawy`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `znak` (`znak`);

--
-- Indexes for table `ustawienia`
--
ALTER TABLE `ustawienia`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wychodzace`
--
ALTER TABLE `wychodzace`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `decyzje`
--
ALTER TABLE `decyzje`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `inne`
--
ALTER TABLE `inne`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `jrwa`
--
ALTER TABLE `jrwa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `metryka`
--
ALTER TABLE `metryka`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `podmioty`
--
ALTER TABLE `podmioty`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `postanowienia`
--
ALTER TABLE `postanowienia`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `pracownicy`
--
ALTER TABLE `pracownicy`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `przychodzace`
--
ALTER TABLE `przychodzace`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `sprawy`
--
ALTER TABLE `sprawy`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ustawienia`
--
ALTER TABLE `ustawienia`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `wychodzace`
--
ALTER TABLE `wychodzace`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
