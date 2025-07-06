-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : jeu. 03 juil. 2025 à 13:09
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.0.30

-- SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
-- START TRANSACTION;
-- SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `mc-legende`;
--

-- --------------------------------------------------------

CREATE DATABASE IF NOT EXISTS `mc-legende` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `mc-legende`;
--
-- Structure de la table `activites_admin`
--

CREATE TABLE `activites_admin` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `details` text DEFAULT NULL,
  `date_activite` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `activites_admin`
--

INSERT INTO `activites_admin` (`id`, `admin_id`, `action`, `details`, `date_activite`) VALUES
(1, 1, 'Publication des résultats', 'Quiz ID: ', '2025-06-17 01:24:46'),
(2, 1, 'Publication des résultats', 'Quiz ID: ', '2025-06-17 01:26:52'),
(3, 1, 'Publication des résultats', 'Quiz ID: 20', '2025-06-17 01:28:13'),
(4, 1, 'Ajout d\'une interrogation', 'Nom: Art Interrogation 3', '2025-06-17 01:37:54'),
(5, 1, 'Ajout d\'une notification', 'Détail : Nouvelle notification Historique ', '2025-06-17 01:53:35'),
(6, 1, 'Ajout d\'une notification', 'Détail : Art2 dfghjkllkjhgfd ', '2025-06-17 02:02:34'),
(7, 1, 'Import des questions', 'Détail : Array [ ', '2025-06-17 09:25:48'),
(8, 1, 'Import des questions', 'Détail : Array [ ', '2025-06-17 09:25:48'),
(9, 1, 'Import des questions', 'Détail : Dans quelle année a eu lieu 1960 ? [Histoire ', '2025-06-17 09:29:19'),
(10, 1, 'Publication des résultats', 'Quiz ID: 17', '2025-06-17 12:33:55'),
(11, 1, 'Modification d\'une interrogation', 'Détails : Art Interrogation 3,  art', '2025-06-17 12:37:36'),
(12, 1, 'Modification d\'un élève', 'Élève : batata kalombo caleb', '2025-06-17 21:02:19'),
(13, 1, 'Modification d\'un élève', 'Élève : batata kalombo caleb', '2025-06-17 21:07:52'),
(14, 1, 'Modification d\'un élève', 'Élève : Lumana Lukosho Jordan', '2025-06-17 21:10:07'),
(15, 1, 'Modification d\'un élève', 'Élève : batata kalombo caleb', '2025-06-17 21:34:20'),
(16, 1, 'Modification d\'un élève', 'Élève : batata kalombo caleb', '2025-06-17 21:35:05'),
(17, 1, 'Modification d\'un élève', 'Élève : batata kalombo caleb', '2025-06-17 21:41:07'),
(18, 1, 'Modification d\'un élève', 'Élève : batata kalombo caleb', '2025-06-17 21:42:10'),
(19, 1, 'Modification d\'un élève', 'Élève : Lumana Lukosho Jordan', '2025-06-17 21:44:23'),
(20, 1, 'Modification d\'un élève', 'Élève : batata kalombo caleb', '2025-06-17 22:35:18'),
(21, 1, 'Suppression d\'un admin', 'Nom :    ', '2025-06-17 22:57:00'),
(22, 1, 'Suppression d\'une notification', 'Détails :    ', '2025-06-17 22:59:42'),
(23, 1, 'Suppression d\'une notification', 'Titre : Musique | Message : Souriez', '2025-06-17 23:10:02'),
(24, 1, 'Suppression d\'un admin', 'Nom : batata kalombo caleb ', '2025-06-17 23:19:31'),
(25, 1, 'Connexion d\'un admin', 'Nom : Mukadi | Email : wwwmclegende@gmail.com', '2025-06-17 23:33:52'),
(26, 1, 'Modification d\'une interrogation', 'Détails : Art Interrogation 2-0,  art', '2025-06-17 23:42:13'),
(27, 1, 'Modification d\'un admin_simple', 'Nom : Tshala  Naomie', '2025-06-18 01:41:53'),
(28, 1, 'Connexion d\'un admin', 'Nom : Mukadi | Email : wwwmclegende@gmail.com', '2025-06-20 05:07:43'),
(29, 1, 'Publication des résultats', 'Quiz ID: 16', '2025-06-20 05:11:14'),
(30, 1, 'Publication des résultats', 'Quiz ID: 16', '2025-06-20 05:11:34'),
(31, 1, 'Ajout d\'une notification', 'Détail : Systeme hello ', '2025-06-20 05:54:13'),
(32, 1, 'Ajout d\'une notification', 'Détail : Systeme hi ', '2025-06-20 05:56:47'),
(33, 1, 'Connexion d\'un admin', 'Nom : Mukadi | Email : wwwmclegende@gmail.com', '2025-06-20 07:30:35'),
(34, 1, 'Ajout d\'une notification', 'Détail : Go Question ouvert ', '2025-06-20 07:31:56'),
(35, 1, 'Modification d\'un admin_simple', 'Nom : kazadi  Bienvenue', '2025-06-20 08:02:44'),
(36, 1, 'Modification d\'un admin_simple', 'Nom : kazadi  Bienvenue', '2025-06-20 08:15:41'),
(37, 1, 'Modification d\'un admin_simple', 'Nom : kazadi  Bienvenue', '2025-06-20 08:38:08'),
(38, 1, 'Modification d\'un admin_simple', 'Nom : kazadi  Bienvenue', '2025-06-20 23:50:54'),
(39, 1, 'Modification d\'un admin_simple', 'Nom : kazadi  Bienvenue', '2025-06-20 23:52:32'),
(40, 20, 'Import des questions', 'Catégorie :  Histoire ', '2025-06-21 00:03:12'),
(41, 20, 'Import des questions', 'Catégorie :  Histoire ', '2025-06-21 00:04:25'),
(42, 20, 'Import des questions', 'Catégorie :  Histoire ', '2025-06-21 00:06:22'),
(43, 20, 'Suppression d\'une question', 'Question : Combien font 5 + 7 ? | Catégorie : Mathématiques', '2025-06-21 00:14:57'),
(44, 20, 'Suppression d\'une question', 'Question : Dans quelle année a eu lieu 1960 ? | Catégorie : Histoire', '2025-06-21 00:15:46'),
(45, 20, 'Modification d\'une question', 'Question: Combien font 5 + 7 ?   |  Catégorie :Mathématique ', '2025-06-21 00:25:16'),
(46, 20, 'Suppression d\'une question', 'Question : Combien font 5 + 7 ? | Catégorie : Mathématique', '2025-06-21 00:25:27'),
(47, 1, 'Connexion d\'un admin', 'Nom : Mukadi | Email : wwwmclegende@gmail.com', '2025-06-21 11:29:49'),
(48, 1, 'Ajout d\'une notification', 'Détail : Go2 Question ouvert ', '2025-06-21 11:31:30'),
(49, 1, 'Ajout d\'une notification', 'Détail : Hi2 systeme ', '2025-06-21 13:07:10'),
(50, 1, 'Ajout d\'une notification', 'Détail : Go26 lkjhgfgjk ', '2025-06-21 13:30:57'),
(51, 1, 'Ajout d\'une notification', 'Détail : Go27 hgrtu ', '2025-06-21 15:41:39'),
(52, 1, 'Ajout d\'une notification', 'Détail : Go28 vcvbn,; ', '2025-06-21 15:43:05'),
(53, 1, 'Publication des résultats', 'Quiz ID: 27', '2025-06-21 15:49:20'),
(54, 1, 'Modification d\'une interrogation', 'Détails : catégorie musique interro 1,  musique', '2025-06-21 16:20:26'),
(55, 1, 'Ajout d\'une notification', 'Détail : Go29 kjhgfdfg ', '2025-06-21 16:45:32'),
(56, 1, 'Ajout d\'une notification', 'Détail : Go29 kjhgf ', '2025-06-21 16:47:07'),
(57, 1, 'Ajout d\'une notification', 'Détail : Go30 dfghjk ', '2025-06-21 16:55:32'),
(58, 1, 'Import de questions', 'Import réussi. 4 ligne(s) importée(s), 1 ignorée(s)', '2025-06-21 17:43:53'),
(59, 1, 'Import de questions', 'Import réussi. 4 ligne(s) importée(s), 1 ignorée(s)', '2025-06-21 17:48:32'),
(60, 1, 'Ajout d\'une interrogation', 'Nom: Art4, Catégrie: art', '2025-06-21 17:53:49'),
(61, 1, 'Ajout d\'une interrogation', 'Nom: Art5, Catégrie: art', '2025-06-21 17:59:02'),
(62, 1, 'Publication des résultats', 'Quiz ID: 34', '2025-06-21 18:02:35'),
(63, 1, 'Connexion d\'un admin', 'Nom : Mukadi | Email : wwwmclegende@gmail.com', '2025-06-21 21:37:37'),
(64, 1, 'Modification d\'une interrogation', 'Détails : Art4,  art', '2025-06-21 21:58:53'),
(65, 1, 'Modification d\'une interrogation', 'Détails : Art5,  art', '2025-06-21 22:19:55'),
(66, 1, 'Ajout d\'une interrogation', 'Nom: Musique12, Catégrie: musique', '2025-06-21 23:32:41'),
(67, 1, 'Ajout d\'une interrogation', 'Nom: Dance1, Catégrie: danse', '2025-06-21 23:58:21'),
(68, 1, 'Modification d\'une interrogation', 'Détails : Musique12,  musique', '2025-06-22 00:16:27'),
(69, 1, 'Modification d\'une interrogation', 'Détails : Musique12,  musique', '2025-06-22 00:16:53'),
(70, 1, 'Modification d\'une interrogation', 'Détails : Dance1,  danse', '2025-06-22 00:17:56'),
(71, 1, 'Modification d\'une interrogation', 'Détails : Dance1,  danse', '2025-06-22 00:43:06'),
(72, 1, 'Modification d\'une interrogation', 'Détails : Dance interrogation 2,  danse', '2025-06-22 00:46:04'),
(73, 1, 'Publication des résultats', 'Quiz ID: 37', '2025-06-22 00:56:36'),
(74, 1, 'Ajout d\'une interrogation', 'Nom: Artefact3, Catégrie: art', '2025-06-22 01:36:57'),
(75, 1, 'Ajout d\'une interrogation', 'Nom: Musique13, Catégrie: musique', '2025-06-22 01:45:44'),
(76, 1, 'Ajout d\'une interrogation', 'Nom: Dance4, Catégrie: danse', '2025-06-22 01:53:27'),
(77, 1, 'Modification d\'une interrogation', 'Détails : Musique13,  musique', '2025-06-22 02:22:51'),
(78, 1, 'Modification d\'une interrogation', 'Détails : Dance4,  danse', '2025-06-22 02:24:48'),
(79, 1, 'Ajout d\'une interrogation', 'Nom: Dance5, Catégrie: danse', '2025-06-22 02:46:56'),
(80, 20, 'Connexion d\'un admin', 'Nom : kazadi | Email : binvenuekazadi@gmail.com', '2025-06-22 14:40:13'),
(81, 1, 'Connexion d\'un admin', 'Nom : Mukadi | Email : wwwmclegende@gmail.com', '2025-06-22 14:45:06'),
(82, 20, 'Connexion d\'un admin', 'Nom : kazadi | Email : binvenuekazadi@gmail.com', '2025-06-22 14:50:22'),
(83, 20, 'Connexion d\'un admin', 'Nom : kazadi | Email : binvenuekazadi@gmail.com', '2025-06-22 17:49:21'),
(84, 1, 'Modification d\'une interrogation', 'Détails : Dance interrogation 1,  danse', '2025-06-22 22:01:40'),
(85, 1, 'Ajout d\'une interrogation', 'Nom: Culture général 1, Catégrie: culture générale', '2025-06-22 22:06:52'),
(86, 1, 'Modification d\'un élève', 'Élève : Django Muteba Jean', '2025-06-22 22:36:54'),
(87, 1, 'Modification d\'une interrogation', 'Détails : Culture général 1,  culture générale', '2025-06-22 22:48:48'),
(88, 1, 'Ajout d\'une interrogation', 'Nom: Art4_1, Catégrie: art', '2025-06-22 22:54:41'),
(89, 1, 'Connexion d\'un admin', 'Nom : Mukadi | Email : wwwmclegende@gmail.com', '2025-06-23 01:06:56'),
(90, 1, 'Modification d\'une interrogation', 'Détails : Art4_1,  art', '2025-06-23 01:07:27'),
(91, 1, 'Modification d\'une interrogation', 'Détails : Art4_1,  art', '2025-06-23 01:07:57'),
(92, 1, 'Modification d\'une interrogation', 'Détails : Art4_1,  art', '2025-06-23 01:33:02'),
(93, 1, 'Ajout d\'une interrogation', 'Nom: Art4_2, Catégrie: art', '2025-06-23 01:52:59'),
(94, 1, 'Modification d\'une interrogation', 'Détails : Art4_2,  art', '2025-06-23 02:23:00'),
(95, 1, 'Ajout d\'une interrogation', 'Nom: Art4_3, Catégrie: art', '2025-06-23 02:27:48'),
(96, 1, 'Ajout d\'une interrogation', 'Nom: Culture général 1_2, Catégrie: culture générale', '2025-06-23 02:36:32'),
(97, 1, 'Publication des résultats', 'Quiz ID: 46', '2025-06-23 02:49:06'),
(98, 1, 'Ajout d\'une interrogation', 'Nom: Culture général 1_3, Catégrie: culture générale', '2025-06-23 02:50:21'),
(99, 20, 'Connexion d\'un admin', 'Nom : kazadi | Email : binvenuekazadi@gmail.com', '2025-06-23 03:20:25'),
(100, 20, 'Import de questions', 'Import réussi. 5 ligne(s) importée(s), 0 ignorée(s)', '2025-06-23 03:21:49'),
(101, 1, 'Connexion d\'un admin', 'Nom : Mukadi | Email : wwwmclegende@gmail.com', '2025-06-23 12:33:24'),
(102, 1, 'Ajout d\'une interrogation', 'Nom: Musique_1_2, Catégrie: musique', '2025-06-23 12:36:45'),
(103, 1, 'Connexion d\'un admin', 'Nom : Mukadi | Email : wwwmclegende@gmail.com', '2025-06-28 12:14:46'),
(104, 1, 'Publication des résultats', 'Quiz ID: 48', '2025-06-28 12:18:43'),
(105, 1, 'Connexion d\'un admin', 'Nom : Mukadi | Email : wwwmclegende@gmail.com', '2025-06-30 23:21:02'),
(106, 1, 'Ajout d\'une interrogation', 'Nom: Art Interrogation 2-0_0, Catégrie: art', '2025-06-30 23:23:09'),
(107, 1, 'Modification d\'un élève', 'Élève : Django Muteba Jean', '2025-07-01 10:50:29'),
(108, 1, 'Modification d\'une interrogation', 'Détails : Art Interrogation 2-0_0,  art', '2025-07-01 12:22:56'),
(109, 1, 'Ajout d\'une interrogation', 'Nom: Art Interrogation 2-0_1, Catégrie: art', '2025-07-01 12:31:22'),
(110, 1, 'Connexion d\'un admin', 'Nom : Mukadi | Email : wwwmclegende@gmail.com', '2025-07-01 12:42:53'),
(111, 1, 'Ajout d\'une interrogation', 'Nom: Art Interrogation 2-0_2, Catégrie: art', '2025-07-01 12:43:28'),
(112, 1, 'Connexion d\'un admin', 'Nom : Mukadi | Email : wwwmclegende@gmail.com', '2025-07-01 14:33:40'),
(113, 1, 'Ajout d\'une interrogation', 'Nom: Art Interrogation 2-0_3, Catégrie: art', '2025-07-01 14:35:08'),
(114, 1, 'Connexion d\'un admin', 'Nom : Mukadi | Email : wwwmclegende@gmail.com', '2025-07-01 14:48:51'),
(115, 21, 'Connexion d\'un admin', 'Nom : Tshala | Email : tshalanaomie@gmail.com', '2025-07-01 14:49:47'),
(116, 1, 'Modification d\'un admin_simple', 'Nom : Tshala  Naomie', '2025-07-01 14:50:58'),
(117, 1, 'Modification d\'un admin_simple', 'Nom : Tshala  Naomie', '2025-07-01 14:51:21'),
(118, 21, 'Import de questions', 'Import réussi. 5 ligne(s) importée(s), 0 ignorée(s)', '2025-07-01 15:15:32'),
(119, 1, 'Suppression d\'un admin', 'Nom :   ', '2025-07-01 22:09:11'),
(120, 1, 'Suppression d\'un admin', 'Nom :   ', '2025-07-01 22:09:39'),
(121, 1, 'Suppression d\'un admin', 'Nom :   ', '2025-07-01 22:10:29'),
(122, 1, 'Suppression d\'un admin', 'Nom :   ', '2025-07-01 22:12:57'),
(123, 1, 'Ajout d\'un élève', 'Élève : Mukadi Mayola Franck', '2025-07-01 22:21:19'),
(124, 1, 'Suppression d\'un élève', 'Nom : Mukadi Mayola Franck ', '2025-07-01 22:22:47'),
(125, 1, 'Ajout d\'un administrateur', 'Détail : Mukadi Franck ', '2025-07-01 22:23:27'),
(126, 1, 'Connexion d\'un admin', 'Nom : Mukadi | Email : wwwmclegende@gmail.com', '2025-07-01 22:25:27'),
(127, 1, 'Suppression d\'un admin', 'Nom : Mukadi  Franck', '2025-07-01 22:44:22'),
(128, 1, 'Ajout d\'une notification', 'Détail : sec ghjk ', '2025-07-01 23:01:10'),
(129, 1, 'Ajout d\'une notification', 'Détail : gfd jhgfd ', '2025-07-01 23:04:56'),
(130, 1, 'Ajout d\'une notification', 'Détail : sdfg sdfg ', '2025-07-01 23:06:30'),
(131, 1, 'Suppression d\'une notification', 'Titre : sec | Message : ghjk', '2025-07-01 23:15:11'),
(132, 1, 'Ajout d\'un élève', 'Élève : Mukadi Mayola Franck', '2025-07-01 23:28:01'),
(133, 1, 'Suppression d\'un élève', 'Nom : Mukadi Mayola Franck', '2025-07-01 23:38:54'),
(134, 1, 'Ajout d\'une notification', 'Détail : sec dfghuio ', '2025-07-02 08:24:31'),
(135, 1, 'Suppression d\'une notification', 'Titre : sec | Message : dfghuio', '2025-07-02 08:28:16'),
(136, 1, 'Ajout d\'une notification', 'Détail : sec kjhgf ', '2025-07-02 08:29:17'),
(137, 1, 'Modification d\'une interrogation', 'Détails : Art Interrogation 2-0S,  art', '2025-07-02 11:38:39'),
(138, 1, 'Suppression d\'une interrogation', 'Détail : catégorie musique interro 1 musique', '2025-07-02 11:49:33'),
(139, 1, 'Suppression d\'une interrogation', 'Détail : Art Interrogation 2-0_0 art', '2025-07-02 11:52:08'),
(140, 1, 'Ajout d\'une interrogation', 'Nom: Art4_1_0, Catégorie: art', '2025-07-02 11:53:07'),
(141, 1, 'Modification d\'une question', 'Détails : Combien de continents y a-t-il sur Terre ?,  Culture générale', '2025-07-02 12:09:52'),
(142, 1, 'Suppression d\'une question', 'Question : Combien font 5 + 7 ? | Catégorie : Mathématiques', '2025-07-02 12:10:11'),
(143, 1, 'Suppression d\'une question', 'Question : Combien font 5 + 7 ? | Catégorie : Mathématiques', '2025-07-02 12:10:26'),
(144, 1, 'Suppression d\'une question', 'Question : Combien font 5 + 7 ? | Catégorie : Mathématiques', '2025-07-02 12:22:47'),
(145, 1, 'Suppression de toutes les questions ', ' ', '2025-07-02 12:26:26'),
(146, 1, 'Import de questions', 'Import réussi. 51 ligne(s) importée(s), 0 ignorée(s)', '2025-07-02 12:28:41'),
(147, 1, 'Import de questions', 'Import réussi. 5 ligne(s) importée(s), 0 ignorée(s)', '2025-07-02 12:29:43'),
(148, 1, 'Ajout d\'une interrogation', 'Nom: test@example.com\' OR \'1\'=\'1, Catégorie: culture générale', '2025-07-02 13:11:40'),
(149, 1, 'Modification d\'une interrogation', 'Détails : <script>alert(\'XSS\')</script>,  culture générale', '2025-07-02 13:13:22'),
(150, 1, 'Suppression d\'une interrogation', 'Détail : alert(\'XSS\') culture générale', '2025-07-02 22:13:42'),
(151, 1, 'Connexion d\'un admin', 'Nom : Mukadi | Email : wwwmclegende@gmail.com', '2025-07-02 22:21:14'),
(152, 1, 'Modification d\'une interrogation', 'Détails : Art4_1_0,  art', '2025-07-02 23:19:31'),
(153, 1, 'Modification d\'une interrogation', 'Détails : Art4_1_0,  art', '2025-07-02 23:21:17'),
(154, 1, 'Ajout d\'une interrogation', 'Nom: A_0, Catégorie: art', '2025-07-02 23:25:12'),
(155, 1, 'Ajout d\'une interrogation', 'Nom: A_1, Catégorie: art', '2025-07-02 23:27:06'),
(156, 1, 'Modification d\'une interrogation', 'Détails : A_1_0,  art', '2025-07-02 23:32:47'),
(157, 1, 'Ajout d\'une interrogation', 'Nom: Musique_0, Catégorie: musique', '2025-07-02 23:38:40'),
(158, 1, 'Modification d\'une interrogation', 'Détails : Musique_0,  musique', '2025-07-02 23:39:45'),
(159, 1, 'Suppression d\'une interrogation', 'Détail : Musique_0 musique', '2025-07-02 23:40:38'),
(160, 1, 'Ajout d\'une interrogation', 'Nom: Musique_0, Catégorie: art', '2025-07-02 23:41:17'),
(161, 1, 'Modification d\'une interrogation', 'Détails : Musique_0,  art', '2025-07-02 23:42:12'),
(162, 1, 'Modification d\'une interrogation', 'Détails : Musique_0,  musique', '2025-07-02 23:43:13'),
(163, 21, 'Connexion d\'un admin', 'Nom : Tshala | Email : tshalanaomie@gmail.com', '2025-07-03 09:25:49'),
(164, 1, 'Connexion d\'un admin', 'Nom : Mukadi | Email : wwwmclegende@gmail.com', '2025-07-03 09:29:32'),
(165, 1, 'Connexion d\'un admin', 'Nom : Mukadi | Email : wwwmclegende@gmail.com', '2025-07-03 10:31:51'),
(166, 1, 'Modification d\'une interrogation', 'Détails : A_0,  art', '2025-07-03 10:34:26'),
(167, 1, 'Publication des résultats', 'Quiz ID: 55', '2025-07-03 10:40:20'),
(168, 1, 'Import de questions', 'Import réussi. 5 ligne(s) importée(s), 0 ignorée(s)', '2025-07-03 10:41:42'),
(169, 1, 'Suppression d\'une question', 'Question : Combien de continents y a-t-il sur Terre ? | Catégorie : Culture générale', '2025-07-03 10:42:37'),
(170, 21, 'Connexion d\'un admin', 'Nom : Tshala | Email : tshalanaomie@gmail.com', '2025-07-03 10:45:22'),
(171, 1, 'Connexion d\'un admin', 'Nom : Mukadi | Email : wwwmclegende@gmail.com', '2025-07-03 10:46:10'),
(172, 1, 'Modification d\'un admin_simple', 'Nom : Tshala  Naomie', '2025-07-03 10:46:23'),
(173, 1, 'Connexion d\'un admin', 'Nom : Mukadi | Email : wwwmclegende@gmail.com', '2025-07-03 11:27:30'),
(174, 1, 'Ajout d\'une interrogation', 'Nom: Musique_1_2_3, Catégorie: musique', '2025-07-03 11:28:17'),
(175, 1, 'Ajout d\'une interrogation', 'Nom: JedA, Catégorie: art', '2025-07-03 11:46:15'),
(176, 1, 'Ajout d\'une interrogation', 'Nom: Mue_1, Catégorie: musique', '2025-07-03 11:58:51');

-- --------------------------------------------------------

--
-- Structure de la table `eleves`
--

CREATE TABLE `eleves` (
  `id` int(11) NOT NULL,
  `utilisateur_id` int(11) NOT NULL,
  `etablissement` varchar(255) NOT NULL,
  `section` varchar(100) NOT NULL,
  `adresse_ecole` varchar(255) DEFAULT NULL,
  `categorie_activite` enum('musique','danse','culture générale','art','autre') NOT NULL,
  `pays` varchar(255) DEFAULT NULL,
  `ville_province` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `eleves`
--

INSERT INTO `eleves` (`id`, `utilisateur_id`, `etablissement`, `section`, `adresse_ecole`, `categorie_activite`, `pays`, `ville_province`) VALUES
(2, 4, 'college saint etienne', 'scientifique', 'limete 3e rue industrielle', 'art', NULL, NULL),
(4, 7, 'college sainte marie', 'litterraire', 'kalamu 3e rue kauka', 'musique', 'RDC', 'Lubumbashi'),
(7, 17, 'college saint etienne', 'Mecanique', 'kalamu 3e rue kauka', 'art', 'RDC', 'Kinshasa'),
(10, 22, 'Saint Raphael', 'Scientifique', '1ère rue limete', 'danse', NULL, NULL),
(11, 23, 'college saint etienne', 'scientifique', 'limete 3e rue industrielle', 'danse', NULL, NULL),
(12, 24, 'college saint etienne', 'Scientifique', 'limete 3e rue industrielle', 'musique', 'RDC', 'Lubumbashi'),
(14, 26, 'college sainte marie', '', 'kalamu 3e rue kauka', 'culture générale', 'RDC', 'Kinshasa'),
(17, 30, 'college sainte Raphael', 'litterraire', 'limete 1e rue industrielle', 'danse', 'RDC', 'Kinshasa'),
(18, 31, 'Saint Raphael', 'Scientifique', '1ère rue limete', 'musique', 'RDC', 'Kinshasa');

-- --------------------------------------------------------

--
-- Structure de la table `interrogation_questions`
--

CREATE TABLE `interrogation_questions` (
  `id` int(11) NOT NULL,
  `utilisateur_id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `interrogation_utilisateur`
--

CREATE TABLE `interrogation_utilisateur` (
  `id` int(11) NOT NULL,
  `utilisateur_id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `debut` datetime DEFAULT current_timestamp(),
  `dernier_acces` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `etat` enum('en_cours','termine','annule','triche') DEFAULT 'en_cours',
  `score` int(11) DEFAULT NULL,
  `duree` int(11) DEFAULT NULL,
  `fin` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `interrogation_utilisateur`
--

INSERT INTO `interrogation_utilisateur` (`id`, `utilisateur_id`, `quiz_id`, `debut`, `dernier_acces`, `etat`, `score`, `duree`, `fin`) VALUES
(1, 4, 43, '2025-06-23 01:10:26', '2025-06-23 01:20:50', 'termine', NULL, NULL, '2025-06-23'),
(2, 17, 43, '2025-06-23 01:21:33', '2025-06-23 01:34:11', 'triche', NULL, NULL, '2025-06-23'),
(3, 17, 44, '2025-06-23 01:53:15', '2025-06-23 02:04:25', 'triche', NULL, NULL, '2025-06-23'),
(4, 4, 44, '2025-06-23 02:23:21', '2025-06-23 02:23:50', 'triche', NULL, NULL, '2025-06-23'),
(5, 4, 45, '2025-06-23 02:28:02', '2025-06-23 02:29:32', 'triche', NULL, NULL, '2025-06-23'),
(6, 17, 45, '2025-06-23 02:30:12', '2025-06-23 02:30:48', 'triche', NULL, NULL, '2025-06-23'),
(7, 26, 46, '2025-06-23 02:37:30', '2025-06-23 02:39:10', 'termine', NULL, NULL, '2025-06-23'),
(8, 24, 46, '2025-06-23 02:42:37', '2025-06-23 02:47:38', 'termine', NULL, NULL, '2025-06-23'),
(9, 24, 47, '2025-06-23 02:50:37', '2025-06-23 02:52:38', 'termine', NULL, NULL, '2025-06-23'),
(10, 7, 48, '2025-06-23 12:37:47', '2025-06-23 12:40:34', 'termine', NULL, NULL, '2025-06-23'),
(13, 17, 50, '2025-07-01 12:32:04', '2025-07-01 12:33:12', 'triche', NULL, NULL, '2025-07-01'),
(14, 4, 50, '2025-07-01 12:34:01', '2025-07-01 12:34:52', 'triche', NULL, NULL, '2025-07-01'),
(15, 17, 51, '2025-07-01 12:43:41', '2025-07-01 12:45:36', 'termine', NULL, NULL, '2025-07-01'),
(16, 4, 51, '2025-07-01 12:47:05', '2025-07-01 12:47:46', 'triche', NULL, NULL, '2025-07-01'),
(17, 4, 52, '2025-07-01 14:37:19', '2025-07-01 14:42:19', 'termine', NULL, NULL, '2025-07-01'),
(18, 17, 52, '2025-07-01 14:44:16', '2025-07-01 14:45:26', 'termine', NULL, NULL, '2025-07-01'),
(19, 17, 53, '2025-07-02 23:44:21', '2025-07-02 23:48:10', 'termine', NULL, NULL, '2025-07-02'),
(20, 17, 55, '2025-07-03 10:36:19', '2025-07-03 10:38:23', 'termine', NULL, NULL, '2025-07-03'),
(21, 4, 55, '2025-07-03 10:48:30', '2025-07-03 10:48:46', 'triche', NULL, NULL, '2025-07-03'),
(22, 7, 61, '2025-07-03 12:00:36', '2025-07-03 12:02:15', 'termine', NULL, NULL, '2025-07-03');

-- --------------------------------------------------------

--
-- Structure de la table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `utilisateur_id` int(11) DEFAULT NULL,
  `quiz_id` int(11) DEFAULT NULL,
  `type` enum('quiz','resultat','systeme') NOT NULL DEFAULT 'systeme',
  `titre` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `lien` varchar(255) DEFAULT NULL,
  `lue` tinyint(1) NOT NULL DEFAULT 0,
  `date_creation` datetime NOT NULL DEFAULT current_timestamp(),
  `role_destinataire` varchar(50) DEFAULT NULL,
  `est_generale` tinyint(1) DEFAULT 0,
  `categorie` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `notifications`
--

INSERT INTO `notifications` (`id`, `utilisateur_id`, `quiz_id`, `type`, `titre`, `message`, `lien`, `lue`, `date_creation`, `role_destinataire`, `est_generale`, `categorie`) VALUES
(12, NULL, NULL, 'resultat', 'Nouvelle notification', 'Resultats disponible', NULL, 1, '2025-06-10 23:37:55', 'eleve', 1, NULL),
(13, 17, NULL, 'quiz', 'Nouvelle interrogation disponible', 'L\'interrogation \'Evo\' est disponible.', 'mes_interro.php?id=18', 1, '2025-06-11 01:19:31', NULL, 0, NULL),
(14, 7, NULL, 'quiz', 'Nouvelle interrogation disponible', 'L\'interrogation \'Mue\' est disponible.', 'quiz.php?id=19', 1, '2025-06-11 01:53:43', NULL, 0, NULL),
(15, 17, 16, 'quiz', 'Nouvelle interrogation disponible', 'L\'interrogation \'catégorie culture interro 1\' est disponible.', 'quiz.php?id=16', 1, '2025-06-11 02:04:06', NULL, 0, NULL),
(16, 7, 16, 'quiz', 'Nouvelle interrogation disponible', 'L\'interrogation \'catégorie culture interro 1\' est disponible.', 'quiz.php?id=16', 1, '2025-06-11 02:04:40', NULL, 0, NULL),
(17, NULL, NULL, '', 'Nouvelle notification', 'Musique2', NULL, 1, '2025-06-11 02:36:36', 'eleve', 1, 'musique'),
(18, NULL, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : Musique', 'mes_interros.php', 1, '2025-06-11 02:40:08', 'eleve', 1, 'musique'),
(19, 17, NULL, 'quiz', 'Nouvelle interrogation disponible', 'L\'interrogation \'catégorie musique interro 1\' est disponible.', 'quiz.php?id=17', 1, '2025-06-11 02:40:17', NULL, 0, NULL),
(20, NULL, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : musique', 'mes_interro.php', 1, '2025-06-11 03:07:02', 'eleve', 1, 'musique'),
(21, NULL, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : art', 'mes_interro.php', 1, '2025-06-11 03:07:02', 'eleve', 1, 'art'),
(22, 7, NULL, 'quiz', 'Nouvelle interrogation disponible', 'L\'interrogation \'catégorie musique interro 1\' est disponible.', 'quiz.php?id=17', 1, '2025-06-11 03:10:17', NULL, 0, NULL),
(23, NULL, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : culture générale', 'mes_interro.php', 0, '2025-06-11 03:22:04', 'eleve', 1, 'culture générale'),
(24, NULL, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : art', 'mes_interro.php', 1, '2025-06-11 03:22:04', 'eleve', 1, 'art'),
(25, 7, NULL, 'quiz', 'Nouvelle interrogation disponible', 'L\'interrogation \'Evo\' est disponible.', 'quiz.php?id=18', 1, '2025-06-11 03:22:14', NULL, 0, NULL),
(26, 17, 20, 'quiz', 'Nouvelle interrogation disponible', 'L\'interrogation \'bn\' est disponible.', 'quiz.php?id=20', 1, '2025-06-11 03:28:02', NULL, 0, NULL),
(27, 7, 20, 'quiz', 'Nouvelle interrogation disponible', 'L\'interrogation \'bn\' est disponible.', 'quiz.php?id=20', 1, '2025-06-11 03:28:46', NULL, 0, NULL),
(28, NULL, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : art', 'mes_interro.php', 1, '2025-06-11 09:25:04', 'eleve', 1, 'art'),
(29, NULL, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : musique', 'mes_interro.php', 1, '2025-06-11 09:31:04', 'eleve', 1, 'musique'),
(30, NULL, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : musique', 'mes_interro.php', 1, '2025-06-11 09:52:03', 'eleve', 1, 'musique'),
(31, NULL, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : art', 'mes_interro.php', 1, '2025-06-11 09:54:06', 'eleve', 1, 'art'),
(32, NULL, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : musique', 'mes_interro.php', 1, '2025-06-11 10:20:28', 'eleve', 1, 'musique'),
(33, NULL, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : culture générale', 'mes_interro.php', 0, '2025-06-11 10:22:21', 'eleve', 1, 'culture générale'),
(37, 4, 20, 'quiz', 'Nouvelle interrogation disponible', 'L\'interrogation \'bn\' est disponible.', 'quiz.php?id=20', 1, '2025-06-11 12:23:41', NULL, 0, NULL),
(38, 4, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : art', 'mes_interro.php', 1, '2025-06-11 12:40:06', 'eleve', 0, NULL),
(39, 17, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : art', 'mes_interro.php', 1, '2025-06-11 12:40:06', 'eleve', 0, NULL),
(40, 20, NULL, '', 'Final', 'prenez gardes svp', 'notifications.php', 0, '2025-06-11 14:57:09', NULL, 0, NULL),
(41, 21, NULL, '', 'Final', 'prenez gardes svp', 'notifications.php', 0, '2025-06-11 14:57:09', NULL, 0, NULL),
(45, 4, NULL, '', 'Artefac', 'Venez me voir ici', 'notifications.php', 1, '2025-06-11 15:48:36', 'eleve', 1, 'art'),
(47, 7, 21, 'quiz', 'Nouvelle interrogation disponible', 'L\'interrogation \'Mue\' est disponible.', 'quiz.php?id=21', 1, '2025-06-11 16:01:36', NULL, 0, NULL),
(48, 7, NULL, 'quiz', 'Nouvelle interrogation disponible', 'L\'interrogation \'Mat\' est disponible.', 'quiz.php?id=23', 1, '2025-06-11 16:17:46', NULL, 0, NULL),
(49, 7, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : musique', 'mes_interro.php', 1, '2025-06-13 00:17:56', 'eleve', 1, 'musique'),
(51, 7, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : musique', 'mes_interro.php', 1, '2025-06-13 00:32:48', 'eleve', 1, 'musique'),
(52, 7, NULL, 'quiz', 'Nouvelle interrogation disponible', 'L\'interrogation \'Test\' est disponible.', 'quiz.php?id=24', 1, '2025-06-13 00:33:03', NULL, 0, NULL),
(53, 7, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : musique', 'mes_interro.php', 1, '2025-06-13 00:39:36', 'eleve', 1, 'musique'),
(54, 7, NULL, 'quiz', 'Nouvelle interrogation disponible', 'L\'interrogation \'Test\' est disponible.', 'mes_interro.php?id=25', 1, '2025-06-13 00:39:45', NULL, 0, NULL),
(55, 7, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : musique', 'mes_interro.php', 1, '2025-06-13 00:48:30', 'eleve', 1, 'musique'),
(57, 7, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : musique', 'mes_interro.php', 1, '2025-06-13 00:55:50', 'eleve', 1, 'musique'),
(58, 7, 27, 'quiz', 'Nouvelle interrogation disponible', 'L\'interrogation \'Test\' est disponible.', 'mes_interro.php?id=27', 1, '2025-06-13 00:55:55', NULL, 0, NULL),
(59, 7, NULL, '', 'Privé', 'T\'es le gagnant du concours', 'notifications.php', 1, '2025-06-13 20:50:22', NULL, 0, NULL),
(60, 7, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : musique', 'mes_interro.php', 1, '2025-06-15 00:40:31', 'eleve', 1, 'musique'),
(61, 7, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : musique', 'mes_interro.php', 0, '2025-06-15 00:52:36', 'eleve', 1, 'musique'),
(62, 7, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : musique', 'mes_interro.php', 0, '2025-06-15 01:03:15', 'eleve', 1, 'musique'),
(63, 4, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : art', 'mes_interro.php', 1, '2025-06-15 02:33:07', 'eleve', 1, 'art'),
(64, 17, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : art', 'mes_interro.php', 1, '2025-06-15 02:33:07', 'eleve', 1, 'art'),
(65, 4, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : art', 'mes_interro.php', 1, '2025-06-15 02:54:52', 'eleve', 1, 'art'),
(66, 17, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : art', 'mes_interro.php', 0, '2025-06-15 02:54:52', 'eleve', 1, 'art'),
(67, 4, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : art', 'mes_interro.php', 1, '2025-06-15 19:09:05', 'eleve', 1, 'art'),
(68, 17, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : art', 'mes_interro.php', 1, '2025-06-15 19:09:05', 'eleve', 1, 'art'),
(69, 7, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : musique', 'mes_interro.php', 1, '2025-06-15 19:40:43', 'eleve', 1, 'musique'),
(70, 7, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : musique', 'mes_interro.php', 1, '2025-06-15 22:13:07', 'eleve', 1, 'musique'),
(71, 4, 28, 'quiz', 'Nouvelle interrogation disponible', 'L\'interrogation \'Art Interrogation 2\' est prête à être commencée.', 'mes_interro.php?id=28', 1, '2025-06-15 22:49:49', NULL, 0, NULL),
(72, 4, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : art', 'mes_interro.php', 1, '2025-06-15 23:15:58', 'eleve', 1, 'art'),
(73, 17, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : art', 'mes_interro.php', 1, '2025-06-15 23:15:58', 'eleve', 1, 'art'),
(74, 17, 28, 'quiz', 'Nouvelle interrogation disponible', 'L\'interrogation \'Art Interrogation 2\' est prête à être commencée.', 'mes_interro.php?id=28', 1, '2025-06-15 23:16:27', NULL, 0, NULL),
(75, 22, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : danse', 'mes_interro.php', 1, '2025-06-16 11:02:06', 'eleve', 1, 'danse'),
(76, 22, 29, 'quiz', 'Nouvelle interrogation disponible', 'L\'interrogation \'Dance interrogation 1\' est prête à être commencée.', 'mes_interro.php?id=29', 1, '2025-06-16 11:02:12', NULL, 0, NULL),
(77, 22, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : danse', 'mes_interro.php', 1, '2025-06-16 11:34:25', 'eleve', 1, 'danse'),
(78, 22, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : danse', 'mes_interro.php', 1, '2025-06-16 11:57:12', 'eleve', 1, 'danse'),
(79, 22, 31, 'quiz', 'Nouvelle interrogation disponible', 'L\'interrogation \'Dance interrogation 3\' est prête à être commencée.', 'mes_interro.php?id=31', 1, '2025-06-16 11:57:20', NULL, 0, NULL),
(80, 7, 27, 'resultat', 'Résultats disponibles', 'Les résultats de l’interrogation \"Test\" ont été publiés. Consulte ton espace.', 'mes_resultats.php', 1, '2025-06-16 22:38:13', 'eleve', 1, 'resultat'),
(81, 7, 21, 'resultat', 'Résultats disponibles', 'Les résultats de l’interrogation \"Mue\" ont été publiés. Consulte ton espace.', 'mes_resultats.php', 1, '2025-06-16 22:38:42', 'eleve', 1, 'resultat'),
(82, 22, 30, 'resultat', 'Résultats disponibles', 'Les résultats de l’interrogation \"Dance interrogation 2\" ont été publiés. Consulte ton espace.', 'resultats.php', 1, '2025-06-16 22:43:45', 'eleve', 1, 'resultat'),
(83, 4, 28, 'resultat', 'Résultats disponibles', 'Les résultats de l’interrogation \"Art Interrogation 2\" ont été publiés. Consulte ton espace.', 'resultats.php', 1, '2025-06-16 22:46:33', 'eleve', 1, 'resultat'),
(84, 17, 28, 'resultat', 'Résultats disponibles', 'Les résultats de l’interrogation \"Art Interrogation 2\" ont été publiés. Consulte ton espace.', 'resultats.php', 1, '2025-06-16 22:46:33', 'eleve', 1, 'resultat'),
(85, 22, 31, 'resultat', 'Résultats disponibles', 'Les résultats de l’interrogation \"Dance interrogation 3\" sont maintenant disponibles.', 'mes_resultats.php', 1, '2025-06-16 23:10:14', 'eleve', 1, 'resultat'),
(86, 22, 30, 'resultat', 'Résultats disponibles', 'Les résultats de l’interrogation \"Dance interrogation 2\" sont maintenant disponibles.', 'resultats.php', 1, '2025-06-16 23:25:57', 'eleve', 1, 'resultat'),
(87, 22, 29, 'resultat', 'Résultats disponibles', 'Les résultats de l’interrogation \"Dance interrogation 1\" sont maintenant disponibles.', 'resultats.php', 1, '2025-06-17 01:21:34', 'eleve', 1, 'resultat'),
(88, 7, 27, 'resultat', 'Résultats disponibles', 'Les résultats de l’interrogation \"Test\" sont maintenant disponibles.', 'resultats.php', 0, '2025-06-17 01:24:46', 'eleve', 1, 'resultat'),
(89, 7, 21, 'resultat', 'Résultats disponibles', 'Les résultats de l’interrogation \"Mue\" sont maintenant disponibles.', 'resultats.php', 1, '2025-06-17 01:26:52', 'eleve', 1, 'resultat'),
(90, 17, 20, 'resultat', 'Résultats disponibles', 'Les résultats de l’interrogation \"bn\" sont maintenant disponibles.', 'resultats.php', 0, '2025-06-17 01:28:13', 'eleve', 1, 'resultat'),
(91, 17, NULL, '', 'Nouvelle notification', 'Historique', 'notifications.php', 1, '2025-06-17 01:52:52', NULL, 0, NULL),
(92, 17, NULL, '', 'Nouvelle notification', 'Historique', 'notifications.php', 1, '2025-06-17 01:53:35', NULL, 0, NULL),
(94, 7, NULL, 'resultat', 'Résultats disponibles', 'Les résultats de l’interrogation \"catégorie musique interro 1\" sont maintenant disponibles.', 'resultats.php', 1, '2025-06-17 12:33:55', 'eleve', 1, 'resultat'),
(95, 4, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : art', 'mes_interro.php', 1, '2025-06-17 12:37:36', 'eleve', 1, 'art'),
(96, 17, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : art', 'mes_interro.php', 1, '2025-06-17 12:37:37', 'eleve', 1, 'art'),
(97, 17, 32, 'quiz', 'Nouvelle interrogation disponible', 'L\'interrogation \'Art Interrogation 3\' est prête à être commencée.', 'mes_interro.php?id=32', 1, '2025-06-17 12:38:13', NULL, 0, NULL),
(98, 4, NULL, '', 'Systeme', 'hello', 'notifications.php', 1, '2025-06-20 05:54:13', 'eleve', 1, 'art'),
(99, 17, NULL, '', 'Systeme', 'hello', 'notifications.php', 1, '2025-06-20 05:54:13', 'eleve', 1, 'art'),
(100, 4, NULL, '', 'Systeme', 'hi', 'notifications.php', 1, '2025-06-20 05:56:47', 'eleve', 1, NULL),
(101, 7, NULL, '', 'Systeme', 'hi', 'notifications.php', 1, '2025-06-20 05:56:47', 'eleve', 1, NULL),
(102, 15, NULL, '', 'Systeme', 'hi', 'notifications.php', 1, '2025-06-20 05:56:47', 'eleve', 1, NULL),
(103, 17, NULL, '', 'Systeme', 'hi', 'notifications.php', 1, '2025-06-20 05:56:47', 'eleve', 1, NULL),
(104, 22, NULL, '', 'Systeme', 'hi', 'notifications.php', 1, '2025-06-20 05:56:47', 'eleve', 1, NULL),
(105, 20, NULL, '', 'Go', 'Question ouvert', 'notifications.php', 1, '2025-06-20 07:31:56', 'admin_simple', 1, NULL),
(106, 21, NULL, '', 'Go', 'Question ouvert', 'notifications.php', 1, '2025-06-20 07:31:56', 'admin_simple', 1, NULL),
(107, 20, NULL, '', 'Go2', 'Question ouvert', 'notifications.php', 1, '2025-06-21 11:31:30', 'admin_simple', 1, NULL),
(108, 21, NULL, '', 'Go2', 'Question ouvert', 'notifications.php', 1, '2025-06-21 11:31:30', 'admin_simple', 1, NULL),
(109, 4, NULL, '', 'Hi2', 'systeme', 'notifications.php', 1, '2025-06-21 13:07:10', 'eleve', 1, NULL),
(110, 7, NULL, '', 'Hi2', 'systeme', 'notifications.php', 1, '2025-06-21 13:07:10', 'eleve', 1, NULL),
(111, 15, NULL, '', 'Hi2', 'systeme', 'notifications.php', 1, '2025-06-21 13:07:10', 'eleve', 1, NULL),
(112, 17, NULL, '', 'Hi2', 'systeme', 'notifications.php', 1, '2025-06-21 13:07:10', 'eleve', 1, NULL),
(113, 22, NULL, '', 'Hi2', 'systeme', 'notifications.php', 0, '2025-06-21 13:07:10', 'eleve', 1, NULL),
(114, 23, NULL, '', 'Hi2', 'systeme', 'notifications.php', 0, '2025-06-21 13:07:10', 'eleve', 1, NULL),
(115, 7, NULL, '', 'Go26', 'lkjhgfgjk', 'notifications.php', 1, '2025-06-21 13:30:57', NULL, 0, NULL),
(116, 7, NULL, '', 'Go27', 'hgrtu', 'notifications.php', 1, '2025-06-21 15:41:39', NULL, 0, NULL),
(117, 4, NULL, '', 'Go28', 'vcvbn,;', 'notifications.php', 1, '2025-06-21 15:43:04', 'eleve', 1, NULL),
(118, 7, NULL, '', 'Go28', 'vcvbn,;', 'notifications.php', 1, '2025-06-21 15:43:05', 'eleve', 1, NULL),
(119, 15, NULL, '', 'Go28', 'vcvbn,;', 'notifications.php', 1, '2025-06-21 15:43:05', 'eleve', 1, NULL),
(120, 17, NULL, '', 'Go28', 'vcvbn,;', 'notifications.php', 1, '2025-06-21 15:43:05', 'eleve', 1, NULL),
(121, 22, NULL, '', 'Go28', 'vcvbn,;', 'notifications.php', 0, '2025-06-21 15:43:05', 'eleve', 1, NULL),
(122, 23, NULL, '', 'Go28', 'vcvbn,;', 'notifications.php', 1, '2025-06-21 15:43:05', 'eleve', 1, NULL),
(123, 7, 27, 'resultat', 'Résultats disponibles', 'Les résultats de l’interrogation \"Test\" sont maintenant disponibles.', 'resultats.php', 1, '2025-06-21 15:49:20', 'eleve', 1, 'resultat'),
(124, 7, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : musique', 'mes_interro.php', 1, '2025-06-21 16:20:27', 'eleve', 1, 'musique'),
(125, 7, NULL, '', 'Go29', 'kjhgfdfg', 'notifications.php', 1, '2025-06-21 16:45:32', 'eleve', 1, 'musique'),
(126, 4, NULL, '', 'Go29', 'kjhgf', 'notifications.php', 1, '2025-06-21 16:47:07', 'eleve', 1, 'art'),
(127, 17, NULL, '', 'Go29', 'kjhgf', 'notifications.php', 1, '2025-06-21 16:47:07', 'eleve', 1, 'art'),
(128, 4, NULL, '', 'Go30', 'dfghjk', 'notifications.php', 1, '2025-06-21 16:55:32', 'eleve', 1, 'art'),
(129, 17, NULL, '', 'Go30', 'dfghjk', 'notifications.php', 0, '2025-06-21 16:55:32', 'eleve', 1, 'art'),
(130, 4, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : art', 'mes_interro.php', 1, '2025-06-21 17:53:50', 'eleve', 1, 'art'),
(131, 17, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : art', 'mes_interro.php', 1, '2025-06-21 17:53:50', 'eleve', 1, 'art'),
(132, 4, 34, 'quiz', 'Nouvelle interrogation disponible', 'L\'interrogation \'Art4\' est prête à être commencée.', 'mes_interro.php?id=34', 0, '2025-06-21 17:53:55', NULL, 0, NULL),
(133, 4, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : art', 'mes_interro.php', 1, '2025-06-21 17:59:02', 'eleve', 1, 'art'),
(134, 17, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : art', 'mes_interro.php', 1, '2025-06-21 17:59:02', 'eleve', 1, 'art'),
(135, 4, 34, 'resultat', 'Résultats disponibles', 'Les résultats de l’interrogation \"Art4\" sont maintenant disponibles.', 'resultats.php', 0, '2025-06-21 18:02:34', 'eleve', 1, 'resultat'),
(136, 4, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : art', 'mes_interro.php', 0, '2025-06-21 21:58:53', 'eleve', 1, 'art'),
(137, 17, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : art', 'mes_interro.php', 1, '2025-06-21 21:58:53', 'eleve', 1, 'art'),
(138, 17, 34, 'quiz', 'Nouvelle interrogation disponible', 'L\'interrogation \'Art4\' est prête à être commencée.', 'mes_interro.php?id=34', 0, '2025-06-21 21:59:41', NULL, 0, NULL),
(139, 4, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : art', 'mes_interro.php', 0, '2025-06-21 22:19:55', 'eleve', 1, 'art'),
(140, 17, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : art', 'mes_interro.php', 1, '2025-06-21 22:19:55', 'eleve', 1, 'art'),
(141, 4, 35, 'quiz', 'Nouvelle interrogation disponible', 'L\'interrogation \'Art5\' est prête à être commencée.', 'mes_interro.php?id=35', 0, '2025-06-21 22:28:29', NULL, 0, NULL),
(142, 7, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : musique', 'mes_interro.php', 1, '2025-06-21 23:32:42', 'eleve', 1, 'musique'),
(143, 7, 36, 'quiz', 'Nouvelle interrogation disponible', 'L\'interrogation \'Musique12\' est prête à être commencée.', 'mes_interro.php?id=36', 0, '2025-06-21 23:33:22', NULL, 0, NULL),
(144, 22, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : danse', 'mes_interro.php', 0, '2025-06-21 23:58:21', 'eleve', 1, 'danse'),
(145, 23, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : danse', 'mes_interro.php', 1, '2025-06-21 23:58:21', 'eleve', 1, 'danse'),
(146, 23, 37, 'quiz', 'Nouvelle interrogation disponible', 'L\'interrogation \'Dance1\' est prête à être commencée.', 'mes_interro.php?id=37', 0, '2025-06-21 23:59:29', NULL, 0, NULL),
(147, 7, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : musique', 'mes_interro.php', 1, '2025-06-22 00:16:53', 'eleve', 1, 'musique'),
(148, 22, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : danse', 'mes_interro.php', 0, '2025-06-22 00:17:56', 'eleve', 1, 'danse'),
(149, 23, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : danse', 'mes_interro.php', 1, '2025-06-22 00:17:56', 'eleve', 1, 'danse'),
(150, 22, 37, 'quiz', 'Nouvelle interrogation disponible', 'L\'interrogation \'Dance1\' est prête à être commencée.', 'mes_interro.php?id=37', 0, '2025-06-22 00:29:05', NULL, 0, NULL),
(151, 22, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : danse', 'mes_interro.php', 0, '2025-06-22 00:43:06', 'eleve', 1, 'danse'),
(152, 23, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : danse', 'mes_interro.php', 1, '2025-06-22 00:43:06', 'eleve', 1, 'danse'),
(153, 22, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : danse', 'mes_interro.php', 0, '2025-06-22 00:46:04', 'eleve', 1, 'danse'),
(154, 23, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : danse', 'mes_interro.php', 1, '2025-06-22 00:46:04', 'eleve', 1, 'danse'),
(155, 23, 30, 'quiz', 'Nouvelle interrogation disponible', 'L\'interrogation \'Dance interrogation 2\' est prête à être commencée.', 'mes_interro.php?id=30', 0, '2025-06-22 00:53:41', NULL, 0, NULL),
(156, 23, 37, 'resultat', 'Résultats disponibles', 'Les résultats de l’interrogation \"Dance1\" sont maintenant disponibles.', 'resultats.php', 1, '2025-06-22 00:56:36', 'eleve', 1, NULL),
(157, 22, 37, 'resultat', 'Résultats disponibles', 'Les résultats de l’interrogation \"Dance1\" sont maintenant disponibles.', 'resultats.php', 0, '2025-06-22 00:56:36', 'eleve', 1, NULL),
(158, 4, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : art', 'mes_interro.php', 0, '2025-06-22 01:36:57', 'eleve', 1, 'art'),
(159, 17, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : art', 'mes_interro.php', 1, '2025-06-22 01:36:57', 'eleve', 1, 'art'),
(160, 17, 38, 'quiz', 'Nouvelle interrogation disponible', 'L\'interrogation \'Artefact3\' est prête à être commencée.', 'mes_interro.php?id=38', 0, '2025-06-22 01:37:17', NULL, 0, NULL),
(161, 4, 38, 'quiz', 'Nouvelle interrogation disponible', 'L\'interrogation \'Artefact3\' est prête à être commencée.', 'mes_interro.php?id=38', 0, '2025-06-22 01:41:20', NULL, 0, NULL),
(162, 7, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : musique', 'mes_interro.php', 1, '2025-06-22 01:45:44', 'eleve', 1, 'musique'),
(163, 7, 39, 'quiz', 'Nouvelle interrogation disponible', 'L\'interrogation \'Musique13\' est prête à être commencée.', 'mes_interro.php?id=39', 0, '2025-06-22 01:46:04', NULL, 0, NULL),
(164, 22, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : danse', 'mes_interro.php', 1, '2025-06-22 01:53:27', 'eleve', 1, 'danse'),
(165, 23, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : danse', 'mes_interro.php', 0, '2025-06-22 01:53:27', 'eleve', 1, 'danse'),
(166, 23, 40, 'quiz', 'Nouvelle interrogation disponible', 'L\'interrogation \'Dance4\' est prête à être commencée.', 'mes_interro.php?id=40', 0, '2025-06-22 01:53:56', NULL, 0, NULL),
(167, 7, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : musique', 'mes_interro.php', 1, '2025-06-22 02:22:51', 'eleve', 1, 'musique'),
(168, 22, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : danse', 'mes_interro.php', 0, '2025-06-22 02:24:48', 'eleve', 1, 'danse'),
(169, 23, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : danse', 'mes_interro.php', 0, '2025-06-22 02:24:48', 'eleve', 1, 'danse'),
(170, 22, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : danse', 'mes_interro.php', 0, '2025-06-22 02:46:57', 'eleve', 1, 'danse'),
(171, 23, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : danse', 'mes_interro.php', 0, '2025-06-22 02:46:57', 'eleve', 1, 'danse'),
(172, 22, 41, 'quiz', 'Nouvelle interrogation disponible', 'L\'interrogation \'Dance5\' est prête à être commencée.', 'mes_interro.php?id=41', 0, '2025-06-22 02:47:09', NULL, 0, NULL),
(173, 23, 41, 'quiz', 'Nouvelle interrogation disponible', 'L\'interrogation \'Dance5\' est prête à être commencée.', 'mes_interro.php?id=41', 0, '2025-06-22 02:51:35', NULL, 0, NULL),
(174, 22, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : danse', 'mes_interro.php', 0, '2025-06-22 22:01:41', 'eleve', 1, 'danse'),
(175, 23, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : danse', 'mes_interro.php', 0, '2025-06-22 22:01:41', 'eleve', 1, 'danse'),
(176, 24, 42, 'quiz', 'Nouvelle interrogation disponible', 'L\'interrogation \'Culture général 1\' est prête à être commencée.', 'mes_interro.php?id=42', 0, '2025-06-22 22:10:16', NULL, 0, NULL),
(177, 26, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : culture générale', 'mes_interro.php', 1, '2025-06-22 22:48:48', 'eleve', 1, 'culture générale'),
(178, 26, 42, 'quiz', 'Nouvelle interrogation disponible', 'L\'interrogation \'Culture général 1\' est prête à être commencée.', 'mes_interro.php?id=42', 0, '2025-06-22 22:49:17', NULL, 0, NULL),
(179, 4, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : art', 'mes_interro.php', 1, '2025-06-22 22:54:41', 'eleve', 1, 'art'),
(180, 17, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : art', 'mes_interro.php', 1, '2025-06-22 22:54:42', 'eleve', 1, 'art'),
(181, 17, 43, 'quiz', 'Nouvelle interrogation disponible', 'L\'interrogation \'Art4_1\' est prête à être commencée.', 'mes_interro.php?id=43', 0, '2025-06-22 22:55:17', NULL, 0, NULL),
(182, 4, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : art', 'mes_interro.php', 1, '2025-06-23 01:07:57', 'eleve', 1, 'art'),
(183, 17, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : art', 'mes_interro.php', 0, '2025-06-23 01:07:57', 'eleve', 1, 'art'),
(184, 4, 43, 'quiz', 'Nouvelle interrogation disponible', 'L\'interrogation \'Art4_1\' est prête à être commencée.', 'mes_interro.php?id=43', 0, '2025-06-23 01:08:03', NULL, 0, NULL),
(185, 4, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : art', 'mes_interro.php', 1, '2025-06-23 01:33:02', 'eleve', 1, 'art'),
(186, 17, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : art', 'mes_interro.php', 0, '2025-06-23 01:33:02', 'eleve', 1, 'art'),
(187, 4, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : art', 'mes_interro.php', 1, '2025-06-23 01:53:00', 'eleve', 1, 'art'),
(188, 17, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : art', 'mes_interro.php', 1, '2025-06-23 01:53:00', 'eleve', 1, 'art'),
(189, 17, 44, 'quiz', 'Nouvelle interrogation disponible', 'L\'interrogation \'Art4_2\' est prête à être commencée.', 'mes_interro.php?id=44', 0, '2025-06-23 01:53:39', NULL, 0, NULL),
(190, 4, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : art', 'mes_interro.php', 1, '2025-06-23 02:23:00', 'eleve', 1, 'art'),
(191, 17, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : art', 'mes_interro.php', 1, '2025-06-23 02:23:00', 'eleve', 1, 'art'),
(192, 4, 44, 'quiz', 'Nouvelle interrogation disponible', 'L\'interrogation \'Art4_2\' est prête à être commencée.', 'mes_interro.php?id=44', 0, '2025-06-23 02:23:13', NULL, 0, NULL),
(193, 4, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : art', 'mes_interro.php', 1, '2025-06-23 02:27:48', 'eleve', 1, 'art'),
(194, 17, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : art', 'mes_interro.php', 1, '2025-06-23 02:27:48', 'eleve', 1, 'art'),
(195, 26, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : culture générale', 'mes_interro.php', 1, '2025-06-23 02:36:32', 'eleve', 1, 'culture générale'),
(196, 26, 46, 'quiz', 'Nouvelle interrogation disponible', 'L\'interrogation \'Culture général 1_2\' est prête à être commencée.', 'mes_interro.php?id=46', 0, '2025-06-23 02:37:06', NULL, 0, NULL),
(197, 24, 46, 'quiz', 'Nouvelle interrogation disponible', 'L\'interrogation \'Culture général 1_2\' est prête à être commencée.', 'mes_interro.php?id=46', 0, '2025-06-23 02:42:09', NULL, 0, NULL),
(198, 26, 46, 'resultat', 'Résultats disponibles', 'Les résultats de l’interrogation \"Culture général 1_2\" sont maintenant disponibles.', 'resultats.php', 0, '2025-06-23 02:49:06', 'eleve', 1, ''),
(199, 24, 46, 'resultat', 'Résultats disponibles', 'Les résultats de l’interrogation \"Culture général 1_2\" sont maintenant disponibles.', 'resultats.php', 0, '2025-06-23 02:49:06', 'eleve', 1, ''),
(200, 24, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : culture générale', 'mes_interro.php', 1, '2025-06-23 02:50:22', 'eleve', 1, 'culture générale'),
(201, 26, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : culture générale', 'mes_interro.php', 0, '2025-06-23 02:50:22', 'eleve', 1, 'culture générale'),
(202, 7, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : musique', 'mes_interro.php', 1, '2025-06-23 12:36:45', 'eleve', 1, 'musique'),
(203, 7, 48, 'quiz', 'Nouvelle interrogation disponible', 'L\'interrogation \'Musique_1_2\' est prête à être commencée.', 'mes_interro.php?id=48', 0, '2025-06-23 12:36:50', NULL, 0, NULL),
(204, 7, 48, 'resultat', 'Résultats disponibles', 'Les résultats de l’interrogation \"Musique_1_2\" sont maintenant disponibles.', 'resultats.php', 0, '2025-06-28 12:18:43', 'eleve', 1, ''),
(205, 4, NULL, 'quiz', 'Nouvelle interrogation disponible', 'L\'interrogation \'Art Interrogation 2-0_0\' est prête à être commencée.', 'mes_interro.php?id=49', 0, '2025-07-01 12:12:26', NULL, 0, NULL),
(206, 4, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : art', 'mes_interro.php', 1, '2025-07-01 12:22:57', 'eleve', 1, 'art'),
(207, 17, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : art', 'mes_interro.php', 1, '2025-07-01 12:22:57', 'eleve', 1, 'art'),
(208, 17, NULL, 'quiz', 'Nouvelle interrogation disponible', 'L\'interrogation \'Art Interrogation 2-0_0\' est prête à être commencée.', 'mes_interro.php?id=49', 0, '2025-07-01 12:23:36', NULL, 0, NULL),
(209, 4, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : art', 'mes_interro.php', 1, '2025-07-01 12:31:22', 'eleve', 1, 'art'),
(210, 17, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : art', 'mes_interro.php', 1, '2025-07-01 12:31:22', 'eleve', 1, 'art'),
(211, 4, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : art', 'mes_interro.php', 1, '2025-07-01 12:43:28', 'eleve', 1, 'art'),
(212, 17, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : art', 'mes_interro.php', 1, '2025-07-01 12:43:28', 'eleve', 1, 'art'),
(213, 4, 50, 'quiz', 'Nouvelle interrogation disponible', 'L\'interrogation \'Art Interrogation 2-0_1\' est prête à être commencée.', 'mes_interro.php?id=50', 0, '2025-07-01 12:46:54', NULL, 0, NULL),
(214, 4, 51, 'quiz', 'Nouvelle interrogation disponible', 'L\'interrogation \'Art Interrogation 2-0_2\' est prête à être commencée.', 'mes_interro.php?id=51', 0, '2025-07-01 12:50:55', NULL, 0, NULL),
(215, 4, 52, 'quiz', 'Nouvelle interrogation disponible', 'L\'interrogation \'Art Interrogation 2-0_3\' est prête à être commencée.', 'mes_interro.php?id=52', 0, '2025-07-01 14:37:07', NULL, 0, NULL),
(216, 17, 52, 'quiz', 'Nouvelle interrogation disponible', 'L\'interrogation \'Art Interrogation 2-0_3\' est prête à être commencée.', 'mes_interro.php?id=52', 0, '2025-07-01 14:44:09', NULL, 0, NULL),
(218, 17, NULL, '', 'gfd', 'jhgfd', 'notifications.php', 0, '2025-07-01 23:04:56', NULL, 0, 'art'),
(219, 4, NULL, '', 'sdfg', 'sdfg', 'notifications.php', 1, '2025-07-01 23:06:30', 'eleve', 1, 'art'),
(220, 17, NULL, '', 'sdfg', 'sdfg', 'notifications.php', 1, '2025-07-01 23:06:30', 'eleve', 1, 'art'),
(222, 17, NULL, '', 'sec', 'kjhgf', 'notifications.php', 1, '2025-07-02 08:29:17', 'eleve', 1, 'art'),
(223, 4, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : art', 'mes_interro.php', 1, '2025-07-02 13:10:55', 'eleve', 1, 'art'),
(224, 17, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : art', 'mes_interro.php', 1, '2025-07-02 13:10:56', 'eleve', 1, 'art'),
(225, 26, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : culture générale', 'mes_interro.php', 0, '2025-07-02 13:11:40', 'eleve', 1, 'culture générale'),
(226, 4, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : art', 'mes_interro.php', 1, '2025-07-02 23:40:38', 'eleve', 1, 'art'),
(227, 17, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : art', 'mes_interro.php', 1, '2025-07-02 23:40:38', 'eleve', 1, 'art'),
(228, 17, 53, 'quiz', 'Nouvelle interrogation disponible', 'L\'interrogation \'A_1_0\' est prête à être commencée.', 'mes_interro.php?id=53', 0, '2025-07-02 23:43:50', NULL, 0, NULL),
(229, 17, 55, 'quiz', 'Nouvelle interrogation disponible', 'L\'interrogation \'A_0\' est prête à être commencée.', 'mes_interro.php?id=55', 0, '2025-07-03 10:36:07', NULL, 0, NULL),
(230, 17, 55, 'resultat', 'Résultats disponibles', 'Les résultats de l’interrogation \"A_0\" sont maintenant disponibles.', 'resultats.php', 0, '2025-07-03 10:40:20', 'eleve', 1, ''),
(231, 4, 55, 'quiz', 'Nouvelle interrogation disponible', 'L\'interrogation \'A_0\' est prête à être commencée.', 'mes_interro.php?id=55', 0, '2025-07-03 10:48:21', NULL, 0, NULL),
(232, 31, 59, 'quiz', 'Nouvelle interrogation disponible', 'L\'interrogation \'Musique_1_2_3\' est prête à être commencée.', 'mes_interro.php?id=59', 0, '2025-07-03 11:29:19', NULL, 0, NULL),
(233, 4, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : art', 'mes_interro.php', 0, '2025-07-03 11:46:15', 'eleve', 1, 'art'),
(234, 17, NULL, 'quiz', 'Nouvelle interrogation disponible', 'Une nouvelle interrogation est disponible dans la catégorie : art', 'mes_interro.php', 1, '2025-07-03 11:46:15', 'eleve', 1, 'art'),
(235, 17, 60, 'quiz', 'Nouvelle interrogation disponible', 'L\'interrogation \'JedA\' est prête à être commencée.', 'mes_interro.php?id=60', 0, '2025-07-03 11:46:54', NULL, 0, NULL),
(236, 7, 61, 'quiz', 'Nouvelle interrogation disponible', 'L\'interrogation \'Mue_1\' est prête à être commencée.', 'mes_interro.php?id=61', 0, '2025-07-03 12:00:05', NULL, 0, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `questions`
--

CREATE TABLE `questions` (
  `id` int(11) NOT NULL,
  `texte_question` text NOT NULL,
  `option_1` varchar(255) DEFAULT NULL,
  `option_2` varchar(255) DEFAULT NULL,
  `option_3` varchar(255) DEFAULT NULL,
  `option_4` varchar(255) DEFAULT NULL,
  `bonne_reponse` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `categorie` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `questions`
--

INSERT INTO `questions` (`id`, `texte_question`, `option_1`, `option_2`, `option_3`, `option_4`, `bonne_reponse`, `created_at`, `categorie`) VALUES
(280, 'Quel est le plus grand désert du monde ?', 'Sahara', 'Gobi', 'Kalahari', 'Antarctique', 'D', '2025-07-02 12:28:39', 'Culture générale'),
(281, 'Quel est le plus grand désert du monde ?', 'Sahara', 'Gobi', 'Kalahari', 'Antarctique', 'D', '2025-07-02 12:28:39', 'Culture générale'),
(282, 'Quel pays a inventé la pizza ?', 'Espagne', 'France', 'Grèce', 'Italie', 'D', '2025-07-02 12:28:39', 'Culture générale'),
(283, 'Quelle est la monnaie du Japon ?', 'Won', 'Yuan', 'Dollar', 'Yen', 'D', '2025-07-02 12:28:39', 'Culture générale'),
(284, 'Quel océan est le plus petit ?', 'Atlantique', 'Indien', 'Arctique', 'Pacifique', 'C', '2025-07-02 12:28:39', 'Culture générale'),
(285, 'Quelle est la langue officielle du Brésil ?', 'Espagnol', 'Portugais', 'Français', 'Anglais', 'B', '2025-07-02 12:28:39', 'Culture générale'),
(287, 'Quel est l\'animal national de l\'Australie ?', 'Koala', 'Kangourou', 'Émeu', 'Ornithorynque', 'B', '2025-07-02 12:28:39', 'Culture générale'),
(288, 'Quelle est la plus haute montagne d\'Afrique ?', 'Mont Kenya', 'Kilimandjaro', 'Mont Stanley', 'Mont Meru', 'B', '2025-07-02 12:28:39', 'Culture générale'),
(289, 'Quel pays est surnommé \"le pays du soleil levant\" ?', 'Chine', 'Corée', 'Thaïlande', 'Japon', 'D', '2025-07-02 12:28:40', 'Culture générale'),
(290, 'Quelle est la ville la plus peuplée du monde ?', 'New York', 'Delhi', 'Shanghai', 'Tokyo', 'D', '2025-07-02 12:28:40', 'Culture générale'),
(291, 'Quel fleuve traverse Paris ?', 'Le Rhin', 'Le Danube', 'La Tamise', 'La Seine', 'D', '2025-07-02 12:28:40', 'Culture générale'),
(292, 'Quelle est la plus grande île du monde ?', 'Australie', 'Groenland', 'Madagascar', 'Bornéo', 'B', '2025-07-02 12:28:40', 'Culture générale'),
(293, 'Quel élément chimique a pour symbole \"Au\" ?', 'Argent', 'Cuivre', 'Fer', 'Or', 'D', '2025-07-02 12:28:40', 'Culture générale'),
(294, 'En quelle année a été fondé l\'ONU ?', '1919', '1945', '1950', '1960', 'B', '2025-07-02 12:28:40', 'Culture générale'),
(295, 'Quel groupe a chanté \"Bohemian Rhapsody\" ?', 'The Beatles', 'Pink Floyd', 'Queen', 'Rolling Stones', 'C', '2025-07-02 12:28:40', 'Musique'),
(296, 'Quel instrument joue Yo-Yo Ma ?', 'Piano', 'Violon', 'Violoncelle', 'Flûte', 'C', '2025-07-02 12:28:40', 'Musique'),
(297, 'Quel compositeur était sourd ?', 'Mozart', 'Beethoven', 'Bach', 'Chopin', 'B', '2025-07-02 12:28:40', 'Musique'),
(298, 'Quel genre musical est né à La Nouvelle-Orléans ?', 'Blues', 'Jazz', 'Rock', 'Hip-hop', 'B', '2025-07-02 12:28:40', 'Musique'),
(299, 'Qui est la \"Reine de la Soul\" ?', 'Whitney Houston', 'Aretha Franklin', 'Diana Ross', 'Tina Turner', 'B', '2025-07-02 12:28:40', 'Musique'),
(300, 'Quel pays a donné naissance au reggae ?', 'Cuba', 'Jamaïque', 'Brésil', 'Nigeria', 'B', '2025-07-02 12:28:40', 'Musique'),
(301, 'Quel est l\'instrument principal dans un orchestre de flamenco ?', 'Piano', 'Guitare', 'Violon', 'Accordéon', 'B', '2025-07-02 12:28:40', 'Musique'),
(302, 'Qui a composé \"Les Quatre Saisons\" ?', 'Mozart', 'Vivaldi', 'Beethoven', 'Haydn', 'B', '2025-07-02 12:28:40', 'Musique'),
(303, 'Quel groupe a sorti l\'album \"The Dark Side of the Moon\" ?', 'Led Zeppelin', 'Pink Floyd', 'The Who', 'The Doors', 'B', '2025-07-02 12:28:40', 'Musique'),
(304, 'Quel est le nom de la harpe africaine ?', 'Kora', 'Balafon', 'Djembé', 'Ngoni', 'A', '2025-07-02 12:28:40', 'Musique'),
(305, 'Qui a chanté \"Like a Virgin\" ?', 'Cher', 'Madonna', 'Cyndi Lauper', 'Whitney Houston', 'B', '2025-07-02 12:28:40', 'Musique'),
(306, 'Quel instrument est associé à Miles Davis ?', 'Saxophone', 'Trompette', 'Trombone', 'Clarinette', 'B', '2025-07-02 12:28:40', 'Musique'),
(307, 'Quel style musical est associé à Johann Strauss ?', 'Opéra', 'Symphonie', 'Valse', 'Concerto', 'C', '2025-07-02 12:28:40', 'Musique'),
(308, 'Quel groupe comprend Bono comme chanteur ?', 'Coldplay', 'U2', 'Radiohead', 'The Police', 'B', '2025-07-02 12:28:40', 'Musique'),
(309, 'Quel pays organise le festival de Woodstock ?', 'Angleterre', 'France', 'États-Unis', 'Canada', 'C', '2025-07-02 12:28:41', 'Musique'),
(310, 'Quel pays est à l\'origine du tango ?', 'Brésil', 'Argentine', 'Cuba', 'Espagne', 'B', '2025-07-02 12:28:41', 'Danse'),
(311, 'Quelle danse utilise des chaussures à claquettes ?', 'Flamenco', 'Claquette', 'Breakdance', 'Salsa', 'B', '2025-07-02 12:28:41', 'Danse'),
(312, 'Quel ballet met en scène un prince et un cygne ?', 'Casse-Noisette', 'Giselle', 'Le Lac des cygnes', 'Coppélia', 'C', '2025-07-02 12:28:41', 'Danse'),
(313, 'Quelle danse est associée à la Nouvelle-Orléans ?', 'Samba', 'Jazz', 'Zumba', 'Charleston', 'D', '2025-07-02 12:28:41', 'Danse'),
(314, 'Quel pays a popularisé la salsa ?', 'Brésil', 'Cuba', 'Mexique', 'Colombie', 'B', '2025-07-02 12:28:41', 'Danse'),
(315, 'Quelle danse utilise un poteau vertical ?', 'Pole dance', 'Breakdance', 'Hip-hop', 'Contemporain', 'A', '2025-07-02 12:28:41', 'Danse'),
(316, 'Quel style de danse est associé à Michael Jackson ?', 'Locking', 'Popping', 'Moonwalk', 'Tutting', 'C', '2025-07-02 12:28:41', 'Danse'),
(317, 'Quelle danse traditionnelle vient d\'Hawaï ?', 'Samba', 'Hula', 'Flamenco', 'Bharatanatyam', 'B', '2025-07-02 12:28:41', 'Danse'),
(318, 'Quel ballet comprend la \"Danse des petits cygnes\" ?', 'La Belle au bois dormant', 'Le Lac des cygnes', 'Casse-Noisette', 'Roméo et Juliette', 'B', '2025-07-02 12:28:41', 'Danse'),
(319, 'Quelle danse est originaire de la République dominicaine ?', 'Merengue', 'Tango', 'Salsa', 'Bachata', 'A', '2025-07-02 12:28:41', 'Danse'),
(320, 'Qui a peint \"La Joconde\" ?', 'Michel-Ange', 'Raphaël', 'Léonard de Vinci', 'Van Gogh', 'C', '2025-07-02 12:28:41', 'Art'),
(321, 'Qui a peint \"La Joconde\" ?', 'Michel-Ange', 'Raphaël', 'Léonard de Vinci', 'Van Gogh', 'C', '2025-07-02 12:28:41', 'Art'),
(322, 'Quel mouvement artistique est associé à Monet ?', 'Cubisme', 'Surréalisme', 'Impressionnisme', 'Baroque', 'C', '2025-07-02 12:28:41', 'Art'),
(323, 'Où se trouve la statue de la Liberté ?', 'Los Angeles', 'Washington', 'New York', 'Chicago', 'C', '2025-07-02 12:28:41', 'Art'),
(324, 'Qui a sculpté \"Le Penseur\" ?', 'Donatello', 'Rodin', 'Michel-Ange', 'Bernini', 'B', '2025-07-02 12:28:41', 'Art'),
(325, 'Quelle technique utilise de la cire chaude ?', 'Aquarelle', 'Tempera', 'Fresque', 'Encaustique', 'D', '2025-07-02 12:28:41', 'Art'),
(326, 'Quel architecte a conçu la Sagrada Familia ?', 'Gaudi', 'Le Corbusier', 'Frank Lloyd Wright', 'Mies van der Rohe', 'A', '2025-07-02 12:28:41', 'Art'),
(327, 'Quel artiste a créé les \"Mobiles\" ?', 'Dali', 'Picasso', 'Calder', 'Kandinsky', 'C', '2025-07-02 12:28:41', 'Art'),
(328, 'Où se trouve le musée du Louvre ?', 'Rome', 'Londres', 'Paris', 'Berlin', 'C', '2025-07-02 12:28:41', 'Art'),
(329, 'Qui a peint \"Le Cri\" ?', 'Van Gogh', 'Munch', 'Picasso', 'Dali', 'B', '2025-07-02 12:28:41', 'Art'),
(330, 'Quelle période artistique suit la Renaissance ?', 'Gothique', 'Baroque', 'Romantique', 'Néoclassique', 'B', '2025-07-02 12:28:41', 'Art'),
(331, 'Quelle est la capitale du Congo ?', 'Kinshasa', 'Brazzaville', 'Lubumbashi', 'Goma', 'A', '2025-07-02 12:29:42', 'Culture générale'),
(332, 'Quel est le plus grand océan ?', 'Atlantique', 'Indien', 'Arctique', 'Pacifique', 'D', '2025-07-02 12:29:43', 'Culture générale'),
(333, 'Combien font 5 + 7 ?', '10', '11', '12', '13', 'C', '2025-07-02 12:29:43', 'Mathématiques'),
(334, 'Qui a écrit Le Père Goriot ?', 'Hugo', 'Zola', 'Balzac', 'Flaubert', 'C', '2025-07-02 12:29:43', 'Littérature'),
(335, 'Dans quelle année a eu lieu 1960 ?', '1945', '1950', '1960', '1970', 'C', '2025-07-02 12:29:43', 'Histoire'),
(336, 'Quelle est la capitale du Congo ?', 'Kinshasa', 'Brazzaville', 'Lubumbashi', 'Goma', 'A', '2025-07-03 10:41:42', 'Culture générale'),
(337, 'Quel est le plus grand océan ?', 'Atlantique', 'Indien', 'Arctique', 'Pacifique', 'D', '2025-07-03 10:41:42', 'Culture générale'),
(338, 'Combien font 5 + 7 ?', '10', '11', '12', '13', 'C', '2025-07-03 10:41:42', 'Mathématiques'),
(339, 'Qui a écrit Le Père Goriot ?', 'Hugo', 'Zola', 'Balzac', 'Flaubert', 'C', '2025-07-03 10:41:42', 'Littérature'),
(340, 'Dans quelle année a eu lieu 1960 ?', '1945', '1950', '1960', '1970', 'C', '2025-07-03 10:41:42', 'Histoire');

-- --------------------------------------------------------

--
-- Structure de la table `quiz`
--

CREATE TABLE `quiz` (
  `id` int(11) NOT NULL,
  `titre` varchar(255) DEFAULT NULL,
  `date_lancement` datetime DEFAULT NULL,
  `duree_totale` int(11) DEFAULT NULL,
  `temps_par_question` int(11) DEFAULT NULL,
  `categorie` varchar(100) DEFAULT NULL,
  `statut` varchar(20) DEFAULT 'inactif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `quiz`
--

INSERT INTO `quiz` (`id`, `titre`, `date_lancement`, `duree_totale`, `temps_par_question`, `categorie`, `statut`) VALUES
(16, 'catégorie culture interro 1', '2025-06-11 10:22:00', 10, 1, 'culture générale', 'inactif'),
(20, 'bn', '2025-06-15 19:08:00', 20, 1, 'art', 'inactif'),
(21, 'Mue', '2025-06-15 19:40:00', 8, 1, 'musique', 'inactif'),
(27, 'Test', '2025-06-15 22:12:00', 10, 1, 'musique', 'inactif'),
(28, 'Art Interrogation 2-0S', '2025-06-15 23:38:00', 20, 10, 'art', 'inactif'),
(29, 'Dance interrogation 1', '2025-06-22 22:01:00', 20, 5, 'danse', 'inactif'),
(30, 'Dance interrogation 2', '2025-06-22 00:46:00', 15, 5, 'danse', 'inactif'),
(31, 'Dance interrogation 3', '2025-06-16 11:57:00', 15, 5, 'danse', 'inactif'),
(32, 'Art Interrogation 3', '2025-06-17 12:36:00', 15, 5, 'art', 'inactif'),
(33, 'Art Interrogation 3', '2025-06-17 10:30:00', 15, 5, 'art', 'inactif'),
(34, 'Art4', '2025-06-21 21:58:00', 15, 10, 'art', 'inactif'),
(35, 'Art5', '2025-06-21 22:19:00', 15, 5, 'art', 'inactif'),
(36, 'Musique12', '2025-06-22 00:16:00', 15, 5, 'musique', 'inactif'),
(37, 'Dance1', '2025-06-22 00:42:00', 14, 5, 'danse', 'inactif'),
(38, 'Artefact3', '2025-06-22 01:36:00', 15, 7, 'art', 'inactif'),
(39, 'Musique13', '2025-06-22 02:22:00', 15, 7, 'musique', 'inactif'),
(40, 'Dance4', '2025-06-22 02:24:00', 15, 7, 'danse', 'inactif'),
(41, 'Dance5', '2025-06-22 02:46:00', 15, 6, 'danse', 'inactif'),
(42, 'Culture général 1', '2025-06-22 22:48:00', 15, 6, 'culture générale', 'inactif'),
(43, 'Art4_1', '2025-06-23 01:32:00', 15, 6, 'art', 'inactif'),
(44, 'Art4_2', '2025-06-23 02:22:00', 20, 6, 'art', 'inactif'),
(45, 'Art4_3', '2025-06-23 02:27:00', 15, 6, 'art', 'inactif'),
(46, 'Culture général 1_2', '2025-06-23 02:36:00', 15, 5, 'culture générale', 'inactif'),
(47, 'Culture général 1_3', '2025-06-23 02:49:00', 20, 2, 'culture générale', 'inactif'),
(48, 'Musique_1_2', '2025-06-23 12:35:00', 10, 6, 'musique', 'inactif'),
(50, 'Art Interrogation 2-0_1', '2025-07-01 12:30:00', 20, 6, 'art', 'inactif'),
(51, 'Art Interrogation 2-0_2', '2025-07-01 12:43:00', 20, 5, 'art', 'inactif'),
(52, 'Art Interrogation 2-0_3', '2025-07-01 14:37:00', 20, 5, 'art', 'inactif'),
(53, 'A_1_0', '2025-07-02 23:40:00', 15, 5, 'art', 'inactif'),
(55, 'A_0', '2025-07-03 10:36:00', 15, 5, 'art', 'inactif'),
(56, 'A_1', '2025-07-06 10:10:00', 15, 5, 'art', 'prévu'),
(58, 'Musique_0', '2025-07-04 23:40:00', 15, 5, 'musique', 'prévu'),
(59, 'Musique_1_2_3', '2025-07-03 11:28:00', 15, 5, 'musique', 'inactif'),
(60, 'JedA', '2025-07-03 11:46:00', 6, 2, 'art', 'inactif'),
(61, 'Mue_1', '2025-07-03 12:00:00', 10, 3, 'musique', 'actif');

-- --------------------------------------------------------

--
-- Structure de la table `quiz_questions`
--

CREATE TABLE `quiz_questions` (
  `id` int(11) NOT NULL,
  `quiz_id` int(11) DEFAULT NULL,
  `question_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `reponses`
--

CREATE TABLE `reponses` (
  `id` int(11) NOT NULL,
  `utilisateur_id` int(11) DEFAULT NULL,
  `quiz_id` int(11) DEFAULT NULL,
  `question_id` int(11) DEFAULT NULL,
  `reponse_donnee` varchar(255) DEFAULT NULL,
  `est_correct` tinyint(1) DEFAULT NULL,
  `date_reponse` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `reponses`
--

INSERT INTO `reponses` (`id`, `utilisateur_id`, `quiz_id`, `question_id`, `reponse_donnee`, `est_correct`, `date_reponse`) VALUES
(201, 17, 53, 330, 'B', 1, '2025-07-02 23:48:09'),
(202, 17, 53, 320, 'B', 0, '2025-07-02 23:48:09'),
(203, 17, 53, 326, 'C', 0, '2025-07-02 23:48:09'),
(204, 17, 53, 328, 'C', 1, '2025-07-02 23:48:09'),
(205, 17, 53, 322, 'C', 1, '2025-07-02 23:48:09'),
(206, 17, 53, 327, 'B', 0, '2025-07-02 23:48:09'),
(207, 17, 53, 329, 'C', 0, '2025-07-02 23:48:09'),
(208, 17, 53, 325, 'C', 0, '2025-07-02 23:48:09'),
(209, 17, 53, 323, 'C', 1, '2025-07-02 23:48:10'),
(210, 17, 53, 324, 'C', 0, '2025-07-02 23:48:10'),
(211, 17, 55, 322, 'B', 0, '2025-07-03 10:38:22'),
(212, 17, 55, 328, 'B', 0, '2025-07-03 10:38:22'),
(213, 17, 55, 321, 'B', 0, '2025-07-03 10:38:22'),
(214, 17, 55, 324, 'B', 1, '2025-07-03 10:38:22'),
(215, 17, 55, 320, 'C', 1, '2025-07-03 10:38:22'),
(216, 17, 55, 325, 'B', 0, '2025-07-03 10:38:22'),
(217, 17, 55, 330, 'B', 1, '2025-07-03 10:38:22'),
(218, 17, 55, 326, 'B', 0, '2025-07-03 10:38:22'),
(219, 17, 55, 323, 'C', 1, '2025-07-03 10:38:22'),
(220, 17, 55, 327, '', 0, '2025-07-03 10:38:22'),
(221, 7, 61, 302, 'C', 0, '2025-07-03 12:02:14'),
(222, 7, 61, 309, 'C', 1, '2025-07-03 12:02:14'),
(223, 7, 61, 297, 'A', 0, '2025-07-03 12:02:14'),
(224, 7, 61, 300, 'C', 0, '2025-07-03 12:02:14'),
(225, 7, 61, 304, 'B', 0, '2025-07-03 12:02:15'),
(226, 7, 61, 299, 'B', 1, '2025-07-03 12:02:15'),
(227, 7, 61, 306, 'B', 1, '2025-07-03 12:02:15'),
(228, 7, 61, 307, 'C', 1, '2025-07-03 12:02:15'),
(229, 7, 61, 298, 'B', 1, '2025-07-03 12:02:15'),
(230, 7, 61, 295, 'B', 0, '2025-07-03 12:02:15');

-- --------------------------------------------------------

--
-- Structure de la table `resultats`
--

CREATE TABLE `resultats` (
  `id` int(11) NOT NULL,
  `utilisateur_id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `score` int(11) NOT NULL,
  `total_questions` int(11) NOT NULL,
  `date_passage` datetime DEFAULT current_timestamp(),
  `duree` int(11) DEFAULT NULL,
  `statut` tinyint(1) DEFAULT 0,
  `etat` enum('en_cours','termine','annule','triche') DEFAULT 'en_cours'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `resultats`
--

INSERT INTO `resultats` (`id`, `utilisateur_id`, `quiz_id`, `score`, `total_questions`, `date_passage`, `duree`, `statut`, `etat`) VALUES
(2, 17, 20, 0, 10, '2025-06-15 19:11:03', NULL, 1, 'en_cours'),
(3, 7, 21, 0, 10, '2025-06-15 19:41:58', NULL, 0, 'en_cours'),
(4, 7, 27, 5, 10, '2025-06-15 22:15:52', NULL, 1, 'en_cours'),
(5, 4, 28, 4, 10, '2025-06-15 22:55:52', NULL, 1, 'en_cours'),
(6, 17, 28, 4, 10, '2025-06-15 23:48:20', NULL, 1, 'en_cours'),
(7, 22, 29, 0, 0, '2025-06-16 11:04:45', NULL, 1, 'en_cours'),
(8, 22, 30, 4, 10, '2025-06-16 11:37:56', NULL, 1, 'en_cours'),
(9, 22, 31, 6, 10, '2025-06-16 12:01:02', NULL, 1, 'en_cours'),
(10, 17, 32, 0, 0, '2025-06-17 12:41:24', NULL, 0, 'en_cours'),
(11, 4, 34, 0, 0, '2025-06-21 17:57:32', NULL, 1, 'en_cours'),
(12, 17, 34, 2, 10, '2025-06-21 22:03:33', NULL, 0, 'en_cours'),
(13, 17, 35, 5, 10, '2025-06-21 22:25:18', NULL, 0, 'en_cours'),
(14, 4, 35, 5, 10, '2025-06-21 22:38:06', NULL, 0, 'en_cours'),
(15, 7, 36, 3, 10, '2025-06-21 23:56:34', NULL, 0, 'en_cours'),
(16, 23, 37, 0, 0, '2025-06-22 00:18:37', NULL, 1, 'en_cours'),
(17, 22, 37, 0, 0, '2025-06-22 00:30:13', NULL, 1, 'en_cours'),
(18, 23, 30, 9, 10, '2025-06-22 00:49:56', NULL, 0, 'en_cours'),
(19, 17, 38, 0, 0, '2025-06-22 01:39:34', NULL, 0, 'en_cours'),
(20, 4, 38, 0, 0, '2025-06-22 01:42:31', NULL, 0, 'en_cours'),
(21, 7, 39, 0, 0, '2025-06-22 01:50:15', NULL, 0, 'en_cours'),
(22, 23, 40, 0, 0, '2025-06-22 01:56:32', NULL, 0, 'en_cours'),
(23, 22, 40, 0, 0, '2025-06-22 02:26:07', NULL, 0, 'en_cours'),
(24, 22, 41, 0, 0, '2025-06-22 02:47:50', NULL, 0, 'en_cours'),
(25, 23, 41, 0, 0, '2025-06-22 02:54:35', NULL, 0, 'en_cours'),
(26, 24, 42, 5, 10, '2025-06-22 22:13:08', NULL, 0, 'en_cours'),
(27, 26, 42, 0, 0, '2025-06-22 22:51:27', NULL, 0, 'en_cours'),
(28, 4, 43, 2, 10, '2025-06-23 01:14:09', NULL, 0, 'en_cours'),
(29, 4, 43, 2, 10, '2025-06-23 01:14:50', NULL, 0, 'en_cours'),
(30, 4, 43, 2, 10, '2025-06-23 01:18:45', NULL, 0, 'en_cours'),
(31, 17, 43, 0, 0, '2025-06-23 01:34:11', NULL, 0, 'en_cours'),
(32, 17, 44, 0, 0, '2025-06-23 02:04:26', NULL, 0, 'en_cours'),
(33, 4, 44, 0, 0, '2025-06-23 02:23:50', NULL, 0, 'en_cours'),
(34, 4, 45, 0, 0, '2025-06-23 02:29:32', NULL, 0, 'en_cours'),
(35, 17, 45, 0, 0, '2025-06-23 02:30:48', NULL, 0, 'en_cours'),
(36, 26, 46, 6, 10, '2025-06-23 02:39:10', NULL, 1, 'en_cours'),
(37, 24, 46, 5, 10, '2025-06-23 02:47:38', NULL, 1, 'en_cours'),
(38, 24, 47, 5, 10, '2025-06-23 02:52:38', NULL, 0, 'en_cours'),
(39, 7, 48, 4, 10, '2025-06-23 12:40:34', NULL, 1, 'en_cours'),
(42, 17, 50, 0, 0, '2025-07-01 12:33:12', NULL, 0, 'en_cours'),
(43, 4, 50, 0, 0, '2025-07-01 12:34:52', NULL, 0, 'en_cours'),
(44, 17, 51, 3, 10, '2025-07-01 12:45:36', NULL, 0, 'en_cours'),
(45, 4, 51, 0, 0, '2025-07-01 12:47:46', NULL, 0, 'en_cours'),
(46, 4, 52, 2, 10, '2025-07-01 14:42:19', NULL, 0, 'en_cours'),
(47, 17, 52, 2, 10, '2025-07-01 14:45:26', NULL, 0, 'en_cours'),
(48, 17, 53, 4, 10, '2025-07-02 23:48:10', NULL, 0, 'en_cours'),
(49, 17, 55, 4, 10, '2025-07-03 10:38:22', NULL, 1, 'en_cours'),
(50, 4, 55, 0, 0, '2025-07-03 10:48:45', NULL, 0, 'en_cours'),
(51, 7, 61, 5, 10, '2025-07-03 12:02:15', NULL, 0, 'en_cours');

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

CREATE TABLE `utilisateurs` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `mot_de_passe` varchar(255) DEFAULT NULL,
  `role` enum('eleve','admin_simple','admin_principal') NOT NULL,
  `date_inscription` datetime DEFAULT current_timestamp(),
  `reset_token` varchar(255) DEFAULT NULL,
  `token_expiration` datetime DEFAULT NULL,
  `photo` varchar(255) DEFAULT 'default.jpg',
  `telephone` varchar(20) DEFAULT NULL,
  `postnom` varchar(255) DEFAULT NULL,
  `prenom` varchar(255) DEFAULT NULL,
  `adresse` varchar(255) DEFAULT NULL,
  `sexe` varchar(255) DEFAULT NULL,
  `naissance` varchar(255) DEFAULT NULL,
  `inscription_complete` tinyint(1) DEFAULT 0,
  `statut` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id`, `nom`, `email`, `mot_de_passe`, `role`, `date_inscription`, `reset_token`, `token_expiration`, `photo`, `telephone`, `postnom`, `prenom`, `adresse`, `sexe`, `naissance`, `inscription_complete`, `statut`) VALUES
(1, 'Mukadi', 'wwwmclegende@gmail.com', '$2y$10$XmRRthn9bKPV7Y2y8qTS1ueSwiCILG3oSCY0bSB9am3GUv0Fppq0i', 'admin_principal', '2025-06-03 15:27:01', NULL, NULL, 'uploads/avatars/68651c88eebab.jpg', '0977411278', 'Mukadi', 'Joseph', 'Limete, funa mutonkole 22', 'Homme', '2003-11-01', 1, 'Actif'),
(4, 'Ndaya', 'ruthndaya@gmail.com', '$2y$10$C0HGafWQPdz70X02NJqlauJ1Lq/YRdEzeziT5H3Q3.g1sRPq6rY1C', 'eleve', '2025-06-05 10:42:31', '8c6546b0df0b5203f90bb042de962fbc36d9eab4fbd8be19217354532fa453ef', '2025-06-23 04:30:18', 'uploads/avatars/68630be4d83ec.png', '243820137457', 'Mukadi', 'Ruth', 'Limete, funa mutonkole 22', 'Femme', '', 1, NULL),
(7, 'Lumana', 'lumanajordan@gmail.com', '$2y$10$QMplWtNbJO5I6CUGzvGAHuhj7.ILdZy442riTOKsAL43RoO5gNfDS', 'eleve', '2025-06-06 15:33:47', NULL, NULL, 'uploads/avatars/68443f0040dff.PNG', '0856324397', 'Lukosho', 'Jordan', 'rue Bukavu Q°yolo nord funa', 'Homme', '2002-03-24', 1, 'Actif'),
(15, 'karl', 'ritchiekwatangolokarl@gmail.com', '$2y$10$wHC6yEKN1DvF2rMEXRJy0uGnobqmdx9ShJtESn3gp2MdIyMxdybXm', 'eleve', '2025-06-07 14:52:27', NULL, NULL, 'default.jpg', '243820137485', 'manzambi', 'Richie', 'mososo , universite 18 A limete', 'Homme', NULL, 1, NULL),
(17, 'Tshibunga', 'jeremiemukadi@gmail.com', '$2y$10$.CGUXf6cWjyNlh/uVBb2CeKpFziREkjq7uhAqgOVmEGLDaobnLfAO', 'eleve', '2025-06-07 19:40:36', NULL, NULL, 'uploads/avatars/68664deb35287.png', '085632400', 'Mukadi', 'Jeremie', 'mososo , universite 18 A limete', 'Homme', '', 1, NULL),
(20, 'kazadi', 'binvenuekazadi@gmail.com', '$2y$10$p29qIy0xH5IHtFdpo0J6B.LtJWsxnh.ICV7CkQNqyAitSTgpYoUnW', 'admin_simple', '2025-06-09 17:06:32', NULL, NULL, 'uploads/avatars/6855fed302042.jpg', '243820137000', '', 'Bienvenue', '', NULL, '2025-06-07', 1, 'actif'),
(21, 'Tshala', 'tshalanaomie@gmail.com', '$2y$10$8WjwwrQpO2OYJkWgvqr6W.pNVk2LNZz5wLforOGSU6uLwQ91B3n26', 'admin_simple', '2025-06-10 09:11:46', NULL, NULL, 'uploads/avatars/68520b5189747.png', '0856789457', NULL, 'Naomie', NULL, NULL, NULL, 1, 'inactif'),
(22, 'Mbula', 'mbulajustin@gmail.com', '$2y$10$sl.uUdCuUkOSjdP0OAqwJehruODAcCu73MnoaTSeaZM7aGO9DcdgK', 'eleve', '2025-06-16 10:54:09', NULL, NULL, 'uploads/avatars/684feab63b9b9.png', '0987654874', 'Mayola', 'Justin', 'Ngaliema musoso 23', 'Homme', '', 1, NULL),
(23, 'Kena', 'kenarebecca@gmail.com', '$2y$10$wrOmTydD9Gaxae6A/Wgj1O3CyjClF1Y5LDuy6BRRTbSK7QCIQ3lky', 'eleve', '2025-06-20 08:51:02', NULL, NULL, 'uploads/avatars/68569efdb90a6.png', '243820137267', 'Mukadi', 'Rebecca', 'Limete,funa mutonkole 22', 'F', '2025-05-29', 1, 'Actif'),
(24, 'Django', 'djangomuteba@gmail.com', '$2y$10$yX2P4.k5FNeeYz.Fbm7Bv.npLqtNYfT6nV8pn/DFN55tMWXRwjjia', 'eleve', '2025-06-22 18:22:30', NULL, NULL, 'uploads/avatars/68586e2395927.png', '0820137458', 'Muteba', 'Jean', 'Limete, funa mutonkole 22', 'M', '2025-06-05', 1, 'Actif'),
(26, 'Ngandu', 'irenengandu@gmail.com', '$2y$10$6GlJ56oH88TnINowX1cpkOG02dAqRmGC9X3dWgvxfYI/Mn65kPA2.', 'eleve', '2025-06-22 22:32:18', NULL, NULL, 'uploads/avatars/685877d7cc5fe.png', '0820100457', 'Mukadi', 'Irene', 'mososo , universite 18 A limete', 'Femme', '', 1, NULL),
(30, 'Bitcho', 'bitchokazadi@gmail.com', '$2y$10$As3T0f/c2vPB4Xj2AFEDI.vPveXF5Acj0Y1UjFgAwUezUxK7luSYK', 'eleve', '2025-07-02 13:59:46', NULL, NULL, 'uploads/avatars/68652ddcdc038.jpg', '0982573459', 'Kazadi', 'Miradi', 'Limete funa', 'Femme', '', 1, NULL),
(31, 'Tshite', 'tshitegabi@gmail.com', '$2y$10$qWSDonyF6Hcc5vIBOaYkv.rrPQ8TIZtzN1alNn4J81YmCmc2OIOiK', 'eleve', '2025-07-03 11:10:23', NULL, NULL, 'uploads/avatars/68665789be33b.png', '0825697538', 'Mukadi', 'Gabi', 'Limete funa 22', 'Homme', '', 1, NULL);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `activites_admin`
--
ALTER TABLE `activites_admin`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Index pour la table `eleves`
--
ALTER TABLE `eleves`
  ADD PRIMARY KEY (`id`),
  ADD KEY `utilisateur_id` (`utilisateur_id`);

--
-- Index pour la table `interrogation_questions`
--
ALTER TABLE `interrogation_questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `utilisateur_id` (`utilisateur_id`),
  ADD KEY `quiz_id` (`quiz_id`),
  ADD KEY `question_id` (`question_id`);

--
-- Index pour la table `interrogation_utilisateur`
--
ALTER TABLE `interrogation_utilisateur`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_utilisateur_quiz` (`utilisateur_id`,`quiz_id`),
  ADD KEY `fk_quiz` (`quiz_id`);

--
-- Index pour la table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quiz_id` (`quiz_id`),
  ADD KEY `idx_notif_utilisateur` (`utilisateur_id`),
  ADD KEY `idx_notif_lue` (`lue`),
  ADD KEY `idx_notif_date` (`date_creation`),
  ADD KEY `idx_notif_type` (`type`);

--
-- Index pour la table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `quiz`
--
ALTER TABLE `quiz`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `quiz_questions`
--
ALTER TABLE `quiz_questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quiz_id` (`quiz_id`),
  ADD KEY `question_id` (`question_id`);

--
-- Index pour la table `reponses`
--
ALTER TABLE `reponses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `utilisateur_id` (`utilisateur_id`),
  ADD KEY `quiz_id` (`quiz_id`),
  ADD KEY `question_id` (`question_id`);

--
-- Index pour la table `resultats`
--
ALTER TABLE `resultats`
  ADD PRIMARY KEY (`id`),
  ADD KEY `utilisateur_id` (`utilisateur_id`),
  ADD KEY `quiz_id` (`quiz_id`);

--
-- Index pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `activites_admin`
--
ALTER TABLE `activites_admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=177;

--
-- AUTO_INCREMENT pour la table `eleves`
--
ALTER TABLE `eleves`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT pour la table `interrogation_questions`
--
ALTER TABLE `interrogation_questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;

--
-- AUTO_INCREMENT pour la table `interrogation_utilisateur`
--
ALTER TABLE `interrogation_utilisateur`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT pour la table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=237;

--
-- AUTO_INCREMENT pour la table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=341;

--
-- AUTO_INCREMENT pour la table `quiz`
--
ALTER TABLE `quiz`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT pour la table `quiz_questions`
--
ALTER TABLE `quiz_questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=191;

--
-- AUTO_INCREMENT pour la table `reponses`
--
ALTER TABLE `reponses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=231;

--
-- AUTO_INCREMENT pour la table `resultats`
--
ALTER TABLE `resultats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `activites_admin`
--
ALTER TABLE `activites_admin`
  ADD CONSTRAINT `activites_admin_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `eleves`
--
ALTER TABLE `eleves`
  ADD CONSTRAINT `eleves_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `interrogation_questions`
--
ALTER TABLE `interrogation_questions`
  ADD CONSTRAINT `interrogation_questions_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id`),
  ADD CONSTRAINT `interrogation_questions_ibfk_2` FOREIGN KEY (`quiz_id`) REFERENCES `quiz` (`id`),
  ADD CONSTRAINT `interrogation_questions_ibfk_3` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`);

--
-- Contraintes pour la table `interrogation_utilisateur`
--
ALTER TABLE `interrogation_utilisateur`
  ADD CONSTRAINT `fk_quiz` FOREIGN KEY (`quiz_id`) REFERENCES `quiz` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_utilisateur` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`quiz_id`) REFERENCES `quiz` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `quiz_questions`
--
ALTER TABLE `quiz_questions`
  ADD CONSTRAINT `quiz_questions_ibfk_1` FOREIGN KEY (`quiz_id`) REFERENCES `quiz` (`id`),
  ADD CONSTRAINT `quiz_questions_ibfk_2` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`);

--
-- Contraintes pour la table `reponses`
--
ALTER TABLE `reponses`
  ADD CONSTRAINT `reponses_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id`),
  ADD CONSTRAINT `reponses_ibfk_2` FOREIGN KEY (`quiz_id`) REFERENCES `quiz` (`id`),
  ADD CONSTRAINT `reponses_ibfk_3` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`);

--
-- Contraintes pour la table `resultats`
--
ALTER TABLE `resultats`
  ADD CONSTRAINT `resultats_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `resultats_ibfk_2` FOREIGN KEY (`quiz_id`) REFERENCES `quiz` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
