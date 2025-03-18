<?php
// on lance la session obligatoire
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// les includes de base pour que ca marche
require_once '../config/config.php';
require_once '../config/security.php';
require_once '../config/routes.php';

// verifie si on a acces a cette page 
checkRouteAccess();

try {
    // connexion a la bdd d 
    $pdo = new PDO('mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8', DB_USER, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// pour les messages (erreur/succes)
$message = '';
$messageType = '';

// on recup tous les users de la bdd pour les afficher
$stmt = $pdo->query("SELECT * FROM user_site ORDER BY Grade DESC, nom, prenom");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// on gere les actions de form ajout/modif/suppression
if(isset($_POST['action'])) {
    // verif du token csrf pour eviter les attaques
    if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
        $message = "Erreur de sécurité. Veuillez réessayer.";
        $messageType = "error";
    } else {
        try {
            if($_POST['action'] == 'add_user') {
                // on recupere les données du form
                $prenom = cleanInput($_POST['prenom']);
                $nom = cleanInput($_POST['nom']);
                $email = cleanInput($_POST['email']);
                $password = cleanInput($_POST['password']);
                $confirm_password = cleanInput($_POST['confirm_password']);
                $grade = cleanInput($_POST['grade']);
                
                // check si les mdp correspondent
                if($password !== $confirm_password) {
                    throw new Exception("Les mots de passe ne correspondent pas.");
                }
                
                // verifie si email deja utilisé
                $stmt = $pdo->prepare("SELECT * FROM user_site WHERE email = ?");
                $stmt->execute([$email]);
                if($stmt->rowCount() > 0) {
                    throw new Exception("Cette adresse email est déjà utilisée.");
                }
                
                // hash le mdp pour la securité 
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                // ajoute le nouveau user
                $stmt = $pdo->prepare("INSERT INTO user_site (prenom, nom, email, password, Grade) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$prenom, $nom, $email, $hashed_password, $grade]);
                
                $message = "Utilisateur ajouté avec succès !";
                $messageType = "success";
                
                // refresh la page pour voir le nouveau user
                header("Location: admin_users.php?message=" . urlencode($message) . "&type=" . urlencode($messageType));
                exit;
            } elseif($_POST['action'] == 'update_user') {
                // pour mettre a jour le role
                $user_id = (int)$_POST['user_id'];
                $grade = cleanInput($_POST['grade']);
                
                // update le role de l'user
                $stmt = $pdo->prepare("UPDATE user_site SET Grade = ? WHERE user_id = ?");
                $stmt->execute([$grade, $user_id]);
                
                $message = "Utilisateur mis à jour avec succès !";
                $messageType = "success";
            } elseif($_POST['action'] == 'delete_user') {
                // pour supprimer un compte
                $user_id = (int)$_POST['user_id'];
                
                // regarde si user existe d'abord
                $stmt = $pdo->prepare("SELECT * FROM user_site WHERE user_id = ?");
                $stmt->execute([$user_id]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if($user) {
                    // supprime l'usrrr
                    $stmt = $pdo->prepare("DELETE FROM user_site WHERE user_id = ?");
                    $stmt->execute([$user_id]);
                    
                    $message = "Utilisateur supprimé avec succès !";
                    $messageType = "success";
                    
                    // refresh la page pour voir les changements
                    header("Location: admin_users.php?message=" . urlencode($message) . "&type=" . urlencode($messageType));
                    exit;
                } else {
                    throw new Exception("Utilisateur introuvable.");
                }
            }
        } catch(Exception $e) {
            $message = "Erreur : " . $e->getMessage();
            $messageType = "error";
        }
    }
}

// on recupere le message si redirection pour le garder
if(isset($_GET['message']) && isset($_GET['type'])) {
    $message = cleanInput($_GET['message']);
    $messageType = cleanInput($_GET['type']);
}

// genere un token csrf pour la securité des formulaires
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
    <title>Gestion des Utilisateurs - SneakersAddict</title>
    <link rel="stylesheet" href="../assets/css/admin_users.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="reste">
        <div class="content">
            <div class="admin-container">
                <div class="admin-header">
                    <h1 class="admin-title">Gestion des Utilisateurs</h1>
                    <div class="admin-actions">
                        <button class="btn-add" id="btn-add-user">
                            <i class="fas fa-plus"></i> Ajouter un utilisateur
                        </button>
                    </div>
                </div>
                
                <?php if(!empty($message)): ?>
                    <div class="alert alert-<?php echo $messageType; ?>">
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>
                
                <table class="users-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Email</th>
                            <th>Rôle</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($users as $user): ?>
                            <tr>
                                <td><?php echo $user['user_id']; ?></td>
                                <td><?php echo htmlspecialchars($user['nom']); ?></td>
                                <td><?php echo htmlspecialchars($user['prenom']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td>
                                    <?php if($user['Grade'] == 'admin'): ?>
                                        <span class="badge badge-admin">Admin</span>
                                    <?php else: ?>
                                        <span class="badge badge-client">Client</span>
                                    <?php endif; ?>
                                </td>
                                <td class="actions">
                                    <form method="post" class="update-form">
                                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                        <input type="hidden" name="action" value="update_user">
                                        <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                                        <select name="grade" onchange="this.form.submit()">
                                            <option value="client" <?php echo $user['Grade'] == 'client' ? 'selected' : ''; ?>>Client</option>
                                            <option value="admin" <?php echo $user['Grade'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                                        </select>
                                    </form>
                                    
                                    <form method="post" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ? Cette action est irréversible.');">
                                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                        <input type="hidden" name="action" value="delete_user">
                                        <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                                        <button type="submit" class="btn-delete">Supprimer</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <?php include '../includes/footer.php'; ?>

    </div>
    
    <!-- Modal pour ajouter un utilisateur -->
    <div id="add-user-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Ajouter un nouvel utilisateur</h2>
                <span class="close">&times;</span>
            </div>
            <form method="post" class="modal-form">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                <input type="hidden" name="action" value="add_user">
                
                <div class="form-group">
                    <label for="prenom">Prénom:</label>
                    <input type="text" id="prenom" name="prenom" required>
                </div>
                
                <div class="form-group">
                    <label for="nom">Nom:</label>
                    <input type="text" id="nom" name="nom" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <div class="form-group password-toggle">
                    <label for="password">Mot de passe:</label>
                    <input type="password" id="password" name="password" required>
                    <i class="toggle-password fas fa-eye"></i>
                </div>
                
                <div class="form-group password-toggle">
                    <label for="confirm_password">Confirmer le mot de passe:</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                    <i class="toggle-password fas fa-eye"></i>
                </div>
                
                <div class="form-group">
                    <label for="grade">Rôle:</label>
                    <select id="grade" name="grade" required>
                        <option value="client">Client</option>
                        <option value="admin">Administrateur</option>
                    </select>
                </div>
                
                <div class="modal-footer">
                    <button type="submit" class="btn-add">Ajouter</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Gestion du modal
            const modal = document.getElementById('add-user-modal');
            const btnAdd = document.getElementById('btn-add-user');
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
            
            // Gestion des toggles de mot de passe
            const toggles = document.querySelectorAll('.toggle-password');
            
            toggles.forEach(toggle => {
                toggle.addEventListener('click', function() {
                    const input = this.previousElementSibling;
                    
                    if (input.type === 'password') {
                        input.type = 'text';
                        this.classList.remove('fa-eye');
                        this.classList.add('fa-eye-slash');
                    } else {
                        input.type = 'password';
                        this.classList.remove('fa-eye-slash');
                        this.classList.add('fa-eye');
                    }
                });
            });
        });
    </script>
</body>
</html> 