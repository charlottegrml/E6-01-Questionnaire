<?php 
session_start();
include '../BDD/cnx.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== "admin") {
    header("Location: accueil.php");
    exit;
}

try {
    // Récupérer la liste des utilisateurs avec leurs groupes
    $stmt = $cnx->prepare("SELECT u.Id, u.NomUtilisateur, u.Nom, u.Prenom, u.Role, 
        COALESCE(GROUP_CONCAT(g.Nom SEPARATOR ', '), 'Aucun') AS Groupes
        FROM utilisateurs u
        LEFT JOIN utilisateurs_groupes ug ON u.Id = ug.UtilisateurId
        LEFT JOIN groupes g ON ug.GroupeId = g.Id
        GROUP BY u.Id, u.NomUtilisateur, u.Nom, u.Prenom, u.Role
    ");

    $stmt->execute();
    $utilisateurs = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erreur SQL : " . $e->getMessage()); // Affiche une erreur SQL si la requête échoue
}

if (!$utilisateurs) {
    $utilisateurs = []; // Empêche les erreurs dans la boucle foreach
}

?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des utilisateurs</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../CSS/style6.css">
</head>
<body>
    <div id="accueil">
        <a href="adminaccueil.php" class="btn btn-primary">Revenir à l'accueil</a>
    </div>
    <div class="container">
        <h2 class="my-4">Liste des utilisateurs</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nom d'utilisateur</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Groupe</th>
                    <th>Modifier le Groupe</th>
                    <th>Changer de statut</th>
                    <th>Historique</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($utilisateurs as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['NomUtilisateur']); ?></td>
                        <td><?= htmlspecialchars($user['Nom']); ?></td>
                        <td><?= htmlspecialchars($user['Prenom']); ?></td>
                        <td><?= htmlspecialchars($user['Groupes'] ?? 'Aucun'); ?></td>
                        <td>
                            <a href="modifier_groupes.php?id=<?= $user['Id']; ?>" class="btn btn-warning">Modifier Groupes</a>
                        </td>
                        <td>
                            <?php if ($user['Id'] !== $_SESSION['user_id']): // Empêche un admin de changer son propre rôle ?>
                                <form action="../PROFIL/changer_role.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="user_id" value="<?= $user['Id']; ?>">
                                    <input type="hidden" name="new_role" value="<?= $user['Role'] === 'admin' ? 'utilisateur' : 'admin'; ?>">
                                    <button type="submit" class="btn btn-warning">
                                        <?= $user['Role'] === 'admin' ? 'Admin' : 'Utilisateur' ?>
                                    </button>
                                </form>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="historique_utilisateur.php?id=<?= $user['Id']; ?>" class="btn btn-info">Voir historique</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
