<?php
// Activation de l'affichage des erreurs pour le débogage
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Démarre une session PHP pour stocker des informations sur l'utilisateur
session_start();

// Inclusion du fichier de configuration pour la connexion à la base de données
require_once '../config.php'; // Vérifiez le chemin si nécessaire

// Initialisation de la variable d'erreur à null
$error = null; 

// Vérifie si la requête est une requête POST (envoi du formulaire)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérifie si l'email et le mot de passe sont présents dans la requête
    if (isset($_POST['email']) && isset($_POST['mdp'])) {
        // Récupération des valeurs envoyées par le formulaire
        $email = $_POST['email'];
        $password = $_POST['mdp'];

        try {
            // Préparation d'une requête SQL pour rechercher l'utilisateur par email
            $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
            $stmt->execute([$email]); // Exécution de la requête avec l'email passé en paramètre
            $user = $stmt->fetch(); // Récupération des résultats

            // Si l'utilisateur existe et le mot de passe est correct
            if ($user && password_verify($password, $user['mdp'])) {
                // Vérifie si l'utilisateur est suspendu
                if ($user['suspended'] == 1) {
                    $error = "Votre compte a été suspendu. Contactez l'administrateur.";
                } else {
                    // Sauvegarde des informations de l'utilisateur dans la session
                    $_SESSION['id'] = $user['id'];
                    $_SESSION['prenom'] = $user['prenom'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['role'] = $user['role'];

                    // Redirection vers la page du compte utilisateur
                    header('Location: ./compte.php');
                    exit();
                }
            } else {
                // Si les informations sont incorrectes
                $error = "Email ou mot de passe incorrect.";
            }
        } catch (PDOException $e) {
            // Si une erreur se produit avec la base de données
            $error = "Erreur de connexion à la base de données : " . $e->getMessage();
        }
    } else {
        // Si les champs ne sont pas remplis
        $error = "Veuillez remplir tous les champs.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
    <!-- Inclusion de la feuille de style pour la page de connexion -->
    <link rel="stylesheet" href="../css/connexion.css?v=1.0">
</head>
<body>
    <div class="container">
        <h1>Connexion</h1>
        
        <!-- Formulaire de connexion -->
        <form action="./connexion.php" method="post">
            <label for="email">Email :</label>
            <input type="email" id="email" name="email" required>
            
            <label for="mdp">Mot de passe :</label>
            <input type="password" id="mdp" name="mdp" required>
            
            <button type="submit">Se connecter</button>
        </form>

        <!-- Lien vers la page d'inscription -->
        <p>Pas encore inscrit ? <a href="./inscription.php">Créer un compte</a></p>

        <!-- Affichage des messages d'erreur si une erreur est présente -->
        <?php if ($error): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
    </div>
</body>
</html>
