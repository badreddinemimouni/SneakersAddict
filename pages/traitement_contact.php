<?php
// Démarrage de la session et inclusion des fichiers nécessaires
session_start();
require_once '../config/config.php';
require_once '../config/security.php';
require_once '../config/routes.php';

// verif accesss
checkRouteAccess();

// verif formulaire envoyé
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // recup données formulaire
    $nom = $_POST['name'];
    $email = $_POST['email'];
    $sujet = $_POST['subject'];
    $message = $_POST['message'];
    
    // adresse email de destination
    $destinataire = "badreddine.mimounipro@gmail.com";
    
    // creation contenu email
    $contenu_email = "
    <html>
    <head>
        <title>Nouveau message de contact</title>
    </head>
    <body>
        <h2>Nouveau message de contact</h2>
        <p><strong>Nom:</strong> $nom</p>
        <p><strong>Email:</strong> $email</p>
        <p><strong>Sujet:</strong> $sujet</p>
        <p><strong>Message:</strong></p>
        <p>" . nl2br($message) . "</p>
    </body>
    </html>
    ";
    
    // configuration en-tetes email
    $headers = "From: $nom <$email>\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    
    // tentative envoi email
    $envoi_reussi = mail($destinataire, "Contact: $sujet", $contenu_email, $headers);
    
    // redirection avec message succes ou erreur
    if ($envoi_reussi) {
        header("Location: Contact.php?envoi=succes");
    } else {
        header("Location: Contact.php?envoi=erreur");
    }
    
    exit;
}
?> 