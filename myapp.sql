-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Gép: 127.0.0.1
-- Létrehozás ideje: 2016. Aug 04. 01:33
-- Kiszolgáló verziója: 10.1.13-MariaDB
-- PHP verzió: 5.6.23

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Adatbázis: `myapp`
--
CREATE DATABASE IF NOT EXISTS `myapp` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `myapp`;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- A tábla adatainak kiíratása `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(1, 'laptop'),
(2, 'számítógép'),
(3, 'kiegészítő'),
(4, 'alkatrész');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `conditions`
--

CREATE TABLE `conditions` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- A tábla adatainak kiíratása `conditions`
--

INSERT INTO `conditions` (`id`, `name`) VALUES
(1, 'Új'),
(2, 'Használt'),
(3, 'Felújított');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` varchar(1000) NOT NULL,
  `price` int(11) NOT NULL,
  `condition_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- A tábla adatainak kiíratása `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `condition_id`) VALUES
(1, 'ASUS laptop', 'Korszerű laptop irodai munkákhoz.', 89000, 1),
(2, 'DELL laptop', 'Felújított laptop otthoni használatra.', 34000, 3),
(6, 'HP laptop', 'Régi laptop.', 12000, 2),
(7, 'ASUS gamer számítógép', 'A legmodernebb asztali számítógép, ami kapható a hazai piacon.', 340000, 1),
(8, 'Media-tech hűtőventillátor', 'Hűtőpad laptopokhoz.', 3500, 1),
(9, 'Samsung fejhallgató', 'Használt fejhallgató kiváló basszus hangzással.', 8500, 2),
(10, 'Lenovo notebook', 'Notebook terepmunkához. Rendkívül strapabíró és megbízható eszköz.', 120000, 1),
(11, 'Sony hangszóró', 'Felújított hangszóró, basszuskiemeléssel.', 6500, 3),
(12, 'Radeon videókártya', 'Videókártya a jobb audiovizuális élményekért.', 34000, 1),
(13, 'AMD SSD 120 GB', 'Gyors, megbízható és kicsi. Ezek jellemzik az AMD SSD-jét.', 80000, 1),
(14, 'HP monitor', 'Használt LCD kijelzős monitor.', 14000, 2);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `products_to_categories`
--

CREATE TABLE `products_to_categories` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- A tábla adatainak kiíratása `products_to_categories`
--

INSERT INTO `products_to_categories` (`id`, `product_id`, `category_id`) VALUES
(1, 1, 1),
(2, 2, 1),
(4, 6, 1),
(5, 7, 2),
(6, 8, 3),
(7, 9, 3),
(8, 10, 1),
(9, 11, 3),
(10, 12, 4),
(11, 13, 4),
(12, 14, 3);

--
-- Indexek a kiírt táblákhoz
--

--
-- A tábla indexei `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- A tábla indexei `conditions`
--
ALTER TABLE `conditions`
  ADD PRIMARY KEY (`id`);

--
-- A tábla indexei `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- A tábla indexei `products_to_categories`
--
ALTER TABLE `products_to_categories`
  ADD PRIMARY KEY (`id`);

--
-- A kiírt táblák AUTO_INCREMENT értéke
--

--
-- AUTO_INCREMENT a táblához `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT a táblához `conditions`
--
ALTER TABLE `conditions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT a táblához `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
--
-- AUTO_INCREMENT a táblához `products_to_categories`
--
ALTER TABLE `products_to_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
