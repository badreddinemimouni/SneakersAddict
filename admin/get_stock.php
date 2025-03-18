<?php
// demarrage de la session comme partout lol
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../config/config.php';
require_once '../config/security.php';
require_once '../config/routes.php';

// verif des droits avec notre fonction
checkRouteAccess();

// verifie si on a bien les params qu'on attend
// sinon ca sert a rien de continuer
if (!isset($_GET['produit_id']) || !isset($_GET['pointure_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Paramètres manquants']);
    exit();
}

// on fait l'cast en int pour eviter les injections sql 
$produit_id = (int)$_GET['produit_id'];
$pointure_id = (int)$_GET['pointure_id'];

try {
    $pdo = new PDO('mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8', DB_USER, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // recupere la quantité en stock pour cette paire de chaussures
    $stmt = $pdo->prepare("SELECT amount FROM stock_size WHERE stock_id = ? AND size_id = ?");
    $stmt->execute([$produit_id, $pointure_id]);
    $stock = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($stock) {
        // si on a trouvé un stock, on renvoie la quantité
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'amount' => $stock['amount']]);
    } else {
        // si pas de stock trouvé, on renvoie 0
        // pour pas d'erreur js
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'amount' => 0]);
    }
} catch (Exception $e) {
    // en cas d'erreur, on renvoie le message
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
// important de faire un exit a la fin 
// sinon ca peut continuer et envoyer du html en plus du json
exit();
?> 