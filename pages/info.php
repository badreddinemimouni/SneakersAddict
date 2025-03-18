<!DOCTYPE html>

<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../config/config.php';
require_once '../config/security.php';

if (isset($_SESSION['admin']) && $_SESSION['admin'] === true) {
    header('Location: ../admin/stock.php');
    exit();
}

try {
    $pdo = new PDO('mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8', DB_USER, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}

$_SESSION['admin'] = false;
$_SESSION['client'] = false;
$_SESSION['personne'] = true;

if (isset($_POST['register'])) {
    if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
        redirectWithError('login.php', 'erreur_csrf');
        exit;
    }
    
    $nom = cleanInput($_POST['nom']);
    $prenom = cleanInput($_POST['prenom']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['mdp'];
    $confirm_password = $_POST['confirm_mdp'];
    
    if ($password !== $confirm_password) {
        redirectWithError('login.php', 'password_mismatch');
        exit;
    }
    
    $stmt = $pdo->prepare("SELECT * FROM user_site WHERE email = ?");
    $stmt->execute([$email]);
    
    if ($stmt->rowCount() > 0) {
        redirectWithError('login.php', 'email_exists');
        exit;
    }
    
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("INSERT INTO user_site (nom, prenom, email, password, Grade) VALUES (?, ?, ?, ?, 'client')");
    $stmt->execute([$nom, $prenom, $email, $hashed_password]);
    
    header('Location: login.php?success=register');
    exit;
}

if (isset($_POST['su'])) {
    if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
        redirectWithError('login.php', 'erreur_csrf');
        exit;
    }
    
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['mdp'];
    
    $stmt = $pdo->prepare("SELECT * FROM user_site WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        redirectWithError('login.php', 'email');
        exit;
    }
    
    if (password_verify($password, $user['password'])) {
        $_SESSION['personne'] = false;
        
        if ($user['Grade'] == 'admin') {
            $_SESSION['admin'] = true;
            $_SESSION['client'] = false;
            header('Location: ../admin/stock.php');
        } else {
            $_SESSION['admin'] = false;
            $_SESSION['client'] = true;
            header('Location: main.php');
        }
        exit;
    } else {
        redirectWithError('login.php', 'password');
        exit;
    }
}

header("Location: login.php");
exit;
?>
