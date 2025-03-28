<?php
session_start();
include '../BDD/cnx.php'; // Connexion à la BDD

if (!isset($_GET['token'])) {
    die("Token invalide.");
}

$token = $_GET['token'];

// Vérifier si le token existe et n'a pas expiré
$sql = "SELECT * FROM utilisateurs WHERE token = :token";
$stmt = $cnx->prepare($sql);
$stmt->execute(['token' => $token]);
$user = $stmt->fetch();

if (!$user) {
    die("Token invalide.");
}

// Vérifier si le token est encore valide (moins de 3 minutes)
$token_creation = strtotime($user['token_created_at']);
$now = time();

if (($now - $token_creation) > 180) { // 180 secondes = 3 minutes
    die("Token expiré.");
}

// Si l'utilisateur soumet le nouveau mot de passe
if (isset($_POST['submit'])) {
    $motDePasse = $_POST['motDePasse'];

    // Vérifier la complexité du mot de passe
    if (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\W).{10,}$/", $motDePasse)) {
        $_SESSION["error"] = "Le mot de passe doit contenir au moins 10 caractères, une minuscule, une majuscule et un caractère spécial.";
    } else {
        // Hasher le mot de passe avant de le stocker
        $hashedPassword = password_hash($motDePasse, PASSWORD_BCRYPT);

        // Mettre à jour le mot de passe en BDD et supprimer le token
        $sql = "UPDATE utilisateurs SET MotDePasse = :motDePasse, token = NULL, token_created_at = NULL WHERE Id = :id";
        $stmt = $cnx->prepare($sql);
        $stmt->execute([
            'motDePasse' => $hashedPassword,
            'id' => $user['Id']
        ]);

        // Redirection après changement
        $_SESSION["success"] = "Mot de passe mis à jour avec succès.";
        header("Location: login.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../CSS/style.css">
    <title>Réinitialisation du mot de passe</title>
</head>
<body>
    <div class="container">
        <h1 class="mt-5">Réinitialiser votre mot de passe</h1>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label for="motDePasse">Nouveau mot de passe :</label>
                <input type="password" name="motDePasse" required>
            </div>
            <button type="submit" class="btn btn-primary" name="submit">Modifier le mot de passe</button>
        </form>
    </div>
</body>
</html>
