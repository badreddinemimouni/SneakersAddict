<?php


// ce fichier contient toutes les fonctions de securité
// a pas toucher sinon tout va buggér 


function cleanInput($data) {
    // Supprime les espaces inutiles
    $data = trim($data);
    // Supprime les antislashs
    $data = stripslashes($data);
    // Convertit les caractères spéciaux en entités HTML
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}


function isValidRoute($route, $allowed_routes) {
    return in_array($route, $allowed_routes);
}


function redirectWithError($page, $error) {
    if (strpos($page, '../') !== 0) {
        $page = '../pages/' . $page;
    }
    // on redirige avec le message d'erreur en paramètre get
    header("Location: $page?error=" . urlencode($error));
    exit();
}

function isLoggedIn($role = null) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if ($role === 'admin') {
        return isset($_SESSION['admin']) && $_SESSION['admin'] === true;
    } elseif ($role === 'client') {
        return isset($_SESSION['client']) && $_SESSION['client'] === true;
    } else {
        return isset($_SESSION['personne']) && $_SESSION['personne'] === false;
    }
}

function generateCSRFToken() {
    // on verifie si la session est démarrée sinon ca bug
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // on genere un nouveau token si y'en a pas deja un
    // bizarrement ca marche pas si on fait un nouveau a chaque fois
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // token random de 64 caractères
    }
    
    return $_SESSION['csrf_token'];
}


function validateCSRFToken($token) {
    // on verifie si la session est démarrée
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // check si le token est le même que celui stocké en session
    // si non c'est peut-être une attaque csrf ou alors le token a expiré
    if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
        return false;
    }
    
    return true;
}


function logoutUser() {
    // on vide la session
    $_SESSION = [];
    
    // on detruit le cookie de session si y'en a un
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }
    
    // on detruit completement la session
    session_destroy();
}


function escapeSQLParam($pdo, $param) {
    // si c'est un tableau, on applique la fonction a chaque élément
    if (is_array($param)) {
        return array_map(function($item) use ($pdo) {
            return escapeSQLParam($pdo, $item);
        }, $param);
    }
    
    // on utilise la fonction quote de pdo (c'est mieux que addslashes)
    if (is_string($param)) {
        return $pdo->quote($param);
    }
    
    // si c'est un int on le cast juste au cas ou
    if (is_numeric($param)) {
        return (int)$param;
    }
    
    // sinon on le laisse tel quel (faut faire gaffe quand meme)
    return $param;
}

?> 