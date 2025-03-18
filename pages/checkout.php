<?php
$a = session_id();
if (empty($a)) session_start();

require_once '../config/config.php';
require_once '../config/security.php';
require_once '../config/routes.php';

// verif acces route
checkRouteAccess();


if (!isset($_SESSION['panier']) || empty($_SESSION['panier'])) {
    header('Location: Panier.php');
    exit;
}

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
        $nom = cleanInput($_POST['nom'] ?? '');
        $prenom = cleanInput($_POST['prenom'] ?? '');
        $adresse = cleanInput($_POST['adresse'] ?? '');


        $ville = cleanInput($_POST['ville'] ?? '');

        $code_postal = cleanInput($_POST['code_postal'] ?? '');
        $email = cleanInput($_POST['email'] ?? '');



        $telephone = cleanInput($_POST['telephone'] ?? '');
        
        if (empty($nom)) $errors[] = "Le nom est requis";

        if (empty($prenom)) $errors[] = "Le prénom est requis";
        if (empty($adresse)) $errors[] = "L'adresse est requise";
        if (empty($ville)) $errors[] = "La ville est requise";

        if (empty($code_postal)) $errors[] = "Le code postal est requis";
        if (empty($email)) $errors[] = "L'email est requis";
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "L'email n'est pas valide";
        if (empty($telephone)) $errors[] = "Le téléphone est requis";
        
        // Si pas d'erreurs, rediriger vers la page de paiement
        if (empty($errors)) {
            $_SESSION['livraison'] = [
                'nom' => $nom,
                'prenom' => $prenom,
                'adresse' => $adresse,
                'ville' => $ville,
                'code_postal' => $code_postal,
                'email' => $email,
                'telephone' => $telephone
            ];
            
            header('Location: payment.php');
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
    <title>Checkout - SneakersAddict</title>
    
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
                <h1>Finaliser votre commande</h1>
                
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
                    <div class="step active">1. Livraison</div>
                    <div class="step">2. Paiement</div>
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
                    <h3>Informations de livraison</h3>
                    <form method="post" action="checkout.php">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="nom">Nom</label>
                                <input type="text" id="nom" name="nom" required>
                            </div>
                            <div class="form-group">
                                <label for="prenom">Prénom</label>
                                <input type="text" id="prenom" name="prenom" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="adresse">Adresse</label>
                            <input type="text" id="adresse" name="adresse" required>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="ville">Ville</label>
                                <input type="text" id="ville" name="ville" required>
                            </div>
                            <div class="form-group">
                                <label for="code_postal">Code postal</label>
                                <input type="text" id="code_postal" name="code_postal" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="telephone">Téléphone</label>
                            <input type="tel" id="telephone" name="telephone" required>
                        </div>
                        
                        <div class="form-actions">
                            <a href="Panier.php" class="btn-secondary">Retour au panier</a>
                            <button type="submit" class="btn-primary">Continuer vers le paiement</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="footer">
            <p>SneakersAddict - Tous droits réservés.</p>
        </div>
    </div>
    
    <script src="script.js"></script>
</body>
</html> 