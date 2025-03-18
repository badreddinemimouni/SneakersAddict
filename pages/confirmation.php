<?php
$a = session_id();
if (empty($a)) session_start();

require_once '../config/config.php';
require_once '../config/security.php';
require_once '../config/routes.php';

checkRouteAccess();

if (!isset($_SESSION['order'])) {
    header('Location: Produits.php');
    exit;
}

// recup les informations de la commande
$order = $_SESSION['order'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation de commande - SneakersAddict</title>
    
    <link rel="shortcut icon" href="../assets/images/SNEAKERSADDICT.png" type="image/x-icon">
    <link rel="stylesheet" href="../assets/css/style_optimized.css">
    <link rel="stylesheet" href="../assets/css/checkout.css">
    <link rel="stylesheet" href="../assets/css/confirmation.css">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab&display=swap" rel="stylesheet">
    
    <!-- Le style a été déplacé vers le fichier assets/css/confirmation.css -->
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="reste">
        <div class="content">
            <div class="checkout-container">
                <div class="checkout-steps">
                    <div class="step completed">1. Livraison</div>
                    <div class="step completed">2. Paiement</div>
                    <div class="step active">3. Confirmation</div>
                </div>
                
                <div class="confirmation-message">
                    <h2>Merci pour votre commande !</h2>
                    <p>Votre commande a été traitée avec succès. Un email de confirmation a été envoyé à l'adresse <?php echo htmlspecialchars($order['shipping']['email']); ?>.</p>
                </div>
                
                <div class="order-details">
                    <h3>Détails de la commande</h3>
                    
                    <div class="order-info">
                        <div>
                            <div class="order-info-item">
                                <strong>Numéro de commande</strong>
                                <?php echo htmlspecialchars($order['number']); ?>
                            </div>
                            <div class="order-info-item">
                                <strong>Date</strong>
                                <?php echo date('d/m/Y à H:i', strtotime($order['date'])); ?>
                            </div>
                            <div class="order-info-item">
                                <strong>Total</strong>
                                <?php echo number_format($order['total'], 2, ',', ' '); ?> €
                            </div>
                        </div>
                        
                        <div class="shipping-info">
                            <h4>Adresse de livraison</h4>
                            <div class="shipping-address">
                                <?php echo htmlspecialchars($order['shipping']['prenom'] . ' ' . $order['shipping']['nom']); ?><br>
                                <?php echo htmlspecialchars($order['shipping']['adresse']); ?><br>
                                <?php echo htmlspecialchars($order['shipping']['code_postal'] . ' ' . $order['shipping']['ville']); ?><br>
                                Tél: <?php echo htmlspecialchars($order['shipping']['telephone']); ?>
                            </div>
                        </div>
                    </div>
                    
                    <h3>Produits commandés</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Produit</th>
                                <th>Pointure</th>
                                <th>Quantité</th>
                                <th>Prix</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($order['products'] as $produit): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($produit['nom']); ?></td>
                                    <td><?php echo htmlspecialchars($produit['pointure']); ?></td>
                                    <td><?php echo htmlspecialchars($produit['quantite']); ?></td>
                                    <td><?php echo htmlspecialchars($produit['prix'] * $produit['quantite']); ?> €</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3"><strong>Total</strong></td>
                                <td><strong><?php echo number_format($order['total'], 2, ',', ' '); ?> €</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                
                <div class="continue-shopping">
                    <a href="Produits.php" class="btn-continue">Continuer vos achats</a>
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