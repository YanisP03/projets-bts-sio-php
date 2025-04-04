<?php
// Active l'affichage des erreurs pour faciliter le débogage
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Inclusion du fichier de configuration pour établir la connexion avec la base de données
require_once('../config.php');

// Démarre une session PHP pour gérer les informations de l'utilisateur
session_start();

// Si l'utilisateur n'est pas connecté, redirige vers la page de connexion
if (!isset($_SESSION['id'])) {
    header("Location: ./connexion.php");
    exit();
}

// Récupère le nom d'utilisateur et le rôle de l'utilisateur depuis la session
$username = isset($_SESSION['prenom']) ? htmlspecialchars($_SESSION['prenom']) : "Utilisateur inconnu";
$role = isset($_SESSION['role']) ? htmlspecialchars($_SESSION['role']) : "Rôle non défini";

// Initialisation des variables pour gérer les messages d'erreur ou de succès
$error = null;
$success = null;

// Vérifie si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupère les valeurs des champs du formulaire
    $current_password = $_POST['current_password'] ?? null;
    $new_password = $_POST['new_password'] ?? null;
    $confirm_password = $_POST['confirm_password'] ?? null;

    // Valide les champs : vérifie qu'ils ne sont pas vides
    if (!$current_password || !$new_password || !$confirm_password) {
        $error = "Veuillez remplir tous les champs.";
    } 
    // Vérifie que les mots de passe correspondent
    elseif ($new_password !== $confirm_password) {
        $error = "Les nouveaux mots de passe ne correspondent pas.";
    } 
    // Vérifie que le nouveau mot de passe respecte les critères de sécurité
    elseif (!isValidPassword($new_password)) {
        $error = "Le mot de passe doit contenir au moins 12 caractères, avec des minuscules, majuscules, chiffres et caractères spéciaux.";
    } 
    else {
        try {
            // Récupère le mot de passe actuel de l'utilisateur depuis la base de données
            $stmt = $pdo->prepare("SELECT password FROM utilisateurs WHERE id = ?");
            $stmt->execute([$_SESSION['id']]);
            $user = $stmt->fetch();

            // Vérifie que le mot de passe actuel est correct
            if ($user && password_verify($current_password, $user['mdp'])) {
                // Vérifie que le nouveau mot de passe n'est pas identique à l'ancien
                if ($new_password === $current_password) {
                    $error = "Le nouveau mot de passe ne peut pas être identique à l'ancien.";
                } else {
                    // Hachage du nouveau mot de passe pour le stocker de manière sécurisée
                    $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

                    // Met à jour le mot de passe dans la base de données
                    $stmt = $pdo->prepare("UPDATE utilisateurs SET password = ? WHERE id = ?");
                    if ($stmt->execute([$hashed_password, $_SESSION['id']])) {
                        // Si la mise à jour est réussie, affiche un message de succès
                        $success = "Mot de passe modifié avec succès.";
                    } else {
                        // Affiche une erreur si la mise à jour échoue
                        $error = "Erreur lors de la mise à jour du mot de passe.";
                    }
                }
            } else {
                // Si le mot de passe actuel est incorrect, affiche une erreur
                $error = "Le mot de passe actuel est incorrect.";
            }
        } catch (PDOException $e) {
            // En cas d'erreur avec la base de données, affiche l'erreur
            $error = "Erreur de connexion à la base de données : " . $e->getMessage();
        }
    }
}

// Fonction pour valider le mot de passe : 12 caractères minimum, avec des lettres, chiffres et caractères spéciaux
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Changer le mot de passe - Allopro</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <link rel="icon" href="../Images/iconVV.png">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="../css/navbar.css">
    <!-- Vous pouvez conserver votre CSS spécifique également -->
    <link rel="stylesheet" href="../Css/modifier_mot_de_passe.css?v=1.0">
</head>
<body class="bg-gray-900">
    <!-- Navbar style Allopro -->
    <nav class="gradient-nav fixed w-full z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20">
                <div class="flex-shrink-0 flex items-center">
                    <a href="../html/index2.html"><span class="text-3xl font-bold text-yellow-500">ALLOPRO</span></a>
                </div>
    
                <div class="hidden md:flex items-center space-x-8">
                    <a href="../html/index2.html" class="nav-link text-gray-100 hover:text-yellow-500 px-3 py-6 font-medium transition-colors">Accueil</a>
                    <a href="../html/Portfolio.html" class="nav-link text-gray-100 hover:text-yellow-500 px-3 py-6 font-medium transition-colors">Portfolio</a>
                    <a href="../html/Realisation.html" class="nav-link text-gray-100 hover:text-yellow-500 px-3 py-6 font-medium transition-colors">Réalisations</a>
                    <a href="../html/Contact.html" class="nav-link text-gray-100 hover:text-yellow-500 px-3 py-6 font-medium transition-colors">Contact</a>
                </div>
    
                <div class="hidden md:flex items-center">
                    <a href="Compte.php" 
                       class="bg-yellow-500 text-white px-6 py-2 rounded-lg font-medium hover:bg-yellow-600 transition-all duration-300">
                        Mon Compte
                    </a>
                </div>
    
                <div class="md:hidden flex items-center">
                    <button id="mobile-menu-button" class="text-gray-100 hover:text-yellow-500 focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path class="menu-icon" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            <path class="close-icon hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    
        <!-- Menu Mobile -->
        <div id="mobile-menu" class="mobile-menu hidden md:hidden absolute w-full menu-backdrop">
            <div class="px-4 py-4 space-y-3">
                <a href="../html/index2.html" class="block text-gray-100 hover:text-yellow-500 font-medium px-4 py-2 rounded-lg hover:bg-white/10 transition-colors">Accueil</a>
                <a href="../html/Portfolio.html" class="block text-gray-100 hover:text-yellow-500 font-medium px-4 py-2 rounded-lg hover:bg-white/10 transition-colors">Portfolio</a>
                <a href="../html/Realisation.html" class="block text-gray-100 hover:text-yellow-500 font-medium px-4 py-2 rounded-lg hover:bg-white/10 transition-colors">Réalisations</a>
                <a href="../html/Contact.html" class="block text-gray-100 hover:text-yellow-500 font-medium px-4 py-2 rounded-lg hover:bg-white/10 transition-colors">Contact</a>
                <a href="Compte.php" class="block text-gray-100 hover:text-yellow-500 font-medium px-4 py-2 rounded-lg hover:bg-white/10 transition-colors">Mon Compte</a>
            </div>
        </div>
    </nav>

    <!-- Conteneur principal du formulaire -->
    <div class="pt-32 pb-20">
        <div class="max-w-lg mx-auto px-4">
            <div class="bg-gray-800 rounded-lg shadow-xl p-8">
                <div class="text-center mb-8">
                    <h1 class="text-2xl font-bold text-white mb-4">Modifier votre mot de passe</h1>
                    <div class="w-16 h-1 bg-yellow-500 mx-auto opacity-50"></div>
                </div>

                <!-- Affichage des messages d'erreur ou de succès -->
                <?php if ($success): ?>
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded">
                        <p><?= htmlspecialchars($success); ?></p>
                    </div>
                <?php elseif ($error): ?>
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
                        <p><?= htmlspecialchars($error); ?></p>
                    </div>
                <?php endif; ?>

                <!-- Formulaire pour modifier le mot de passe -->
                <form method="post" class="space-y-6">
                    <div>
                        <label for="current_password" class="block text-gray-300 mb-2">Mot de passe actuel</label>
                        <input type="password" id="current_password" name="current_password" 
                               class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-yellow-500 transition-colors" 
                               required>
                    </div>

                    <div>
                        <label for="new_password" class="block text-gray-300 mb-2">Nouveau mot de passe</label>
                        <input type="password" id="new_password" name="new_password" 
                               class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-yellow-500 transition-colors" 
                               required>
                        <p class="text-gray-400 text-sm mt-2">Le mot de passe doit contenir au moins 12 caractères, des lettres minuscules, majuscules, des chiffres et des caractères spéciaux.</p>
                    </div>

                    <div>
                        <label for="confirm_password" class="block text-gray-300 mb-2">Confirmer le nouveau mot de passe</label>
                        <input type="password" id="confirm_password" name="confirm_password" 
                               class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-yellow-500 transition-colors" 
                               required>
                    </div>

                    <div class="pt-4">
                        <button type="submit" 
                                class="w-full bg-yellow-500 hover:bg-yellow-600 text-white font-medium py-3 px-4 rounded-lg transition-colors">
                            Valider le changement
                        </button>
                    </div>
                </form>

                <div class="mt-6 text-center">
                    <a href="Compte.php" class="text-gray-400 hover:text-yellow-500 transition-colors">Retour à mon compte</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer py-12 text-white bg-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12">
                <div>
                    <h3 class="text-2xl font-bold mb-6">ALLOPRO</h3>
                    <p class="text-gray-400">Votre partenaire de confiance pour tous vos projets de construction et rénovation.</p>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-6">Services</h4>
                    <ul class="space-y-4 text-gray-400">
                        <li><a href="#" class="hover:text-yellow-500 transition-colors">Construction</a></li>
                        <li><a href="#" class="hover:text-yellow-500 transition-colors">Rénovation</a></li>
                        <li><a href="#" class="hover:text-yellow-500 transition-colors">Expertise</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-6">Contact</h4>
                    <ul class="space-y-4 text-gray-400">
                        <li>BP 12578 Yaoundé Cameroune</li>
                        <li>75000 Paris, France</li>
                        <li>+00 213 694 862 751</li>
                        <li>+00 213 677 191 558</li>
                        <li>alloprocm@gmail.com</li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-6">Suivez-nous</h4>
                    <div class="flex space-x-4">
                        <a href="#" class="w-10 h-10 bg-gray-700 rounded-full flex items-center justify-center hover:bg-yellow-500 transition-colors">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-12 pt-8 text-center text-gray-400">
                <p>&copy; 2025 ALLOPRO. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <style>
    .gradient-nav {
        background: linear-gradient(to bottom, rgba(17, 24, 39, 0.95), rgba(17, 24, 39, 0.8));
        backdrop-filter: blur(10px);
    }
    </style>

    <!-- Scripts -->
    <script src="../js/navbar.js"></script>
</body>
</html>