<?php 
$a=session_id(); if(empty($a)) session_start();

require_once '../config/config.php';
require_once '../config/security.php';
require_once '../config/routes.php';

checkRouteAccess();

try {
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    if ($mysqli->connect_error) {
        throw new Exception("Erreur de connexion : " . $mysqli->connect_error);
    }
    $mysqli->set_charset("utf8");
} catch (Exception $e) {
    die("Erreur : " . $e->getMessage());
}

// Récupération de l'ID de la chaussure depuis l'URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    // Redirection vers la page des produits si aucun ID valide n'est fourni
    header("Location: Produits.php");
    exit;
}

// recup informations de la chaussure
$requete = "SELECT id, nom, prix, image, couleur FROM stock WHERE id = ?";
$stmt = $mysqli->prepare($requete);
$stmt->bind_param("i", $id);
$stmt->execute();
$resultat = $stmt->get_result();

if ($resultat->num_rows == 0) {
    // Redirection si la chaussure n'existe pas
    header("Location: Produits.php");
    exit;
}

$ligne = $resultat->fetch_assoc();
$nom = $ligne['nom'];
$prix = $ligne['prix'];
$image = $ligne['image'] ?: "../assets/images/default.webp"; // Image par défaut si non définie
$couleur = $ligne['couleur'] ?: "Non spécifiée";

if(isset($_POST['ajouter_panier'])) {
    $pointure = isset($_POST['pointureC']) ? $_POST['pointureC'] : 0;
    
    // Vérifier la disponibilité avant d'ajouter au panier
    $requete_dispo = "SELECT ss.amount 
                      FROM stock s
                      JOIN stock_size ss ON s.id = ss.stock_id
                      JOIN size sz ON ss.size_id = sz.id
                      WHERE s.id = ? AND sz.size = ?";
    
    $stmt_dispo = $mysqli->prepare($requete_dispo);
    $stmt_dispo->bind_param("ii", $id, $pointure);
    $stmt_dispo->execute();
    $resultat_dispo = $stmt_dispo->get_result();
    
    if($resultat_dispo->num_rows > 0) {
        $stock_info = $resultat_dispo->fetch_assoc();
        
        if($stock_info['amount'] > 0) {
            // Initialiser le panier s'il n'existe pas
            if(!isset($_SESSION['panier'])) {
                $_SESSION['panier'] = array();
            }
            
            $produit_id = $id . '_' . $pointure;
            
            // Vérifier si le produit est déjà dans le panier
            if(isset($_SESSION['panier'][$produit_id])) {
                // Incrémenter la quantité
                $_SESSION['panier'][$produit_id]['quantite']++;
            } else {
                // Ajouter le produit au panier
                $_SESSION['panier'][$produit_id] = array(
                    'id' => $id,
                    'nom' => $nom,
                    'prix' => $prix,
                    'pointure' => $pointure,
                    'image' => $image,
                    'couleur' => $couleur,
                    'quantite' => 1
                );
            }
            
            // Rediriger vers la même page avec un paramètre pour afficher le mini-panier
            header("Location: Chaussure.php?id=$id&ajout=1");
            exit;
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
    <title><?php echo htmlspecialchars($nom); ?> - SneakersAddict</title>

    <link rel="shortcut icon" href="../assets/images/SNEAKERSADDICT.png" type="image/x-icon">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab&display=swap" rel="stylesheet">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="reste">
        <div class="content">
            <form method="POST" class="product-detail">
                <div id="Chaussure1" class="product-container">
                    <img src="<?php echo htmlspecialchars($image); ?>" alt="<?php echo htmlspecialchars($nom); ?>" class="product-image">

                    <p id="nom_shoes" class="product-name"><?php echo htmlspecialchars($nom); ?></p>

                    <div id="couleurs" class="product-colors">
                        <p>Couleur : </p>
                        <div id="cadre_couleur1">
                            <p id="couleur1"><?php echo htmlspecialchars($couleur); ?></p>
                        </div>
                    </div>

                    <p id="dispo" class="product-availability">En stock : 
                    <?php
                    if(isset($_POST['verifs'])){
                        $pointures = $_POST['pointureC'];
                        
                        // Requête pour vérifier la disponibilité
                        $requete_dispo = "SELECT ss.amount 
                                        FROM stock s
                                        JOIN stock_size ss ON s.id = ss.stock_id
                                        JOIN size sz ON ss.size_id = sz.id
                                        WHERE s.id = ? AND sz.size = ?";
                        
                        $stmt_dispo = $mysqli->prepare($requete_dispo);
                        $stmt_dispo->bind_param("ii", $id, $pointures);
                        $stmt_dispo->execute();
                        $resultat_dispo = $stmt_dispo->get_result();
                        
                        if($resultat_dispo->num_rows == 0){
                            echo '<img src="../assets/images/Rond_rouge.png" alt="Rond_rouge" style="width:20px;"> ';
                            echo "Taille : " . htmlspecialchars($pointures); 
                            echo "<div id='Panier'>";
                            echo "\n";
                        } else {
                            $stock_info = $resultat_dispo->fetch_assoc();
                            
                            if($stock_info['amount'] == 0){
                                echo '<img src="../assets/images/Rond_rouge.png" alt="Rond_rouge" style="width:20px;"> ';
                                echo "Taille : " . htmlspecialchars($pointures); 
                                echo "<div id='Panier'>";
                                echo "\n";
                            } else {
                                echo '<img src="../assets/images/Rond_vert.png" alt="Rond_vert" style="width:20px;"> ';
                                echo "Taille : " . htmlspecialchars($pointures);
                                echo "<div id='Panier'>";
                                echo "\n";
                                echo "<button type='submit' name='ajouter_panier' class='add-to-cart-btn'>Ajouter au panier";
                                echo "\n";
                                echo "</button>"; 
                                echo "\n";
                            }
                        }
                    }
                    ?>
                    </p>
                    <p id="prix" class="product-price">Prix : <?php echo htmlspecialchars($prix); ?>€</p>

                    <div id="pointure_cadre" class="size-selector">
                        <label for="pointure">Pointure : </label>
                        <select id="pointure" name="pointureC">
                            <?php
                            // recup des pointures disponibles pour cette chaussure
                            $requete_pointures = "SELECT DISTINCT sz.size 
                                                FROM size sz
                                                JOIN stock_size ss ON sz.id = ss.size_id
                                                WHERE ss.stock_id = ?
                                                ORDER BY sz.size";
                            
                            $stmt_pointures = $mysqli->prepare($requete_pointures);
                            $stmt_pointures->bind_param("i", $id);
                            $stmt_pointures->execute();
                            $resultat_pointures = $stmt_pointures->get_result();
                            
                            while($pointure = $resultat_pointures->fetch_assoc()) {
                                echo '<option value="' . $pointure['size'] . '">' . $pointure['size'] . '</option>';
                            }
                            
                            // Si aucune pointure n'est disponible, afficher des valeurs par défaut
                            if($resultat_pointures->num_rows == 0) {
                                echo '<option value="36">36</option>';
                                echo '<option value="37">37</option>';
                                echo '<option value="38">38</option>';
                                echo '<option value="39">39</option>';
                                echo '<option value="40">40</option>';
                            }
                            ?>
                        </select>
                            
                        <input type="submit" value="Vérification" id="verif_pointure" name="verifs" class="check-btn">
                    </div>
                </div>
            </form>
        </div>

        <div class="footer">
            <p>SneakersAddict - Tous droits réservés.</p>
        </div>
    </div>

    <script src="script.js"></script>

</body>
</html> 