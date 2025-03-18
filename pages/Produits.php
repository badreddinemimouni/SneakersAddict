<?php 
$a=session_id(); if(empty($a)) session_start();

require_once '../config/config.php';
require_once '../config/security.php';
require_once '../config/routes.php';

// verif des droits d'acces a la page
checkRouteAccess();

try {
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    if ($mysqli->connect_error) {
        throw new Exception("Erreur de connexion : " . $mysqli->connect_error);
    }
} catch (Exception $e) {
    die("Erreur : " . $e->getMessage());
}

$mysqli->set_charset("utf8");

$requete = "SELECT id, nom, prix, image, couleur FROM stock ORDER BY id";
$resultat = $mysqli->query($requete);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nos produits - SneakersAddict</title>

    <link rel="shortcut icon" href="../assets/images/SNEAKERSADDICT.png" type="image/x-icon">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab&display=swap" rel="stylesheet">    
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="reste">
        <div class="content">
            <h1 id="nos_produits">Nos produits</h1>

            <div id="conteneur">
                <?php
                // Vérifier s'il y a des produits
                if ($resultat->num_rows > 0) {
                    $counter = 0;
                    while ($produit = $resultat->fetch_assoc()) {
                        $counter++;
                        $id = $produit['id'];
                        $nom = htmlspecialchars($produit['nom']);
                        $image = htmlspecialchars($produit['image']);
                        
                        // Si l'image n'est pas définie, utiliser une image par défaut
                        
                        
                        echo '<div id="chaussure' . $counter . '" class="produit-item">';
                        echo '<a href="Chaussure.php?id=' . $id . '">';
                        echo '<picture>';
                        echo '<img src="' . $image . '" class="images" alt="' . $nom . '">';
                        echo '<p class="description">' . $nom . '</p>';
                        echo '</picture>';
                        echo '</a>';
                        echo '</div>';
                    }
                } else {
                    echo '<p>Aucun produit disponible pour le moment.</p>';
                }
                ?>
            </div>
        </div>

        <div class="footer">
            <p>SneakersAddict - Tous droits réservés.</p>
        </div>
    </div>
    
    <script src="../assets/js/script.js"></script>
</body>
</html>