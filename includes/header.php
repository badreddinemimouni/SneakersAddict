<?php
$a=session_id(); if(empty($a)) session_start();

// calcul nbr total d'articles dans le panier
$nombre_articles = 0;
$total_panier = 0;
if(isset($_SESSION['panier']) && !empty($_SESSION['panier'])) {
    foreach($_SESSION['panier'] as $produit) {
        $nombre_articles += $produit['quantite'];
        $total_panier += $produit['prix'] * $produit['quantite'];
    }
}
?>

<link href="../assets/css/style_optimized.css" rel="stylesheet">

<div id="bandeau">
    <div class="header-content">
        <div class="logo-container">
            <a href="../pages/main.php"><img src="../assets/images/LOGO-TEST.png" class="logo" alt="logo"></a>
            <a href="../pages/main.php"><h1 class="site-title">SneakersAddict</h1></a>
        </div>
        
        <div id="petit_boutons">
            <a href="../pages/main.php">Accueil</a>
            <?php
            if($_SESSION['personne']==false){
                echo "<a href='../pages/Produits.php'>Produits</a>";
                echo "<a href='../pages/Contact.php'>Contact</a>";
            }
            ?>
            <?php if($_SESSION['admin']==true && $_SESSION['client']==false){ 
                echo "<a href='../admin/stock.php'>Stock</a>";
                echo "<a href='../admin/admin_users.php'>Utilisateurs</a>";
            }?>
            
            <div class="header-right">
                <!-- Conteneur du panier -->
                <div class="panier-wrapper">
                    <a href="javascript:void(0);" id="panier-icon">
                        <img src="../assets/images/PANIER.png" alt="panier" id="petit_panier">
                        <?php if($nombre_articles > 0): ?>
                            <span class="panier-badge"><?php echo $nombre_articles; ?></span>
                        <?php endif; ?>
                    </a>
                    
                    <!-- Mini-panier -->
                    <div id="mini-panier">
                        <h3>Votre Panier</h3>
                        <?php if(empty($_SESSION['panier'])): ?>
                            <p class="panier-vide-message">Votre panier est vide</p>
                        <?php else: ?>
                            <ul class="mini-panier-items">
                                <?php foreach($_SESSION['panier'] as $id_produit => $produit): ?>
                                    <li>
                                        <img src="<?php echo htmlspecialchars($produit['image']); ?>" alt="<?php echo htmlspecialchars($produit['nom']); ?>">
                                        <div class="mini-panier-info">
                                            <p class="mini-panier-nom"><?php echo htmlspecialchars($produit['nom']); ?></p>
                                            <p class="mini-panier-details">
                                                Pointure: <?php echo htmlspecialchars($produit['pointure']); ?> | 
                                                Qté: <?php echo $produit['quantite']; ?> | 
                                                <?php echo $produit['prix'] * $produit['quantite']; ?> €
                                            </p>
                                        </div>
                                        <a href="../pages/Panier.php?action=supprimer&id=<?php echo $id_produit; ?>" class="mini-panier-supprimer">×</a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                            <div class="mini-panier-total">
                                <p>Total: <?php echo number_format($total_panier, 2, ',', ' '); ?> €</p>
                            </div>
                            <div class="mini-panier-actions">
                                <a href="../pages/Panier.php" class="mini-panier-btn">Voir le panier</a>
                                <a href="../pages/Panier.php?payer=1" class="mini-panier-btn mini-panier-payer">Payer</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <?php if($_SESSION['admin']==true || $_SESSION['client']==true){
                    echo "<a href='../pages/deconnexion.php' class='bouton bouton-right'>Se deconnecter</a>";
                }
                else{
                    echo "<a href='../pages/login.php' class='bouton bouton-right'>Se connecter</a>";
                }
                ?>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // recup les éléments
    var panierIcon = document.getElementById('panier-icon');
    var miniPanier = document.getElementById('mini-panier');
    
    // affich / masquer le mini-panier au clic sur l'icône
    panierIcon.addEventListener('click', function(e) {
        e.preventDefault();
        if (miniPanier.style.display === 'block') {
            miniPanier.style.display = 'none';
        } else {
            miniPanier.style.display = 'block';
        }
    });
    
    // Fermer le mini-panier si on clique ailleurs sur la page
    document.addEventListener('click', function(e) {
        if (!panierIcon.contains(e.target) && !miniPanier.contains(e.target)) {
            miniPanier.style.display = 'none';
        }
    });
});

// Fonction pour afficher le mini-panier après ajout d'un produit
function afficherMiniPanier() {
    document.getElementById('mini-panier').style.display = 'block';
    
    // Masquer automatiquement après 5 secondes
    setTimeout(function() {
        document.getElementById('mini-panier').style.display = 'none';
    }, 5000);
}

// Si on vient d'ajouter un produit, afficher le mini-panier
<?php if(isset($_GET['ajout']) && $_GET['ajout'] == 1): ?>
document.addEventListener('DOMContentLoaded', function() {
    afficherMiniPanier();
});
<?php endif; ?>
</script> 