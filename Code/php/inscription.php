<?php

// Activation de l'affichage des erreurs pour faciliter le débogage
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Inclusion du fichier de configuration pour établir la connexion avec la base de données
require_once('../config.php');

// Démarre une session PHP pour pouvoir stocker des informations utilisateur
session_start();

// Initialisation des variables d'erreur et de succès
$error = null; 
$success = null;

// Vérifie si le formulaire a été soumis via la méthode POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération des valeurs envoyées par le formulaire, avec valeur par défaut null si non renseignées
    $username = $_POST['prenom'] ?? null;
    $email = $_POST['email'] ?? null;
    $password = $_POST['mdp'] ?? null;
    $confirm_password = $_POST['confirm_password'] ?? null;

    // Validation des champs du formulaire
    // Vérifie si tous les champs sont remplis
    if (!$username || !$email || !$password || !$confirm_password) {
        $error = "Veuillez remplir tous les champs.";
    } 
    // Vérifie si les mots de passe ne correspondent pas
    elseif ($password !== $confirm_password) {
        $error = "Les mots de passe ne correspondent pas.";
    } 
    // Vérifie si le mot de passe respecte les critères de sécurité
    elseif (!isValidPassword($password)) {
        $error = "Le mot de passe doit contenir au moins 12 caractères, avec des minuscules, majuscules, chiffres, et caractères spéciaux.";
    } 
    else {
        try {
            // Vérifie si l'email ou le nom d'utilisateur existe déjà dans la base de données
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM utilisateurs WHERE prenom = ? OR email = ?");
            $stmt->execute([$username, $email]);
            $count = $stmt->fetchColumn(); // Récupère le nombre d'occurrences trouvées

            // Si le nom d'utilisateur ou l'email existe déjà, affiche une erreur
            if ($count > 0) {
                $error = "Nom d'utilisateur ou email déjà utilisé.";
            } else {
                // Hachage du mot de passe pour le stocker de manière sécurisée
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);

                // Insertion des données dans la base de données
                $stmt = $pdo->prepare("INSERT INTO utilisateurs (prenom, email, mdp) VALUES (?, ?, ?)");
                if ($stmt->execute([$username, $email, $hashed_password])) {
                    // Récupère l'ID de l'utilisateur fraîchement inséré
                    $user_id = $pdo->lastInsertId();

                    // Initialisation des variables de session pour l'utilisateur
                    $_SESSION['id'] = $user_id;
                    $_SESSION['prenom'] = $username;
                    $_SESSION['email'] = $email;
                    $_SESSION['role'] = 'utilisateur'; // Attribue un rôle par défaut à l'utilisateur

                    // Redirection vers la page du compte après une inscription réussie
                    header("Location: ../php/compte.php");
                    exit();
                } else {
                    // Affiche un message d'erreur si l'insertion échoue
                    $error = "Erreur lors de l'inscription.";
                }
            }
        } catch (PDOException $e) {
            // En cas d'erreur avec la base de données, affiche l'erreur
            $error = "Erreur de connexion à la base de données : " . $e->getMessage();
        }
    }
}

// Fonction de validation du mot de passe (12 caractères minimum, avec des majuscules, minuscules, chiffres et caractères spéciaux)
function isValidPassword($password) {
    return (strlen($password) >= 12 && 
            preg_match('/[a-z]/', $password) && // Doit contenir au moins une lettre minuscule
            preg_match('/[A-Z]/', $password) && // Doit contenir au moins une lettre majuscule
            preg_match('/[0-9]/', $password) && // Doit contenir au moins un chiffre
            preg_match('/[\W_]/', $password)); // Doit contenir un caractère spécial
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription</title>
    <!-- Inclusion de la feuille de style CSS -->
    <link rel="stylesheet" href="../css/inscription.css?v=1.0">
</head>
<body>
    <div class="container">
        <h1>Inscription</h1>

        <!-- Formulaire d'inscription -->
        <form action="inscription.php" method="post">
            <label for="email">Email :</label>
            <input type="email" id="email" name="email" required>

            <label for="prenom">Nom d'utilisateur :</label>
            <input type="text" id="prenom" name="prenom" required>

            <label for="mdp">Mot de passe :</label>
            <input type="password" id="mdp" name="mdp" required>

            <label for="confirm_password">Confirmer le mot de passe :</label>
            <input type="password" id="confirm_password" name="confirm_password" required>

            <button type="submit">S'inscrire</button>
        </form>

        <!-- Affichage des messages d'erreur ou de succès -->
        <?php if ($success): ?>
            <p class="success"><?php echo htmlspecialchars($success); ?></p>
        <?php elseif ($error): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <!-- Lien vers la page de connexion -->
        <p>Déjà inscrit ? <a href="../php/connexion.php">Se connecter</a></p>
    </div>
</body>
</html>
