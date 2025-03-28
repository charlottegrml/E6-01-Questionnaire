<?php 
session_start();
include '../BDD/cnx.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); 
    exit;
}

// Récupérer l'utilisateur connecté
$user_id = $_SESSION['user_id'];
$stmt = $cnx->prepare("SELECT NomUtilisateur, Nom, Prenom, Role, email FROM utilisateurs WHERE id = :user_id");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

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
    <link rel="stylesheet" href="../../CSS/style2.css">
</head>
<body>
    <div id="bienvenue">
        <h2 id="txtbienvenue">Bienvenue <?= htmlspecialchars($user['Prenom']); ?> !</h2>
    </div>
    
    <div id="deconnecteypnl"> 
        <a href="../PROFIL/modifierProfile.php" class="btn btn-primary">Modifier mon profil</a>
        <a href="../LOGIN/login.php" class="btn btn-primary">Déconnexion</a>
    </div>

    <div id="lstquestionnaire">
        <h1>Liste des Questionnaires :</h1>
    </div> 

    <!-- Liste déroulante des thèmes -->
    <div id="lstthemes">
        <label for="thselect">Filtrer par thème :</label>
        <select id="thselect" class="form-select">
            <option value="all">Tous les thèmes</option>
            <?php foreach ($themes as $theme): ?>
                <option value="<?= $theme['Id']; ?>"><?= htmlspecialchars($theme['Nom']); ?></option>
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
                <tr data-theme="<?= $row['ThemeId']; ?>">
                    <td><?= htmlspecialchars($row['Libelle']); ?></td>
                    <td><?= htmlspecialchars($row['ThemeNom']); ?></td>
                    <td>
                        <a href="question.php?id=<?= $row['Id']; ?>" class="btn btn-primary">Commencer</a>
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
