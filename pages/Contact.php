<?php 
$a=session_id(); if(empty($a)) session_start(); 

require_once '../config/config.php';
require_once '../config/security.php';
require_once '../config/routes.php';

// verif acces route 
checkRouteAccess();

$message_statut = "";
if (isset($_GET['envoi'])) {
    if ($_GET['envoi'] == 'succes') {
        $message_statut = '<div class="alert alert-success"><i class="fas fa-check-circle"></i> Votre message a été envoyé avec succès !</div>';
    } elseif ($_GET['envoi'] == 'erreur') {
        if (isset($_GET['sauvegarde']) && $_GET['sauvegarde'] == '1') {
            $message_statut = '<div class="alert alert-warning"><i class="fas fa-exclamation-circle"></i> L\'envoi de l\'email a échoué, mais votre message a été enregistré. Nous le traiterons dès que possible.</div>';
        } else {
            $message_statut = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Une erreur s\'est produite lors de l\'envoi du message. Veuillez réessayer.</div>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact - SneakersAddict</title>

    <link rel="shortcut icon" href="../assets/images/SNEAKERSADDICT.png" type="image/x-icon"> <!-- Définit l'icône du site -->
    <link href="../assets/css/style_optimized.css" rel="stylesheet"> <!-- Importe la feuille de style optimisée -->
    <link href="../assets/css/contact.css" rel="stylesheet"> <!-- Importe la feuille de style spécifique à la page contact -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <link rel="preconnect" href="https://fonts.googleapis.com"> <!-- Préconnecte au domaine de Google Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin> <!-- Préconnecte au domaine de Google Fonts en spécifiant l'attribut "crossorigin" -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab&display=swap" rel="stylesheet"> <!-- Importe la police de caractères "Roboto Slab" depuis Google Fonts -->
</head>
<body>
    <div class="container">
        <?php include '../includes/header.php'; ?>
        
        <div class="reste">
            <div class="content">
                <h1 class="page-title">Contactez-nous</h1>
                
                <div class="contact-container">
                    <div class="contact-info">
                        <h3>Informations de contact</h3>
                        <p>Nous sommes là pour vous aider. N'hésitez pas à nous contacter pour toute question concernant nos produits ou services.</p>
                        
                        <div class="contact-info-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>123 Rue des Sneakers, 75000 Paris</span>
                        </div>
                        
                        <div class="contact-info-item">
                            <i class="fas fa-phone"></i>
                            <span>+33 1 23 45 67 89</span>
                        </div>
                        
                        <div class="contact-info-item">
                            <i class="fas fa-envelope"></i>
                            <span>badr_mim@outlook.fr</span>
                        </div>
                        
                        <div class="contact-info-item">
                            <i class="fas fa-clock"></i>
                            <span>Lun-Ven: 9h-18h | Sam: 10h-16h</span>
                        </div>
                    </div>
                    
                    <div class="contact-form">
                        <h3>Envoyez-nous un message</h3>
                        
                        <div id="form-messages">
                            <?php echo $message_statut; ?>
                        </div>
                        
                        <form id="contact-form" method="post" action="traitement_contact.php">
                            <div class="form-group">
                                <label for="name">Nom complet</label>
                                <input type="text" id="name" name="name" class="form-control" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="email">Adresse email</label>
                                <input type="email" id="email" name="email" class="form-control" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="subject">Sujet</label>
                                <input type="text" id="subject" name="subject" class="form-control" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="message">Message</label>
                                <textarea id="message" name="message" class="form-control" rows="5" required></textarea>
                            </div>
                            
                            <button type="submit" name="submit" class="submit-btn">
                                <i class="fas fa-paper-plane"></i> Envoyer le message
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <?php include '../includes/footer.php'; ?>
        </div>
    </div>

    <script src="../assets/js/script.js"></script>
</body>
</html>
