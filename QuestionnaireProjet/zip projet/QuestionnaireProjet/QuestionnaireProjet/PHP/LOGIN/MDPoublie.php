<?php
session_start(); // Démarrer la session
include '../BDD/cnx.php'; // Connexion à la BDD

if (isset($_POST['submit'])) {
    $input = $_POST['emailOrPassword'];

    // Vérifier si l'utilisateur existe
    $sql = "SELECT * FROM utilisateurs WHERE email = :input OR NomUtilisateur = :input";
    $stmt = $cnx->prepare($sql);
    $stmt->execute(['input' => $input]);
    $user = $stmt->fetch();

    if ($user) {
        // Générer un token sécurisé
        $token = bin2hex(random_bytes(32));
        $token_created_at = date('Y-m-d H:i:s'); // Date actuelle

        // Stocker le token en BDD
        $sql = "UPDATE utilisateurs SET token = :token, token_created_at = :token_created_at WHERE Id = :id";
        $stmt = $cnx->prepare($sql);
        $stmt->execute([
            'token' => $token,
            'token_created_at' => $token_created_at,
            'id' => $user['Id']
        ]);

        // Rediriger l'utilisateur vers la page de réinitialisation avec le token dans l'URL
        header("Location: reinitialisationMDP.php?token=$token");
        exit();
    } else {
        $_SESSION['error'] = "Aucun utilisateur trouvé avec ces informations.";
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
    <title>Mot de passe oublié</title>
</head>
<body>
    <!-- <div class="container" id="ktn"> -->
        <h1 class="mt-5" id="mdp">Réinitialiser mon mot de passe</h1>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-2" id="mdp2" >
                <label for="emailOrPassword">Email ou Nom d'utilisateur :</label>
                <input type="text" name="emailOrPassword" required>
                <button type="submit" class="btn btn-primary" name="submit">Envoyer</button>
            </div>
        </form>
    <!-- </div> -->
</body>
</html>
