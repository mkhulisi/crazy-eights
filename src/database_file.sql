--  MySQL 5.5.5-10.6.16-MariaDB-0ubuntu0.22.04.1 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `cards`;
CREATE TABLE `cards` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `card_name` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `cards` (`id`, `card_name`) VALUES
(1,	'10_club.png'),
(2,	'3_heart.png'),
(3,	'5_heart.png'),
(4,	'8_club.png'),
(5,	'a_heart.png'),
(6,	'k_heart.png'),
(7,	'10_dice.png'),
(8,	'3_spade.png'),
(9,	'5_spade.png'),
(10,	'8_dice.png'),
(11,	'a_spade.png'),
(12,	'k_spade.png'),
(13,	'10_heart.png'),
(14,	'4_club.png'),
(15,	'6_club.png'),
(16,	'8_heart.png'),
(17,	'q_club.png'),
(18,	'10_spade.png'),
(19,	'4_dice.png'),
(20,	'6_dice.png'),
(21,	'8_spade.png'),
(22,	'q_dice.png'),
(23,	'2_club.png'),
(24,	'4_heart.png'),
(25,	'6_heart.png'),
(26,	'9_club.png'),
(27,	'j_club.png'),
(28,	'q_heart.png'),
(29,	'2_dice.png'),
(30,	'4_spade.png'),
(31,	'6_spade.png'),
(32,	'9_dice.png'),
(33,	'j_dice.png'),
(34,	'q_spade.png'),
(35,	'2_heart.png'),
(36,	'7_club.png'),
(37,	'9_heart.png'),
(38,	'j_heart.png'),
(39,	'2_spade.png'),
(40,	'7_dice.png'),
(41,	'9_spade.png'),
(42,	'j_spade.png'),
(43,	'3_club.png'),
(44,	'5_club.png'),
(45,	'7_heart.png'),
(46,	'a_club.png'),
(47,	'k_club.png'),
(48,	'3_dice.png'),
(49,	'5_dice.png'),
(50,	'7_spade.png'),
(51,	'a_dice.png'),
(52,	'k_dice.png');

-- 2024-05-25 10:32:01