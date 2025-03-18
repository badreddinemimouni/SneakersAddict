<?php
session_start();
require_once '../config/config.php';
require_once '../config/security.php';

// je verifie si un util est connecté déjà
 
if (isset($_SESSION['admin']) && $_SESSION['admin'] === true) {
    header('Location: ../admin/stock.php');
    exit();
}

$_SESSION['admin']=false;
$_SESSION['client']=false;
$_SESSION['personne']=true;

try {
    $pdo = new PDO('mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8', DB_USER, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Générer un jeton CSRF
$csrf_token = generateCSRFToken();

// Vérifier si un message d'erreur doit être affiché
$error_message = "";
if(isset($_GET['error'])) {
    switch($_GET['error']) {
        case 'password':
            $error_message = "Mot de passe incorrect. Veuillez réessayer.";
            break;
        case 'email':
            $error_message = "Adresse email non reconnue. Veuillez vérifier ou vous inscrire.";
            break;
        case 'password_mismatch':
            $error_message = "Les mots de passe ne correspondent pas. Veuillez réessayer.";
            break;
        case 'email_exists':
            $error_message = "Cette adresse email est déjà utilisée. Veuillez en choisir une autre.";
            break;
        case 'erreur_csrf':
            $error_message = "Erreur de sécurité. Veuillez réessayer.";
            break;
        default:
            $error_message = "Une erreur est survenue. Veuillez réessayer.";
    }
}

// Vérifier si un message de succès doit être affiché
$success_message = "";
if(isset($_GET['success']) && $_GET['success'] == 'register') {
    $success_message = "Inscription réussie ! Vous pouvez maintenant vous connecter.";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="shortcut icon" href="../assets/images/SNEAKERSADDICT.png" type="image/x-icon">
    <link href="../assets/css/style_optimized.css" rel="stylesheet">
    <link href="../assets/css/login_style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab&display=swap" rel="stylesheet"> 

    <title>Se connecter - SneakersAddict</title>
</head>
<body>
    <div class="container">
        <?php include '../includes/header.php'; ?>
        
        <div class="reste">
            <div class="content">
                <div class="login-container">
                    <div class="login-header">
                        <h2>Bienvenue sur SneakersAddict</h2>
                        <p>Connectez-vous pour accéder à votre compte</p>
                    </div>
                    
                    <div class="login-tabs">
                        <div class="login-tab active" id="tab-login">Connexion</div>
                        <div class="login-tab" id="tab-register">Inscription</div>
                    </div>
                    
                    <?php if(!empty($error_message)): ?>
                        <div class="error-message">
                            <i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if(!empty($success_message)): ?>
                        <div class="success-message">
                            <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
                        </div>
                    <?php endif; ?>
                    
                    <div id="login-form-container">
                        <form action="info.php" method="POST" class="login-form">
                            <!-- Jeton CSRF -->
                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                            
                            <div class="form-group">
                                <label for="email">Adresse email</label>
                                <input type="email" id="email" name="email" class="form-control" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="password">Mot de passe</label>
                                <div class="password-field">
                                    <input type="password" id="password" name="mdp" class="form-control" required>
                                    <i class="toggle-password fas fa-eye" id="toggle-password"></i>
                                </div>
                            </div>
                            
                            <div class="remember-me">
                                <input type="checkbox" id="remember" name="remember">
                                <label for="remember">Se souvenir de moi</label>
                            </div>
                            
                            <button type="submit" name="su" class="btn-submit">
                                <i class="fas fa-sign-in-alt"></i> Se connecter
                            </button>
                            
                            <div class="login-footer">
                                <a href="#" id="forgot-password">Mot de passe oublié ?</a>
                            </div>
                        </form>
                    </div>
                    
                    <div id="register-form-container" style="display: none;">
                        <form action="info.php" method="POST" class="login-form">
                            <!-- Jeton CSRF -->
                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                            
                            <div class="form-group">
                                <label for="register-name">Nom</label>
                                <input type="text" id="register-name" name="nom" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="register-prenom">Prenom</label>
                                <input type="text" id="register-prenom" name="prenom" class="form-control" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="register-email">Adresse email</label>
                                <input type="email" id="register-email" name="email" class="form-control" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="register-password">Mot de passe</label>
                                <div class="password-field">
                                    <input type="password" id="register-password" name="mdp" class="form-control" required>
                                    <i class="toggle-password fas fa-eye" id="toggle-register-password"></i>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="register-confirm">Confirmer le mot de passe</label>
                                <div class="password-field">
                                    <input type="password" id="register-confirm" name="confirm_mdp" class="form-control" required>
                                    <i class="toggle-password fas fa-eye" id="toggle-confirm-password"></i>
                                </div>
                            </div>
                            
                            <button type="submit" name="register" class="btn-submit">
                                <i class="fas fa-user-plus"></i> S'inscrire
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="footer">
                <p>SneakersAddict - Tous droits réservés.</p>
            </div>
        </div>
    </div>
    
    <script src="script.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Gestion des onglets
            const tabLogin = document.getElementById('tab-login');
            const tabRegister = document.getElementById('tab-register');
            const loginFormContainer = document.getElementById('login-form-container');
            const registerFormContainer = document.getElementById('register-form-container');
            
            tabLogin.addEventListener('click', function() {
                tabLogin.classList.add('active');
                tabRegister.classList.remove('active');
                loginFormContainer.style.display = 'block';
                registerFormContainer.style.display = 'none';
            });
            
            tabRegister.addEventListener('click', function() {
                tabRegister.classList.add('active');
                tabLogin.classList.remove('active');
                registerFormContainer.style.display = 'block';
                loginFormContainer.style.display = 'none';
            });
            
            // Gestion de l'affichage du mot de passe
            const togglePassword = document.getElementById('toggle-password');
            const password = document.getElementById('password');
            
            if (togglePassword && password) {
                togglePassword.addEventListener('click', function() {
                    const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                    password.setAttribute('type', type);
                    this.classList.toggle('fa-eye');
                    this.classList.toggle('fa-eye-slash');
                });
            }
            
            // Gestion de l'affichage du mot de passe pour l'inscription
            const toggleRegisterPassword = document.getElementById('toggle-register-password');
            const registerPassword = document.getElementById('register-password');
            
            if (toggleRegisterPassword && registerPassword) {
                toggleRegisterPassword.addEventListener('click', function() {
                    const type = registerPassword.getAttribute('type') === 'password' ? 'text' : 'password';
                    registerPassword.setAttribute('type', type);
                    this.classList.toggle('fa-eye');
                    this.classList.toggle('fa-eye-slash');
                });
            }
            
            // Gestion de l'affichage de la confirmation du mot de passe
            const toggleConfirmPassword = document.getElementById('toggle-confirm-password');
            const confirmPassword = document.getElementById('register-confirm');
            
            if (toggleConfirmPassword && confirmPassword) {
                toggleConfirmPassword.addEventListener('click', function() {
                    const type = confirmPassword.getAttribute('type') === 'password' ? 'text' : 'password';
                    confirmPassword.setAttribute('type', type);
                    this.classList.toggle('fa-eye');
                    this.classList.toggle('fa-eye-slash');
                });
            }
        });
    </script>
</body>
</html> 