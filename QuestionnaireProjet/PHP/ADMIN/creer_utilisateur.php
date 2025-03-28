<?php
session_start();
include '../BDD/cnx.php';
include '../BDD/config.php';


if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== "admin") {
    header("Location: accueil.php");
    exit;
}

$role = $_SESSION[("role")];
$user_id = $_SESSION[("user_id")];
if ($role == "utilisateur")
{
    header('Location : ../QUESTIONNAIRE/accueil.php');
    exit;
}
// Récupération des erreurs s'il y en a
$errors = $_SESSION["errors"] ?? [];
unset($_SESSION["errors"]); // Supprimer après affichage

// Récupération des anciennes valeurs pour ne pas les perdre après une erreur
$old = $_SESSION["old"] ?? [];
unset($_SESSION["old"]); // Supprimer après récupération


?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../CSS/style.css">
</head>
<body id="bodyadmin">
    <div class="container" >
        <div id="nvuti">
            <h1 class="mt-5">Créer un nouvel utilisateur :</h1>
        </div>
        
        <?php if (!empty($_SESSION["error"])): ?>
            <div class="alert alert-danger"><?= $_SESSION["error"]; ?></div>
            <?php unset($_SESSION["error"]); ?>
        <?php endif; ?>

        <form action="../LOGIN/traitement_inscription.php" method="POST">
        <input type="hidden" name="admin_creation" value="1">
            <div class="mb-3" id="mb-31">
                <label for="nomUtilisateur" class="form-label">Nom d'utilisateur :</label>
                <input type="text" class="form-control <?= isset($errors['nomUtilisateur']) ? 'is-invalid' : '' ?>" name="nomUtilisateur" value="<?= htmlspecialchars($old['nomUtilisateur'] ?? '') ?>" required>
                <?php if (isset($errors['nomUtilisateur'])): ?>
                    <div class="invalid-feedback"><?= $errors['nomUtilisateur']; ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-3" id="mb-32">
                <label for="email" class="form-label">Email :</label>
                <input type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" name="email" value="<?= htmlspecialchars($old['email'] ?? '') ?>" required>
                <?php if (isset($errors['email'])): ?>
                    <div class="invalid-feedback"><?= $errors['email']; ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-3" id="mb-33">
                <label for="motDePasse" class="form-label">Mot de passe :</label>
                <input type="password" class="form-control <?= isset($errors['motDePasse']) ? 'is-invalid' : '' ?>" name="motDePasse" required>
                <?php if (isset($errors['motDePasse'])): ?>
                    <div class="invalid-feedback"><?= $errors['motDePasse']; ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-3" id="mb-34">
                <label for="nom" class="form-label">Nom :</label>
                <input type="text" class="form-control" name="nom" value="<?= htmlspecialchars($old['nom'] ?? '') ?>" required>
            </div>

            <div class="mb-3" id="mb-35">
                <label for="prenom" class="form-label">Prénom :</label>
                <input type="text" class="form-control" name="prenom" value="<?= htmlspecialchars($old['prenom'] ?? '') ?>" required>
            </div>

            <button id="btninscription" type="submit" class="btn btn-primary">Créer</button>
        </form>
        <a href="adminaccueil.php" class="btn btn-primary" id="btnAnnuler">Annuler</a>
    </div>
</body>
</html>
