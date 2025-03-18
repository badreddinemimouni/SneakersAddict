<?php
/**
 * fichier contenant la config des routes de l'application
 */

// routes accessibles sans etre connecté
$public_routes = [
    'login.php',
    'info.php',
    'deconnexion.php',
    'main.php', // page de presentation accessible a tous
];

// routes pour les clients connectés
$client_routes = [
    'main.php',
    'Produits.php',
    'Chaussure.php',
    'Panier.php',
    'Contact.php',
    'checkout.php',
    'payment.php',
    'confirmation.php'
];

// routes pour les admins
$admin_routes = [
    'main.php',
    'Produits.php',
    'Chaussure.php',
    'Panier.php',
    'Contact.php',
    'stock.php',
    'get_stock.php',
    'checkout.php',
    'payment.php',
    'confirmation.php',
    'admin_users.php'
];


function canAccessRoute($route) {
    global $public_routes, $client_routes, $admin_routes;
    
    // demarrer la session si pas deja fait
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // les routes publiques sont accessibles a tlm
    if (in_array($route, $public_routes)) {
        return true;
    }
    
    // verif si user est connecté (personne = false quand on est connecté, c bizarre mais c comme ca)
    $is_logged_in = isset($_SESSION['personne']) && $_SESSION['personne'] === false;
    
    if (!$is_logged_in) {
        // pas connecté donc pas d'acces
        return false;
    }
    
    // verif si admin
    if (isset($_SESSION['admin']) && $_SESSION['admin'] === true) {
        return in_array($route, $admin_routes);
    }
    
    // verif si client
    if (isset($_SESSION['client']) && $_SESSION['client'] === true) {
        return in_array($route, $client_routes);
    }
    
    // si on arrive la c que y'a un pb
    return false;
}


function checkRouteAccess() {
    // nom du fichier actuel
    $current_route = basename($_SERVER['PHP_SELF']);
    
    // debug pour voir ce qui se passe - a enlever apres
    // echo "Route actuelle: " . $current_route . "<br>";
    // echo "Session personne: " . (isset($_SESSION['personne']) ? ($_SESSION['personne'] ? 'true' : 'false') : 'non défini') . "<br>";
    // echo "Session admin: " . (isset($_SESSION['admin']) ? ($_SESSION['admin'] ? 'true' : 'false') : 'non défini') . "<br>";
    // echo "Session client: " . (isset($_SESSION['client']) ? ($_SESSION['client'] ? 'true' : 'false') : 'non défini') . "<br>";
    
    // verif acces a la route
    if (!canAccessRoute($current_route)) {
        // on recupere le chemin relatif pour faire la redirection
        $path_parts = pathinfo($_SERVER['PHP_SELF']);
        $dir_level = substr_count($path_parts['dirname'], '/');
        
        // on est dans admin ou pages? on adapte la redirection
        if (strpos($_SERVER['PHP_SELF'], '/admin/') !== false) {
            // admin qui essaye d'acceder a une page sans droits
            if (isset($_SESSION['personne']) && $_SESSION['personne'] === false) {
                // connecté mais pas admin -> main.php
                header('Location: ../pages/main.php');
            } else {
                // pas connecté -> login
                header('Location: ../pages/login.php');
            }
        } else {
            // dans le dossier pages ou ailleurs
            if (isset($_SESSION['personne']) && $_SESSION['personne'] === false) {
                // connecté mais pas les droits -> main.php
                header('Location: main.php');
            } else {
                // pas connecté -> login
                header('Location: login.php');
            }
        }
        exit(); // important darreter lexecution du script apres redirection
    }
} 