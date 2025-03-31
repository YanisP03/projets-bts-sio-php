<?php
// Démarrage de la session pour gérer la session de l'utilisateur
session_start();

// Vérifie si l'utilisateur est connecté en vérifiant l'existence d'une variable de session
if (!isset($_SESSION['id'])) {
    // Si l'utilisateur n'est pas connecté, il est redirigé vers la page de connexion
    header('Location: ./connexion.php');
    exit(); // Arrête l'exécution du script après la redirection
}

// Récupération des informations de l'utilisateur depuis la session
$username = isset($_SESSION['prenom']) ? htmlspecialchars($_SESSION['prenom']) : "Utilisateur inconnu"; 
// Utilisation de htmlspecialchars pour éviter les failles XSS
$role = isset($_SESSION['role']) ? htmlspecialchars($_SESSION['role']) : "Rôle non défini"; 
// Si le rôle n'est pas défini, une valeur par défaut est donnée
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"> <!-- Encodage du texte en UTF-8 pour supporter les caractères spéciaux -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Adaptation du site sur mobile -->
    <link rel="stylesheet" href="../Css/styleCompte.css"> <!-- Lien vers le fichier CSS externe -->
    <link rel="icon" href="../Images/iconVV.png"> <!-- Icône de la page -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet"> <!-- Police Montserrat -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"> <!-- Icônes Font Awesome -->
    <title>Mon Compte</title> <!-- Titre de la page -->
</head>
<body>
    <nav class="navbar"> <!-- Navigation du site -->
        <a href="../Accueil.php">
            <img class="Logo" src="../Images/iconVVGrey.png" alt="Logo de test" />
        </a>
        <h1>Compte</h1> <!-- Titre de la page de compte -->
        <ul class="menu">
            <li><a href="../html/index2.html">Accueil</a></li>
            <li><a href="Magasin.php">Magasin</a></li>
            <li><a href="Compte.php">Mon compte</a></li>
            <li><a href="modifier_mot_de_passe.php">Changer le mot de passe</a></li>
        </ul>
    </nav>
    <section class="content"> <!-- Section principale de la page -->
        <h1 id="nouveaute">Mon Compte</h1> <!-- Titre principal -->
        <p>Bienvenue, <strong><?= $username ?></strong> !</p> <!-- Affichage du nom de l'utilisateur -->
        <p>Votre rôle : <strong><?= $role ?></strong></p> <!-- Affichage du rôle de l'utilisateur -->
        
        <?php if ($role === 'admin'): ?>
            <!-- Si l'utilisateur a le rôle 'admin', afficher un bouton pour accéder au mode administrateur -->
            <a href="admin.php" class="admin-button">Mode Administrateur</a>
        <?php endif; ?>
        
        <!-- Lien pour déconnecter l'utilisateur -->
        <a href="../html/index2.html" class="logout-button">Se déconnecter</a>
    </section>
</body>
</html>
