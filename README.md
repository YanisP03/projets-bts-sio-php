# AlloPro — Plateforme e-commerce BTP

> Projet réalisé dans le cadre du BTS SIO par Adam Bouali & Yanis Perrin.

**Stack :** PHP 8.0 · MySQL 8.0 · HTML5 · CSS3 · JavaScript

---

## Présentation

AlloPro est une plateforme e-commerce destinée au secteur du Bâtiment et Travaux Publics. Elle permet à des professionnels comme à des particuliers de commander des matériaux de construction, des équipements de sécurité et des outillages directement en ligne.

Le projet couvre l'intégralité du cycle d'une boutique : catalogue produits, gestion du panier, suivi des commandes et interface d'administration.

---

## Fonctionnalités

### Espace client
- Inscription et connexion sécurisées (hashage bcrypt)
- Navigation dans le catalogue par catégories (Ciment, Béton, Sécurité, Outils)
- Panier dynamique avec gestion des quantités
- Validation et suivi des commandes
- Historique des achats et gestion du profil
- Réinitialisation du mot de passe par token

### Interface d'administration
- Gestion des utilisateurs : modification des rôles, blocage/déblocage de comptes
- Gestion du catalogue : création, modification et suppression de produits (CRUD complet)
- Suivi des commandes clients
- Tableau de bord avec statistiques générales

### Sécurité
- Requêtes préparées (PDO) contre les injections SQL
- Hashage bcrypt avec salt pour les mots de passe
- Gestion des sessions PHP
- Validation des données côté serveur
- Protection CSRF

---

## Technologies

| Couche | Outils |
|--------|--------|
| Backend | PHP 8.0+, MySQL 8.0, PDO |
| Frontend | HTML5, CSS3, JavaScript |
| Dev local | XAMPP / WAMP, HeidiSQL |
| Versioning | Git, GitHub |

---

## Installation

### Prérequis
- PHP 8.0+
- MySQL 8.0+
- Serveur Apache (XAMPP, WAMP ou MAMP)

### Étapes

**1. Cloner le dépôt**
```bash
git clone https://github.com/adam110905/Projet-PHP-Bts-Sio.git
cd Projet-PHP-Bts-Sio
```

**2. Créer et importer la base de données**
```bash
mysql -u root -p -e "CREATE DATABASE basesitebtp;"
mysql -u root -p basesitebtp < "projet php v2/BDD/base de donnée pour le site allopro.sql"
```

**3. Configurer la connexion**

Ouvrez le fichier de configuration et adaptez les paramètres :
```php
$host     = 'localhost';
$dbname   = 'basesitebtp';
$username = 'root';
$password = ''; // votre mot de passe MySQL
```

**4. Lancer l'application**

Placez le projet dans `htdocs` (XAMPP) ou `www` (WAMP), démarrez Apache et MySQL, puis accédez à :
```
http://localhost/Projet-PHP-Bts-Sio/projet php v2/Code/php/offline/index.html
```

---

## Comptes de test

| Rôle | Email | Mot de passe |
|------|-------|--------------|
| Administrateur | admin@gmail.com | G7u$kP-m2NYvb! |
| Client | — | Créez un compte via l'inscription |

> ⚠️ Ces identifiants sont réservés aux tests. Ne les utilisez jamais en production.

---

## Structure du projet

```
Projet-PHP-Bts-Sio/
├── projet php v2/
│   ├── BDD/                        # Fichier SQL d'initialisation
│   └── Code/
│       ├── php/
│       │   ├── online/             # Pages dynamiques PHP
│       │   │   ├── admin/          # Interface administrateur
│       │   │   ├── connexion.php
│       │   │   ├── inscription.php
│       │   │   ├── produits.php
│       │   │   ├── panier.php
│       │   │   └── moncompte.php
│       │   └── offline/
│       │       └── index.html      # Page d'accueil statique
│       ├── css/
│       ├── js/
│       └── images/
└── README.md
```

---

## Base de données

Le schéma repose sur 6 tables :

| Table | Rôle |
|-------|------|
| `users` | Clients et administrateurs |
| `products` | Catalogue produits |
| `cart` | Panier temporaire |
| `orders` | Commandes validées |
| `order_items` | Détails des lignes de commande |
| `password_resets` | Tokens de réinitialisation |

---

## Architecture

Le projet suit une architecture **MVC adaptée** :
- **Modèle** — accès aux données via PDO
- **Vue** — templates HTML/CSS avec injection PHP
- **Contrôleur** — scripts PHP gérant la logique métier

---

## Pistes d'amélioration

- Intégration d'un module de paiement (Stripe, PayPal)
- Envoi d'emails transactionnels (confirmation de commande)
- Système d'avis et de notation produits
- Exposition d'une API REST pour application mobile
- Gestion des stocks en temps réel
- Support multi-devises et multilingue

---

## Équipe

| Développeur | Rôle | GitHub |
|-------------|------|--------|
| Yanis Perrin | Full Stack | [@YanisP03](https://github.com/YanisP03) |
| Adam Bouali | Full Stack | [@adam110905](https://github.com/adam110905) |

Travaux réalisés en commun : développement de la partie statique (HTML/CSS), conception du schéma de base de données, système d'authentification et structure de l'espace client.

---

## Contact

LinkedIn : [Yanis Perrin](https://www.linkedin.com/in/yanis-perrin-a63316357/)

---

*Projet pédagogique — BTS SIO · © 2025 AlloPro*
