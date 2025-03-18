<?php
$a = session_id();
if (empty($a)) session_start();

require_once '../config/config.php';
require_once '../config/security.php';
require_once '../config/routes.php';

// Vérifier l'accès à la route
checkRouteAccess();

try {
    $pdo = new PDO('mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8', DB_USER, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}


// Rediriger si le panier est vide ou si les informations de livraison ne sont pas définies
if (!isset($_SESSION['panier']) || empty($_SESSION['panier']) || !isset($_SESSION['livraison'])) {
    header('Location: checkout.php');
    exit;
}

//  total du panier
$total = 0;
foreach ($_SESSION['panier'] as $produit) {
    $total += $produit['prix'] * $produit['quantite'];
}

$csrf_token = generateCSRFToken();

$errors = [];
$success = false;


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
        $errors[] = "Erreur de sécurité. Veuillez réessayer.";
    } else {
        $card_number = cleanInput($_POST['card_number'] ?? '');
        $card_name = cleanInput($_POST['card_name'] ?? '');
        $card_expiry = cleanInput($_POST['card_expiry'] ?? '');
        $card_cvv = cleanInput($_POST['card_cvv'] ?? '');
        
        if (empty($card_number)) $errors[] = "Le numéro de carte est requis";
        if (empty($card_name)) $errors[] = "Le nom sur la carte est requis";
        if (empty($card_expiry)) $errors[] = "La date d'expiration est requise";
        if (empty($card_cvv)) $errors[] = "Le code de sécurité est requis";
        
        if (!preg_match('/^\d{16}$/', str_replace(' ', '', $card_number))) {
            $errors[] = "Le numéro de carte doit contenir 16 chiffres";
        }
        
        if (!preg_match('/^\d{3,4}$/', $card_cvv)) {
            $errors[] = "Le code de sécurité doit contenir 3 ou 4 chiffres";
        }
        
        if (empty($errors)) {
            // Générer un numéro de commande unique
            $order_number = 'SA-' . date('YmdHis') . '-' . rand(1000, 9999);
            
            // Stocker les informations de la commande dans la session
            $_SESSION['order'] = [
                'number' => $order_number,
                'date' => date('Y-m-d H:i:s'),
                'total' => $total,
                'products' => $_SESSION['panier'],
                'shipping' => $_SESSION['livraison']
            ];
            
            if(isset($_POST['action']) && $_POST['action'] == 'payer'){
                foreach($_SESSION['panier'] as $produit){
                    $stmt = $pdo->prepare("SELECT id FROM size WHERE size = ?");
                    $stmt->execute([$produit['pointure']]);
                    $size_result = $stmt->fetch(PDO::FETCH_ASSOC);
                    $size_id = $size_result['id'];

                    $produit_id = $produit['id'];
                    $produit_pointure = $produit['pointure'];
                    $stmt = $pdo->prepare("UPDATE stock_size SET amount = amount - ? WHERE stock_id = ? AND size_id = ?");
                    $stmt->execute([$produit['quantite'],$produit_id,$size_id]);


                   }
                   
            }
            // Vider le panier
            $_SESSION['panier'] = array();
            
            // Rediriger vers la page de confirmation
            header('Location: confirmation.php');
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
    <title>Paiement - SneakersAddict</title>
    
    <link rel="shortcut icon" href="../assets/images/SNEAKERSADDICT.png" type="image/x-icon">
    <link rel="stylesheet" href="../assets/css/style_optimized.css">
    <link rel="stylesheet" href="../assets/css/checkout.css">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab&display=swap" rel="stylesheet">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="reste">
        <div class="content">
            <div class="checkout-container">
                <h1>Paiement</h1>
                
                <?php if (!empty($errors)): ?>
                    <div class="error-message">
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <div class="checkout-steps">
                    <div class="step completed">1. Livraison</div>
                    <div class="step active">2. Paiement</div>
                    <div class="step">3. Confirmation</div>
                </div>
                
                <div class="checkout-summary">
                    <h3>Récapitulatif de votre commande</h3>
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
                            <?php foreach ($_SESSION['panier'] as $produit): ?>
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
                                <td><strong><?php echo number_format($total, 2, ',', ' '); ?> €</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                
                <div class="checkout-form">
                    <h3>Informations de paiement</h3>
                    
                    <div class="card-icons">
                        <div class="card-icon"><img src="../assets/images/visa.png" alt="Visa" width="40"></div>
                        <div class="card-icon"><img src="../assets/images/mastercard.png" alt="Mastercard" width="40"></div>
                        <div class="card-icon"><img src="../assets/images/amex.png" alt="American Express" width="40"></div>
                    </div>
                    
                    <form method="post" action="payment.php" id="payment-form">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                        <input type="hidden" name="action" value="payer">
                        <div class="form-group">
                            <label for="card_number">Numéro de carte</label>
                            <input type="text" id="card_number" name="card_number" placeholder="1234 5678 9012 3456" maxlength="19" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="card_name">Nom sur la carte</label>
                            <input type="text" id="card_name" name="card_name" placeholder="JEAN DUPONT" required>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="card_expiry">Date d'expiration (MM/AA)</label>
                                <input type="text" id="card_expiry" name="card_expiry" placeholder="MM/AA" maxlength="5" required>
                            </div>
                            <div class="form-group">
                                <label for="card_cvv">Code de sécurité (CVV)</label>
                                <input type="text" id="card_cvv" name="card_cvv" placeholder="123" maxlength="4" required>
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <a href="checkout.php" class="btn-secondary">Retour</a>
                            <button type="submit" class="btn-primary">Payer <?php echo number_format($total, 2, ',', ' '); ?> €</button>
                        </div>
                    </form>
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