<?php 
session_start();
include '../BDD/cnx.php';
include '../BDD/config.php';

// Vérification de la session pour l'utilisateur connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); 
    exit;
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['Role'];

if($role == "admin"){
    $accueil = "../ADMIN/adminaccueil.php";
}
else if ($role == "utilisateur"){
    $accueil = "accueil.php";
}

// Récupération des erreurs s'il y en a
$errors = $_SESSION["errors"] ?? [];
unset($_SESSION["errors"]); // Supprimer après affichage

// Récupération des anciennes valeurs pour ne pas les perdre après une erreur
$old = $_SESSION["old"] ?? [];
unset($_SESSION["old"]); // Supprimer après récupération


// Récupération des informations de l'utilisateur depuis la base de données
$stmt = $cnx->prepare("SELECT NomUtilisateur, Nom, Prenom, Role, email FROM utilisateurs WHERE id = :user_id");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier mon profil</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../CSS/style.css">
</head>
<body>
    <h1>Modifier mon profil :</h1>
    
    <form action="traitement_modification.php" method="POST">
        <div class="mb-3">
            <label for="nomUtilisateur" class="form-label">Nom d'utilisateur :</label>
            <input type="text" class="form-control <?= isset($errors['nomUtilisateur']) ? 'is-invalid' : '' ?>" 
                   name="nomUtilisateur" value="<?= htmlspecialchars($old['nomUtilisateur'] ?? $user['NomUtilisateur']) ?>" required>
            <?php if (isset($errors['nomUtilisateur'])): ?>
                <div class="invalid-feedback"><?= $errors['nomUtilisateur']; ?></div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email :</label>
            <input type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" 
                   name="email" value="<?= htmlspecialchars(decryptEmail($old['email'] ?? $user['email'])) ?>" required>
            <?php if (isset($errors['email'])): ?>
                <div class="invalid-feedback"><?= $errors['email']; ?></div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="motDePasse" class="form-label">Mot de passe :</label>
            <input type="password" class="form-control <?= isset($errors['motDePasse']) ? 'is-invalid' : '' ?>" 
                   name="motDePasse" placeholder="Laissez vide pour ne pas modifier">
            <?php if (isset($errors['motDePasse'])): ?>
                <div class="invalid-feedback"><?= $errors['motDePasse']; ?></div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="nom" class="form-label">Nom :</label>
            <input type="text" class="form-control" name="nom" value="<?= htmlspecialchars($old['nom'] ?? $user['Nom']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="prenom" class="form-label">Prénom :</label>
            <input type="text" class="form-control" name="prenom" value="<?= htmlspecialchars($old['prenom'] ?? $user['Prenom']) ?>" required>
        </div>

        <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
    </form>
</body>
</html>
