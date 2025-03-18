<?php
// demarrage session et securité
session_start();
require_once '../config/config.php';
require_once '../config/security.php';
require_once '../config/routes.php';

// verif acces
checkRouteAccess();

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les données du formulaire
    $nom = $_POST['name'];
    $email = $_POST['email'];
    $sujet = $_POST['subject'];
    $message = $_POST['message'];
    
    // Adresse email de destination
    $destinataire = "badreddine.mimounipro@gmail.com";
    
    // Contenu du message
    $contenu_email = "
    <html>
    <head>
        <title>Nouveau message de contact</title>
        <style>
            body { font-family: Arial, sans-serif; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; }
            .header { background-color: #333; color: #fff; padding: 10px; }
            .content { padding: 20px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>Nouveau message de contact</h2>
            </div>
            <div class='content'>
                <p><strong>Nom:</strong> $nom</p>
                <p><strong>Email:</strong> $email</p>
                <p><strong>Sujet:</strong> $sujet</p>
                <p><strong>Message:</strong></p>
                <p>" . nl2br($message) . "</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    // Enregistrer le message dans un fichier (méthode de secours)
    $dossier_messages = "messages_contact";
    
    // Créer le dossier s'il n'existe pas
    if (!is_dir($dossier_messages)) {
        mkdir($dossier_messages, 0777, true);
    }
    
    // Créer un nom de fichier unique
    $nom_fichier = $dossier_messages . "/" . date("Y-m-d_H-i-s") . "_" . str_replace("@", "_", $email) . ".html";
    
    // Enregistrer le message dans un fichier
    file_put_contents($nom_fichier, $contenu_email);
    
    // Tentative d'envoi de l'email via SMTP direct
    $envoi_reussi = false;
    
    try {
        // Configuration SMTP
        $smtp_server = "smtp.outlook.com";
        $smtp_port = 587;
        $smtp_username = "badreddine.mimounipro@gmail.com";
        $smtp_password = ""; // Remplacez par votre mot de passe réel
        
        // Créer une connexion au serveur SMTP
        $socket = fsockopen($smtp_server, $smtp_port, $errno, $errstr, 30);
        
        if (!$socket) {
            throw new Exception("Impossible de se connecter au serveur SMTP: $errstr ($errno)");
        }
        
        // Lire la réponse du serveur
        $response = fgets($socket, 515);
        if (substr($response, 0, 3) != '220') {
            throw new Exception("Erreur de connexion SMTP: $response");
        }
        
        // Dire bonjour au serveur
        fputs($socket, "HELO " . $_SERVER['HTTP_HOST'] . "\r\n");
        $response = fgets($socket, 515);
        if (substr($response, 0, 3) != '250') {
            throw new Exception("Erreur HELO: $response");
        }
        
        // Authentification
        fputs($socket, "AUTH LOGIN\r\n");
        $response = fgets($socket, 515);
        if (substr($response, 0, 3) != '334') {
            throw new Exception("Erreur AUTH: $response");
        }
        
        fputs($socket, base64_encode($smtp_username) . "\r\n");
        $response = fgets($socket, 515);
        if (substr($response, 0, 3) != '334') {
            throw new Exception("Erreur nom d'utilisateur: $response");
        }
        
        fputs($socket, base64_encode($smtp_password) . "\r\n");
        $response = fgets($socket, 515);
        if (substr($response, 0, 3) != '235') {
            throw new Exception("Erreur mot de passe: $response");
        }
        
        // Définir l'expéditeur
        fputs($socket, "MAIL FROM: <$smtp_username>\r\n");
        $response = fgets($socket, 515);
        if (substr($response, 0, 3) != '250') {
            throw new Exception("Erreur MAIL FROM: $response");
        }
        
        // Définir le destinataire
        fputs($socket, "RCPT TO: <$destinataire>\r\n");
        $response = fgets($socket, 515);
        if (substr($response, 0, 3) != '250') {
            throw new Exception("Erreur RCPT TO: $response");
        }
        
        // Commencer les données
        fputs($socket, "DATA\r\n");
        $response = fgets($socket, 515);
        if (substr($response, 0, 3) != '354') {
            throw new Exception("Erreur DATA: $response");
        }
        
        // Créer les en-têtes de l'email
        $headers = "From: $nom <$email>\r\n";
        $headers .= "Reply-To: $email\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        
        // Envoyer les en-têtes et le message
        fputs($socket, "Subject: Contact: $sujet\r\n");
        fputs($socket, $headers . "\r\n");
        fputs($socket, $contenu_email . "\r\n.\r\n");
        $response = fgets($socket, 515);
        if (substr($response, 0, 3) != '250') {
            throw new Exception("Erreur envoi message: $response");
        }
        
        // Quitter
        fputs($socket, "QUIT\r\n");
        
        // Fermer la connexion
        fclose($socket);
        
        $envoi_reussi = true;
        
    } catch (Exception $e) {
        // Enregistrer l'erreur dans un fichier de log
        $log_file = $dossier_messages . "/error_log.txt";
        file_put_contents($log_file, date("Y-m-d H:i:s") . " - Erreur: " . $e->getMessage() . "\n", FILE_APPEND);
    }
    
    // Rediriger vers la page de contact avec un message de succès ou d'erreur
    if ($envoi_reussi) {
        header("Location: Contact.php?envoi=succes");
    } else {
        header("Location: Contact.php?envoi=erreur&sauvegarde=1");
    }
    
    exit;
}
?> 