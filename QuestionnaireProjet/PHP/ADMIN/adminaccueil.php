<?php 
session_start();
include '../BDD/cnx.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); 
    exit;
}

// Récupérer l'utilisateur connecté
$user_id = $_SESSION['user_id'];
$stmt = $cnx->prepare("SELECT NomUtilisateur, Nom, Prenom, Role, Email FROM utilisateurs WHERE id = :user_id");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$role = $user['Role'];

if ($role == "utilisateur")
{
    header('Location : ../QUESTIONNAIRE/accueil.php');
    exit;
}

$_SESSION['user_id'] = $user_id;
$_SESSION[("role")]=$role;
// Récupérer les thèmes
$themeStmt = $cnx->prepare("SELECT Id, Nom FROM theme");
$themeStmt->execute();
$themes = $themeStmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer tous les questionnaires
$questionnaireStmt = $cnx->prepare("SELECT q.Id, q.Libelle, t.Nom AS ThemeNom, q.ThemeId FROM questionnaire q INNER JOIN theme t ON q.ThemeId = t.Id");
$questionnaireStmt->execute();
$questionnaires = $questionnaireStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../CSS/style5.css">
</head>
<body>

<!-- Barre de navigation -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="adminaccueil.php">Bienvenue <?= htmlspecialchars($user['Prenom']); ?> !</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="../PROFIL/modifierProfile.php">Modifier mon profil</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../QUESTIONNAIRE/historique.php">Mon historique</a>
                </li>
                <?php if ($role === "admin"): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="utilisateurs.php">Utilisateurs</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="creer_utilisateur.php">Créer un utilisateur</a>
                    </li>
                <?php endif; ?>
                <li class="nav-item">
                    <a class="nav-link btn btn-danger" href="../LOGIN/login.php">Déconnexion</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div id="lstquestionnaire">
    <h1>Liste des Questionnaires :</h1>
</div> 

<!-- Liste déroulante des thèmes -->
<div id="lstthemes">
    <select id="thselect" class="form-select">
        <option value="all">Tous les thèmes</option>
        <?php foreach ($themes as $theme): ?>
            <option value="<?= htmlspecialchars($theme['Id']); ?>"><?= htmlspecialchars($theme['Nom']); ?></option>
        <?php endforeach; ?>
    </select>
</div>

<!-- Tableau des questionnaires -->
<table class="table table-striped">
    <thead>
        <tr>
            <th>Nom</th>
            <th>Thème</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody id="questionnaireTable">
        <?php foreach ($questionnaires as $row): ?>
            <tr data-theme="<?= htmlspecialchars($row['ThemeId']); ?>">
                <td><?= htmlspecialchars($row['Libelle']); ?></td>
                <td><?= htmlspecialchars($row['ThemeNom']); ?></td>
                <td>
                    <a href="../QUESTIONNAIRE/question.php?id=<?= $row['Id']; ?>" class="btn btn-primary">Commencer</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<script>
    document.getElementById("thselect").addEventListener("change", function() {
        let selectedTheme = this.value;
        let rows = document.querySelectorAll("#questionnaireTable tr");

        rows.forEach(row => {
            let themeId = row.getAttribute("data-theme");

            if (selectedTheme === "all" || themeId === selectedTheme) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        });
    });
</script>

</body>
</html>
