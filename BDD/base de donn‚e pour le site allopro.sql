-- Création de la base de données 'basesitebtp' si elle n'existe pas déjà

-- Utilise le jeu de caractères utf8mb4 et une collation compatible

CREATE DATABASE IF NOT EXISTS `basesitebtp` 

  DEFAULT CHARACTER SET utf8mb4 

  COLLATE utf8mb4_general_ci;


-- Sélection de la base de données pour les opérations suivantes

USE `basesitebtp`;


-- 1. CRÉATION DES TABLES SANS DÉPENDANCES D'ABORD


-- Création de la table 'products' pour stocker les produits disponibles à la vente

CREATE TABLE IF NOT EXISTS `products` (

  `id` int NOT NULL AUTO_INCREMENT,           -- Identifiant unique du produit (clé primaire)

  `name` varchar(100) NOT NULL,               -- Nom du produit

  `description` text,                         -- Description détaillée du produit

  `price` decimal(10,2) NOT NULL,             -- Prix du produit (2 décimales)

  `image` varchar(255) NOT NULL,              -- Chemin vers l'image du produit

  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP, -- Date de création du produit

  `category` varchar(255) NOT NULL,           -- Catégorie du produit

  PRIMARY KEY (`id`)                          -- Définition de la clé primaire

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- Création de la table 'users' pour stocker les utilisateurs du site

CREATE TABLE IF NOT EXISTS `users` (

  `id` int NOT NULL AUTO_INCREMENT,           -- Identifiant unique de l'utilisateur (clé primaire)

  `email` varchar(255) NOT NULL,              -- Email de l'utilisateur (unique)

  `pwd` varchar(255) NOT NULL,                -- Mot de passe hashé

  `username` varchar(100) DEFAULT NULL,       -- Nom d'utilisateur

  `role` enum('client','professionnel','admin') NOT NULL, -- Rôle de l'utilisateur

  `date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,       -- Date de création du compte

  `modification` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, -- Date de dernière modification

  `status` enum('active','blocked') DEFAULT 'active', -- Statut du compte

  `login_attempts` int DEFAULT '0',           -- Nombre de tentatives de connexion échouées

  `last_attempt_time` datetime DEFAULT NULL,  -- Heure de la dernière tentative de connexion

  PRIMARY KEY (`id`),                         -- Définition de la clé primaire

  UNIQUE KEY `email` (`email`)                -- L'email doit être unique

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- 2. CRÉATION DES TABLES AVEC DÉPENDANCES ENSUITE


-- Création de la table 'cart' pour stocker les articles du panier des utilisateurs

CREATE TABLE IF NOT EXISTS `cart` (

  `id` int NOT NULL AUTO_INCREMENT,           -- Identifiant unique du panier (clé primaire)

  `user_id` int NOT NULL,                     -- Référence à l'utilisateur propriétaire du panier

  `product_id` int NOT NULL,                  -- Référence au produit ajouté au panier

  `quantity` int DEFAULT '1',                 -- Quantité du produit (par défaut: 1)

  `added_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP, -- Date d'ajout au panier

  PRIMARY KEY (`id`),                         -- Définition de la clé primaire

  KEY `user_id` (`user_id`),                  -- Index sur user_id pour optimiser les recherches

  KEY `product_id` (`product_id`),            -- Index sur product_id pour optimiser les recherches

  CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE, -- Supprime les entrées si l'utilisateur est supprimé

  CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE -- Supprime les entrées si le produit est supprimé

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- Création de la table 'orders' pour stocker les commandes des utilisateurs

CREATE TABLE IF NOT EXISTS `orders` (

  `id` int NOT NULL AUTO_INCREMENT,           -- Identifiant unique de la commande (clé primaire)

  `user_id` int NOT NULL,                     -- Référence à l'utilisateur qui a passé la commande

  `total_amount` decimal(10,2) NOT NULL,      -- Montant total de la commande (2 décimales)

  `order_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP, -- Date de la commande

  `status` enum('en attente','confirmée','expédiée','livrée') DEFAULT 'confirmée', -- Statut de la commande

  PRIMARY KEY (`id`),                         -- Définition de la clé primaire

  KEY `user_id` (`user_id`),                  -- Index sur user_id pour optimiser les recherches

  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE -- Supprime les commandes si l'utilisateur est supprimé

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- Création de la table 'order_items' pour stocker les articles de chaque commande

CREATE TABLE IF NOT EXISTS `order_items` (

  `id` int NOT NULL AUTO_INCREMENT,           -- Identifiant unique de l'article commandé (clé primaire)

  `order_id` int NOT NULL,                    -- Référence à la commande

  `product_id` int NOT NULL,                  -- Référence au produit commandé

  `quantity` int NOT NULL,                    -- Quantité commandée

  `price` decimal(10,2) NOT NULL,             -- Prix unitaire au moment de la commande

  PRIMARY KEY (`id`),                         -- Définition de la clé primaire

  KEY `order_id` (`order_id`),                -- Index sur order_id pour optimiser les recherches

  KEY `product_id` (`product_id`),            -- Index sur product_id pour optimiser les recherches

  CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE, -- Supprime les articles si la commande est supprimée

  CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) -- Garde les articles même si le produit est supprimé

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- 3. INSERTION DES DONNÉES DANS L'ORDRE APPROPRIÉ


-- Insertion des données dans la table 'products'

INSERT INTO `products` (`id`, `name`, `description`, `price`, `image`, `created_at`, `category`) VALUES

  (1, 'Ciment Ultra Résistant', 'Ciment pour fondations, résiste à l''eau.', 15.99, 'sac-de-ciment.jpg', '2025-05-03 13:40:23', 'Ciment'),

  (2, 'Béton Armé Prémix', 'Béton prêt à l''emploi avec armatures intégrées.', 49.90, 'BetonPremix.jpg', '2025-05-03 13:40:23', 'Béton'),

  (3, 'Casque de chantier jaune', 'Casque de sécurité aux normes CE.', 12.50, 'CasqueChantier.jpg', '2025-05-03 13:40:23', 'Sécurité'),

  (4, 'Perceuse Pro 2200W', 'Perceuse pour murs porteurs, garantie 5 ans.', 129.99, 'PerceusePro.jpg', '2025-05-03 13:40:23', 'Outils');


-- Insertion des données dans la table 'users' 

INSERT INTO `users` (`id`, `email`, `pwd`, `username`, `role`, `date`, `modification`, `status`, `login_attempts`, `last_attempt_time`) VALUES

  (2, 'admin@gmail.com', '$2y$10$0lhYizUvN4v9fkZDS/RMOubkik2WmJl05wRf1nQmbJ0CwQO5FiAta', 'Admin', 'admin', '2025-05-03 13:40:33', '2025-05-03 13:40:33', 'active', 0, NULL);


-- Insertion des données dans la table 'cart' 

INSERT INTO `cart` (`id`, `user_id`, `product_id`, `quantity`, `added_at`) VALUES

  (4, 2, 3, 10, '2025-05-05 09:03:15'),

  (5, 2, 1, 10, '2025-05-05 09:03:55'),

  (6, 2, 2, 10, '2025-05-05 09:04:05'),

  (7, 2, 4, 10, '2025-05-05 09:04:09');

