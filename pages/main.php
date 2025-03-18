<?php
$a=session_id(); if(empty($a)) session_start();

require_once '../config/config.php';
require_once '../config/security.php';

// Pas de vérification d'authentification pour cette page - accessible à tous
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link rel="shortcut icon" href="../assets/images/SNEAKERSADDICT.png" type="image/x-icon">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab&display=swap" rel="stylesheet">    

    <title>SneakersAddict</title>

</head>

<body>
    <?php include '../includes/header.php'; ?>

    <div class="reste">
        <div class="content">
            <div id="id_qui">
                <h2 class="Qui">Qui sommes-nous ?</h2>
            </div>

            <div id="presentation">
                <img src="../assets/images/SNEAKERSADDICT.png" alt="Qui sommes-nous ?" style="float:left; margin-right:20px; width:30%;">
                <div class="desc_pres">
                    <p>SneakersAddict est votre destination ultime pour trouver les sneakers les plus rares et exclusives sur le marché. Que vous soyez un collectionneur passionné ou un amateur de mode à la recherche d'une paire unique, nous sommes là pour satisfaire votre obsession des sneakers.</p>
                    <p>Nous collaborons avec les meilleures marques et les revendeurs les plus réputés pour vous offrir une sélection exceptionnelle de sneakers haut de gamme. Notre équipe de passionnés de sneakers parcourt le monde à la recherche des modèles les plus recherchés et les plus convoités, afin de vous proposer une collection constamment renouvelée et diversifiée.</p>
                    <p>Chez SneakersAddict, nous sommes fiers de notre engagement envers l'authenticité et la qualité. Toutes nos sneakers sont soigneusement vérifiées et garanties 100% authentiques, vous permettant d'acheter en toute confiance. Notre service clientèle dévoué est également là pour répondre à toutes vos questions et vous fournir une expérience d'achat exceptionnelle.</p>
                    <p>Ne manquez pas l'opportunité de posséder des sneakers uniques qui feront tourner les têtes.</p>
                </div>
            </div>
        </div>

        <div class="footer">
            <p>SneakersAddict - Tous droits réservés.</p>
        </div>
    </div>

    <script src="../assets/js/script.js"></script>
</body>
</html>