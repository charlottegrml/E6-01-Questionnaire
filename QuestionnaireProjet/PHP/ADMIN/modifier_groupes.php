<?php
session_start();
include '../BDD/cnx.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== "admin") {
    header("Location: accueil.php");
    exit;
}

$userId = $_GET['id'] ?? null;
if (!$userId) {
    header("Location: utilisateurs.php");
    exit;
}

// Récupérer les groupes existants
$stmt = $cnx->prepare("SELECT * FROM groupes");
$stmt->execute();
$groupes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les groupes actuels de l'utilisateur
$stmt = $cnx->prepare("SELECT GroupeId FROM utilisateurs_groupes WHERE UtilisateurId = ?");
$stmt->execute([$userId]);
$groupesUtilisateur = $stmt->fetchAll(PDO::FETCH_COLUMN);

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier Groupes</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../CSS/style7.css">
</head>
<body>
    <div class="container">
        <h2 class="mt-4">Modifier les groupes</h2>
        <form action="traitement_groupes.php" method="POST">
            <input type="hidden" name="userId" value="<?= $userId; ?>">
            <?php foreach ($groupes as $groupe): ?>
                <div class="form-check" id="grp">
                    <input class="form-check-input" type="checkbox" name="groupes[]" value="<?= $groupe['Id']; ?>"
                        <?= in_array($groupe['Id'], $groupesUtilisateur) ? 'checked' : ''; ?>>
                    <label class="form-check-label"><?= htmlspecialchars($groupe['Nom']); ?></label>
                </div>
            <?php endforeach; ?>
            <button type="submit" class="btn btn-primary mt-3">Enregistrer</button>
            <a href="utilisateurs.php" class="btn btn-secondary mt-3">Annuler</a>
        </form>
    </div>
</body>
</html>
