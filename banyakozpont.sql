-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Gép: 127.0.0.1
-- Létrehozás ideje: 2023. Dec 31. 15:48
-- Kiszolgáló verziója: 10.4.32-MariaDB
-- PHP verzió: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Adatbázis: `banyakozpont`
--

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `extrak`
--

CREATE TABLE `extrak` (
  `id` int(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `udvozlo_uzenet` varchar(255) NOT NULL,
  `udvozlo_uzenet_2` varchar(255) NOT NULL,
  `udvozlo_uzenet_3` varchar(255) NOT NULL,
  `udvozlo_uzenet_4` varchar(255) NOT NULL,
  `udvozlo_uzenet_5` varchar(255) NOT NULL,
  `nevelo` varchar(255) NOT NULL,
  `nev_utotag` varchar(255) NOT NULL,
  `ultra_szint` varchar(255) NOT NULL,
  `udvozlo_aktivalva` tinyint(1) NOT NULL,
  `nevelo_aktivalva` tinyint(1) NOT NULL,
  `nev_utotag_aktivalva` tinyint(1) NOT NULL,
  `ultra_szint_aktivalva` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- A tábla adatainak kiíratása `extrak`
--

INSERT INTO `extrak` (`id`, `username`, `udvozlo_uzenet`, `udvozlo_uzenet_2`, `udvozlo_uzenet_3`, `udvozlo_uzenet_4`, `udvozlo_uzenet_5`, `nevelo`, `nev_utotag`, `ultra_szint`, `udvozlo_aktivalva`, `nevelo_aktivalva`, `nev_utotag_aktivalva`, `ultra_szint_aktivalva`) VALUES
(1, 'Knuddeliger_', 'a', 'a', 'a', 'a', 'a', '0*', '', '', 1, 1, 0, 1),
(2, 'mate', '', '', '', '', '', '', '', '', 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `extrak_ara`
--

CREATE TABLE `extrak_ara` (
  `id` int(255) NOT NULL,
  `udvozlo_uzenet_be` int(255) NOT NULL,
  `udvozlo_uzenet_ft` int(255) NOT NULL,
  `nevelo_be` int(255) NOT NULL,
  `nevelo_ft` int(255) NOT NULL,
  `ultra_szint_ft` int(255) NOT NULL,
  `nevelo_modositas_be` int(255) NOT NULL,
  `nevelo_modositas_ft` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- A tábla adatainak kiíratása `extrak_ara`
--

INSERT INTO `extrak_ara` (`id`, `udvozlo_uzenet_be`, `udvozlo_uzenet_ft`, `nevelo_be`, `nevelo_ft`, `ultra_szint_ft`, `nevelo_modositas_be`, `nevelo_modositas_ft`) VALUES
(1, 4800, 2032, 9360, 3048, 508, 1560, 508);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `kellekek`
--

CREATE TABLE `kellekek` (
  `id` int(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `skin` varchar(255) NOT NULL,
  `hd_skin` varchar(255) NOT NULL,
  `szarny` varchar(255) NOT NULL,
  `farok` varchar(255) NOT NULL,
  `kalap` varchar(255) NOT NULL,
  `karkoto` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- A tábla adatainak kiíratása `kellekek`
--

INSERT INTO `kellekek` (`id`, `username`, `skin`, `hd_skin`, `szarny`, `farok`, `kalap`, `karkoto`) VALUES
(1, 'mate', 'mate_skin.png', '', '', '', '', ''),
(2, 'Knuddeliger_', '', '', '', '', '', '');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `ladak`
--

CREATE TABLE `ladak` (
  `lada_username` varchar(255) NOT NULL,
  `alap_lada` varchar(255) NOT NULL,
  `pandora_szelenceje` varchar(255) NOT NULL,
  `kivalasztottak_ladaja` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- A tábla adatainak kiíratása `ladak`
--

INSERT INTO `ladak` (`lada_username`, `alap_lada`, `pandora_szelenceje`, `kivalasztottak_ladaja`) VALUES
('Knuddeliger_', '1', '1', '2');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `rangok`
--

CREATE TABLE `rangok` (
  `id` int(255) NOT NULL,
  `rang` varchar(255) NOT NULL,
  `leiras` varchar(255) NOT NULL,
  `forint_ertek` int(255) NOT NULL,
  `be_ertek` int(255) NOT NULL,
  `forint_ertek_orokre` int(255) NOT NULL,
  `be_ertek_orokre` int(255) NOT NULL,
  `forint_ertek_havonta` int(255) NOT NULL,
  `be_ertek_havonta` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- A tábla adatainak kiíratása `rangok`
--

INSERT INTO `rangok` (`id`, `rang`, `leiras`, `forint_ertek`, `be_ertek`, `forint_ertek_orokre`, `be_ertek_orokre`, `forint_ertek_havonta`, `be_ertek_havonta`) VALUES
(1, 'VIP', 'vip leírás', 254, 780, 254, 2340, 0, 312),
(2, 'Elit', 'elit leírás', 508, 1565, 508, 4680, 0, 546),
(3, 'Zsírkirály', 'zsk leírás', 1016, 3120, 1016, 9360, 0, 1170),
(4, 'Titán', 'titán leírás', 2032, 9360, 2032, 28080, 0, 3120),
(5, 'Félisten', 'félisten leírás', 3048, 18720, 3048, 56160, 0, 6240),
(6, 'Mindenható', 'mh leírás', 4064, 31200, 4064, 93600, 0, 7800),
(7, 'Troll', 'troll leírás', 2032, 0, 2032, 0, 0, 0),
(8, 'Troll+\n', 'trollplusz leírás', 5080, 0, 5080, 0, 0, 0),
(9, 'Mindenható+', 'mh+ leírás', 5080, 0, 5080, 93600, 0, 7800),
(10, 'Mutáns', 'mutáns leírás', 20000, 0, 20000, 0, 0, 0),
(11, 'Bosszúálló', 'bosszúálló leírás', 20000, 0, 20000, 0, 0, 0),
(12, 'Sith', 'sith leírás', 5000, 0, 5000, 0, 0, 0),
(13, 'Jedi', 'jedi leírás', 5000, 0, 5000, 0, 0, 0);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `users`
--

CREATE TABLE `users` (
  `id` int(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `banyaszerme` int(255) NOT NULL,
  `forint` int(255) NOT NULL,
  `korona` int(255) NOT NULL,
  `rang` varchar(255) NOT NULL,
  `rang_expire` varchar(255) NOT NULL,
  `regisztracio` varchar(255) NOT NULL,
  `picture` varchar(255) NOT NULL,
  `new_password_id` varchar(255) NOT NULL,
  `new_email_id` varchar(255) NOT NULL,
  `new_email` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- A tábla adatainak kiíratása `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `banyaszerme`, `forint`, `korona`, `rang`, `rang_expire`, `regisztracio`, `picture`, `new_password_id`, `new_email_id`, `new_email`) VALUES
(1, 'Knuddeliger_', 'ariel', 'teszt@teszt.com', 120055, 11476, 32, 'Mindenható', '', '2018-08-14 16:29:24', 'Knuddeliger_', '', '', ''),
(2, 'mate', 'jelszo', 'haha@gmail.com', 1190120, 4839, 30, 'Titán', 'Örök', '2019-12-21 22:14:35', 'mate', '', '', ''),
(3, 'janika', 'jancsi', 'janika@jani.com', 0, 0, 2, 'Zsírkirály', '2027-12-05', '2022-10-14 14:25:12', '', '', '', '');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `utalasok`
--

CREATE TABLE `utalasok` (
  `id` int(255) NOT NULL,
  `utalas_kezdemenyzo` varchar(255) NOT NULL,
  `utalas_megkapo` varchar(255) NOT NULL,
  `tipus` varchar(255) NOT NULL,
  `datum` varchar(255) NOT NULL,
  `ertek` int(255) NOT NULL,
  `statusz` varchar(255) NOT NULL,
  `trade_id` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- A tábla adatainak kiíratása `utalasok`
--

INSERT INTO `utalasok` (`id`, `utalas_kezdemenyzo`, `utalas_megkapo`, `tipus`, `datum`, `ertek`, `statusz`, `trade_id`) VALUES
(15, 'Knuddeliger_', 'mate', 'Bányászérme', '2023-12-29 21:05:40', 12, 'Függőben', '5c538373f4ebc408bf1617ca14148610af65666dfacc21a97c3e29e830299179'),
(16, 'Knuddeliger_', 'janika', 'Bányászérme', '2023-12-29 21:06:40', 23, 'Elfogadva', '0dbebb2cc7361850e0abb1761ae0598993be0f1cb653613e4f7faf2a32cd8aaf'),
(17, 'Knuddeliger_', 'janika', 'Korona', '2023-12-29 21:07:47', 2, 'Elfogadva', '4c564ac1faaf37872bf03e2be0abbceeeb6cd750fad78db20b4dd1044c338f2e'),
(18, 'Knuddeliger_', 'mate', 'Forint', '2023-12-29 21:08:24', 4331, 'Elfogadva', 'f4bc41df93bb8fa68f5dbe3c9e9ded3febbb5eb976e51957dbff550adac6519a'),
(19, 'janika', 'Knuddeliger_', 'Bányászérme', '2023-12-30 11:45:31', 23, 'Elfogadva', '12d804b749c7f4af3aec26794782402259c9c6b6ed1b676c917502ab187e8fc8');

--
-- Indexek a kiírt táblákhoz
--

--
-- A tábla indexei `extrak`
--
ALTER TABLE `extrak`
  ADD PRIMARY KEY (`id`);

--
-- A tábla indexei `extrak_ara`
--
ALTER TABLE `extrak_ara`
  ADD PRIMARY KEY (`id`);

--
-- A tábla indexei `kellekek`
--
ALTER TABLE `kellekek`
  ADD PRIMARY KEY (`id`);

--
-- A tábla indexei `rangok`
--
ALTER TABLE `rangok`
  ADD PRIMARY KEY (`id`);

--
-- A tábla indexei `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- A tábla indexei `utalasok`
--
ALTER TABLE `utalasok`
  ADD PRIMARY KEY (`id`);

--
-- A kiírt táblák AUTO_INCREMENT értéke
--

--
-- AUTO_INCREMENT a táblához `extrak`
--
ALTER TABLE `extrak`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT a táblához `extrak_ara`
--
ALTER TABLE `extrak_ara`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT a táblához `kellekek`
--
ALTER TABLE `kellekek`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT a táblához `rangok`
--
ALTER TABLE `rangok`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT a táblához `users`
--
ALTER TABLE `users`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT a táblához `utalasok`
--
ALTER TABLE `utalasok`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
