<?php 
// active l'affichage des erreurs pour debug (à enlever )
error_reporting(E_ALL);
ini_set('display_errors', 1);

// demarrage de la session comme ca on peut stocker des trucs
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// on prend tous les fichiers de config dont on a besoin
require_once '../config/config.php';
require_once '../config/security.php';
require_once '../config/routes.php';

// on vérifie si la route est accessible
checkRouteAccess();

try {
    $pdo = new PDO('mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8', DB_USER, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// pour les messages a afficher genre erreur ou succes
$message = '';
$messageType = '';

// on va chercher tous les produits
$stmt = $pdo->query("SELECT * FROM stock ORDER BY nom");
$produits = $stmt->fetchAll(PDO::FETCH_ASSOC);

// et ttes les pointures
$stmt = $pdo->query("SELECT * FROM size ORDER BY size");
$pointures = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ici on traite qd l'utilisateur fait qqch comme ajouter/modif/supprimer un truc
if(isset($_POST['action'])) {
    // on verifie le token csrf pour la securite
    if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
        $message = "Erreur de sécurité. Veuillez réessayer.";
        $messageType = "error";
    } else {
        try {
            if($_POST['action'] == 'update_stock') {
                // on recupere les valeurs du formulaire
                $produit_id = (int)$_POST['produit_id'];
                $pointure_id = (int)$_POST['pointure_id'];
                $quantite = (int)$_POST['quantite'];
                $prix = (float)$_POST['prix'];
                
                // on regarde si le stock existe deja
                $stmt = $pdo->prepare("SELECT * FROM stock_size WHERE stock_id = ? AND size_id = ?");
                $stmt->execute([$produit_id, $pointure_id]);
                $stock_size = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if($stock_size) {
                    // update le stock qui existe
                    $stmt = $pdo->prepare("UPDATE stock_size SET amount = ? WHERE id = ?");
                    $stmt->execute([$quantite, $stock_size['id']]);
                    
                    // et le prix aussi
                    $stmt = $pdo->prepare("UPDATE stock SET prix = ? WHERE id = ?");
                    $stmt->execute([$prix, $produit_id]);
                    
                    $message = "Stock mis à jour avec succès !";
                    $messageType = "success";
                } else {
                    // nouveau stock si pas existant
                    $stmt = $pdo->prepare("INSERT INTO stock_size (stock_id, size_id, amount) VALUES (?, ?, ?)");
                    $stmt->execute([$produit_id, $pointure_id, $quantite]);
                    
                    // pareil pour le prix
                    $stmt = $pdo->prepare("UPDATE stock SET prix = ? WHERE id = ?");
                    $stmt->execute([$prix, $produit_id]);
                    
                    $message = "Nouveau stock ajouté avec succès !";
                    $messageType = "success";
                }
            } elseif($_POST['action'] == 'add_product') {
                // on prend les infos du form
                $nom = cleanInput($_POST['nom']);
                $prix = (float)$_POST['prix'];
                $couleur = cleanInput($_POST['couleur']);
                $image_path = null;
                
                // gestion de l'upload d'image
                // ca m'a pris du temps cette partie 
                if(isset($_FILES['image_file']) && $_FILES['image_file']['error'] == 0) {
                    // max 2Mo pour pas surcharger le serveur
                    if($_FILES['image_file']['size'] <= 2000000) {
                        // securité pour pas uploader n'importe quoi
                        $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
                        $file_info = getimagesize($_FILES['image_file']['tmp_name']);
                        
                        if($file_info && in_array($file_info['mime'], $allowed_types)) {
                            // nom unique pour eviter les conflits
                            $extension = pathinfo($_FILES['image_file']['name'], PATHINFO_EXTENSION);
                            $new_filename = uniqid('sneaker_') . '.' . $extension;
                            $upload_path = '../assets/images/' . $new_filename;
                            
                            // on deplace le fichier temporaire
                            if(move_uploaded_file($_FILES['image_file']['tmp_name'], $upload_path)) {
                                $image_path = $upload_path;
                            } else {
                                throw new Exception("Erreur lors du téléchargement de l'image.");
                            }
                        } else {
                            throw new Exception("Le format de l'image n'est pas accepté. Utilisez JPG, PNG ou WEBP.");
                        }
                    } else {
                        throw new Exception("L'image est trop volumineuse. Taille maximale: 2 Mo.");
                    }
                }
                
                // on insere le produit dans la bdd
                $stmt = $pdo->prepare("INSERT INTO stock (nom, prix, couleur, image) VALUES (?, ?, ?, ?)");
                $stmt->execute([$nom, $prix, $couleur, $image_path]);
                
                $message = "Nouveau produit ajouté avec succès !";
                $messageType = "success";
                
                // on recharge la page pour voir le nouveau produit
                header("Location: stock.php?message=" . urlencode($message) . "&type=" . urlencode($messageType));
                exit;
            } elseif($_POST['action'] == 'delete_product') {
                $produit_id = (int)$_POST['produit_id'];
                
                // regarde si le produit existe
                $stmt = $pdo->prepare("SELECT * FROM stock WHERE id = ?");
                $stmt->execute([$produit_id]);
                $produit = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if($produit) {
                    // on supprime d'abord les tailles
                    // sinon ca marche pas a cause des relations
                    $stmt = $pdo->prepare("DELETE FROM stock_size WHERE stock_id = ?");
                    $stmt->execute([$produit_id]);
                    
                    // puis le produit lui-meme
                    $stmt = $pdo->prepare("DELETE FROM stock WHERE id = ?");
                    $stmt->execute([$produit_id]);
                    
                    // si le produit avait une image on la supprime aussi
                    // sauf si c'est l'image par defaut
                    if($produit['image'] && file_exists($produit['image']) && $produit['image'] != '../assets/images/default.webp') {
                        unlink($produit['image']);
                    }
                    
                    $message = "Produit supprimé avec succès !";
                    $messageType = "success";
                    
                    // refresh la page
                    header("Location: stock.php?message=" . urlencode($message) . "&type=" . urlencode($messageType));
                    exit;
                } else {
                    throw new Exception("Produit introuvable.");
                }
            }
        } catch(Exception $e) {
            $message = "Erreur : " . $e->getMessage();
            $messageType = "error";
        }
    }
}

// recupere le message de l'url si redirect
if(isset($_GET['message']) && isset($_GET['type'])) {
    $message = cleanInput($_GET['message']);
    $messageType = cleanInput($_GET['type']);
}

// on cree un jeton CSRF pour la sécurité des forms
$csrf_token = generateCSRFToken();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../assets/css/style_optimized.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <link rel="shortcut icon" href="../assets/images/SNEAKERSADDICT.png" type="image/x-icon">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab&display=swap" rel="stylesheet">    
    <title>Gestion des Stocks - SneakersAddict</title>
    <link rel="stylesheet" href="../assets/css/admin_stock.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="reste">
        <div class="content">
            <div class="stock-container">
                <div class="stock-header">
                    <h1 class="stock-title">Gestion des Stocks</h1>
                    <div class="stock-actions">
                        <button class="btn-add" id="btn-add-product">
                            <i class="fas fa-plus"></i> Ajouter un produit
                        </button>
                    </div>
                </div>
                
                <?php if(!empty($message)): ?>
                    <div class="alert alert-<?php echo $messageType; ?>">
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>
                
                <div class="tabs">
                    <div class="tab active" data-tab="grid">Vue Grille</div>
                    <div class="tab" data-tab="table">Vue Tableau</div>
                </div>

                <div class="tab-content active" id="grid-view">
                    <div class="stock-grid">
                        <?php foreach($produits as $produit): ?>
                            <div class="stock-card">
                                <div class="stock-card-image">
                                    <img src="<?php echo htmlspecialchars($produit['image'] ?: '../assets/images/default.webp'); ?>" alt="<?php echo htmlspecialchars($produit['nom']); ?>">
                                </div>
                                <div class="stock-card-content">
                                    <h3 class="stock-card-title"><?php echo htmlspecialchars($produit['nom']); ?></h3>
                                    <div class="stock-card-info">
                                        <span>Prix: <?php echo number_format($produit['prix'], 2, ',', ' '); ?> €</span>
                                        <span>Couleur: <?php echo htmlspecialchars($produit['couleur'] ?: 'Non spécifiée'); ?></span>
                                    </div>
                                    
                                    <form class="stock-form" method="post">
                                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                        <input type="hidden" name="action" value="update_stock">
                                        <input type="hidden" name="produit_id" value="<?php echo $produit['id']; ?>">
                                        <input type="hidden" name="prix" value="<?php echo $produit['prix']; ?>">
                                        
                                        <div class="form-group">
                                            <label for="pointure-<?php echo $produit['id']; ?>">Pointure:</label>
                                            <select id="pointure-<?php echo $produit['id']; ?>" name="pointure_id" required>
                                                <option value="">Sélectionner une pointure</option>
                                                <?php foreach($pointures as $pointure): ?>
                                                    <option value="<?php echo $pointure['id']; ?>"><?php echo $pointure['size']; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="quantite-<?php echo $produit['id']; ?>">Quantité:</label>
                                            <input type="number" id="quantite-<?php echo $produit['id']; ?>" name="quantite" min="0" required>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="prix-<?php echo $produit['id']; ?>">Prix (€):</label>
                                            <input type="number" id="prix-<?php echo $produit['id']; ?>" name="prix" min="0" step="0.01" value="<?php echo $produit['prix']; ?>" required>
                                        </div>
                                        
                                        <button type="submit" class="btn-update">Mettre à jour</button>
                                    </form>
                                    
                                    <div class="stock-card-actions">
                                        <form method="post" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ? Cette action est irréversible.');">
                                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                            <input type="hidden" name="action" value="delete_product">
                                            <input type="hidden" name="produit_id" value="<?php echo $produit['id']; ?>">
                                            <button type="submit" class="btn-delete">Supprimer le produit</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div class="tab-content" id="table-view">
                    <table class="stock-table">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Produit</th>
                                <th>Prix</th>
                                <th>Couleur</th>
                                <th>Stocks par pointure</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($produits as $produit): ?>
                                <tr>
                                    <td><img src="<?php echo htmlspecialchars($produit['image'] ?: '../assets/images/default.webp'); ?>" alt="<?php echo htmlspecialchars($produit['nom']); ?>"></td>
                                    <td><?php echo htmlspecialchars($produit['nom']); ?></td>
                                    <td><?php echo number_format($produit['prix'], 2, ',', ' '); ?> €</td>
                                    <td><?php echo htmlspecialchars($produit['couleur'] ?: 'Non spécifiée'); ?></td>
                                    <td>
                                        <?php
                                        // on recupere les stocks par pointure pour ce produit
                                        $stmt = $pdo->prepare("
                                            SELECT s.size, ss.amount 
                                            FROM stock_size ss
                                            JOIN size s ON ss.size_id = s.id
                                            WHERE ss.stock_id = ?
                                            ORDER BY s.size
                                        ");
                                        $stmt->execute([$produit['id']]);
                                        $stocks = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                        
                                        if(count($stocks) > 0) {
                                            foreach($stocks as $stock) {
                                                echo "<div>Pointure " . $stock['size'] . ": " . $stock['amount'] . " pcs</div>";
                                            }
                                        } else {
                                            echo "Aucun stock défini";
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <form method="post" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ? Cette action est irréversible.');">
                                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                            <input type="hidden" name="action" value="delete_product">
                                            <input type="hidden" name="produit_id" value="<?php echo $produit['id']; ?>">
                                            <button type="submit" class="btn-delete">Supprimer</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div> 
        </div>    

        <?php include '../includes/footer.php'; ?>
    </div>
    
    <!-- Modal pour ajouter un produit -->
    <div id="add-product-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Ajouter un nouveau produit</h2>
                <span class="close">&times;</span>
            </div>
            <form method="post" class="modal-form" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                <input type="hidden" name="action" value="add_product">
                
                <div>
                    <label for="nom">Nom du produit:</label>
                    <input type="text" id="nom" name="nom" required>
                </div>
                
                <div>
                    <label for="prix">Prix (€):</label>
                    <input type="number" id="prix" name="prix" min="0" step="0.01" required>
                </div>
                
                <div>
                    <label for="couleur">Couleur:</label>
                    <input type="text" id="couleur" name="couleur">
                </div>
                
                <div>
                    <label for="image_file">Image du produit:</label>
                    <input type="file" id="image_file" name="image_file" accept="image/*">
                    <p class="form-help">Formats acceptés: JPG, PNG, WEBP. Taille max: 2 Mo</p>
                </div>
                
                <div class="modal-footer">
                    <button type="submit" class="btn-add">Ajouter</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        // Gestion des onglets
        document.addEventListener('DOMContentLoaded', function() {
            const tabs = document.querySelectorAll('.tab');
            const tabContents = document.querySelectorAll('.tab-content');
            
            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    // Retirer la classe active de tous les onglets
                    tabs.forEach(t => t.classList.remove('active'));
                    // Ajouter la classe active à l'onglet cliqué
                    this.classList.add('active');
                    
                    // Masquer tous les contenus d'onglet
                    tabContents.forEach(content => content.classList.remove('active'));
                    // Afficher le contenu correspondant à l'onglet cliqué
                    const tabId = this.getAttribute('data-tab');
                    document.getElementById(tabId + '-view').classList.add('active');
                });
            });
            
            // Gestion du modal
            const modal = document.getElementById('add-product-modal');
            const btnAdd = document.getElementById('btn-add-product');
            const closeBtn = document.querySelector('.close');
            
            btnAdd.addEventListener('click', function() {
                modal.style.display = 'block';
            });
            
            closeBtn.addEventListener('click', function() {
                modal.style.display = 'none';
            });
            
            window.addEventListener('click', function(event) {
                if (event.target == modal) {
                    modal.style.display = 'none';
                }
            });
            
            // Charger les stocks existants pour chaque produit
            <?php foreach($produits as $produit): ?>
                const selectPointure<?php echo $produit['id']; ?> = document.getElementById('pointure-<?php echo $produit['id']; ?>');
                const inputQuantite<?php echo $produit['id']; ?> = document.getElementById('quantite-<?php echo $produit['id']; ?>');
                
                if(selectPointure<?php echo $produit['id']; ?>) {
                    selectPointure<?php echo $produit['id']; ?>.addEventListener('change', function() {
                        const produitId = <?php echo $produit['id']; ?>;
                        const pointureId = this.value;
                        
                        if(pointureId) {
                            // Récupérer la quantité en stock pour produit/pointure
                            fetch(`get_stock.php?produit_id=${produitId}&pointure_id=${pointureId}`)
                                .then(response => response.json())
                                .then(data => {
                                    if(data.success && inputQuantite<?php echo $produit['id']; ?>) {
                                        inputQuantite<?php echo $produit['id']; ?>.value = data.amount;
                                    } else {
                                        inputQuantite<?php echo $produit['id']; ?>.value = 0;
                                    }
                                })
                                .catch(error => {
                                    console.error('Erreur:', error);
                                    inputQuantite<?php echo $produit['id']; ?>.value = 0;
                                });
                        }
                    });
                }
            <?php endforeach; ?>
        });
    </script>
</body>
</html>