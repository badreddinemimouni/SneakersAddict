<?php 
$a=session_id(); if(empty($a)) session_start();

require_once '../config/config.php';
require_once '../config/security.php';
require_once '../config/routes.php';

// verif acces utilisateur
checkRouteAccess();

try {
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    if ($mysqli->connect_error) {
        throw new Exception("Erreur de connexion : " . $mysqli->connect_error);
    }
} catch (Exception $e) {
    die("Erreur : " . $e->getMessage());
}

// Initialiser le panier s'il n'existe pas
if(!isset($_SESSION['panier'])) {
    $_SESSION['panier'] = array();
}

// Traitement des actions sur le panier
if(isset($_GET['action'])) {
    $action = $_GET['action'];
    
    // Supprimer un article du panier
    if($action == 'supprimer' && isset($_GET['id'])) {
        $id_produit = $_GET['id'];
        if(isset($_SESSION['panier'][$id_produit])) {
            unset($_SESSION['panier'][$id_produit]);
        }
    }
    
    // Vider tout le panier
    if($action == 'vider') {
        $_SESSION['panier'] = array();
    }
    
    // Modifier la quantité d'un article
    if($action == 'modifier' && isset($_GET['id']) && isset($_GET['quantite'])) {
        $id_produit = $_GET['id'];
        $quantite = intval($_GET['quantite']);
        
        if(isset($_SESSION['panier'][$id_produit]) && $quantite > 0) {
            $_SESSION['panier'][$id_produit]['quantite'] = $quantite;
        }
    }
    
    // Rediriger pour éviter les soumissions multiples
    header("Location: Panier.php");
    exit;
}

// Calculer le total du panier
$total = 0;
foreach($_SESSION['panier'] as $produit) {
    $total += $produit['prix'] * $produit['quantite'];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Votre Panier - SneakersAddict</title>

    <link rel="shortcut icon" href="../assets/images/SNEAKERSADDICT.png" type="image/x-icon">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab&display=swap" rel="stylesheet">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="reste">
        <div class="content">
            <div id="Panier_liste">
                <h2>Votre Panier</h2>
                
                <?php if(isset($_GET['confirmation']) && isset($_SESSION['message_commande'])): ?>
                    <div class="message-confirmation">
                        <?php echo $_SESSION['message_commande']; ?>
                    </div>
                    <?php unset($_SESSION['message_commande']); ?>
                <?php endif; ?>
                
                <?php if(empty($_SESSION['panier'])): ?>
                    <div class="panier-vide">
                        <p>Votre panier est vide.</p>
                        <p><a href="Produits.php">Continuer vos achats</a></p>
                    </div>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Produit</th>
                                <th>Pointure</th>
                                <th>Prix unitaire</th>
                                <th>Quantité</th>
                                <th>Total</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($_SESSION['panier'] as $id_produit => $produit): ?>
                                <tr>
                                    <td><img src="<?php echo htmlspecialchars($produit['image']); ?>" alt="<?php echo htmlspecialchars($produit['nom']); ?>"></td>
                                    <td><?php echo htmlspecialchars($produit['nom']); ?></td>
                                    <td><?php echo htmlspecialchars($produit['pointure']); ?></td>
                                    <td><?php echo htmlspecialchars($produit['prix']); ?> €</td>
                                    <td>
                                        <form method="get" action="Panier.php" style="display:inline;">
                                            <input type="hidden" name="action" value="modifier">
                                            <input type="hidden" name="id" value="<?php echo $id_produit; ?>">
                                            <input type="number" name="quantite" value="<?php echo $produit['quantite']; ?>" min="1" class="quantite-input">
                                            <button type="submit" class="btn-action btn-update">Mettre à jour</button>
                                        </form>
                                    </td>
                                    <td><?php echo $produit['prix'] * $produit['quantite']; ?> €</td>
                                    <td>
                                        <a href="Panier.php?action=supprimer&id=<?php echo $id_produit; ?>" class="btn-action">Supprimer</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    
                    <div class="panier-total">
                        Total : <?php echo number_format($total, 2, ',', ' '); ?> €
                    </div>
                    
                    <div class="panier-actions">
                        <a href="Panier.php?action=vider" class="btn-action">Vider le panier</a>
                        <a href="checkout.php" class="btn-action btn-payer">Procéder au paiement</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="footer">
            <p>SneakersAddict - Tous droits réservés.</p>
        </div>
    </div>
    
    <script src="script.js"></script>
</body>
</html>