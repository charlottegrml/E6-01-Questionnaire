<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../BDD/cnx.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: ../LOGIN/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Récupérer tous les thèmes
$themes = $cnx->query("SELECT Id, Nom FROM theme")->fetchAll(PDO::FETCH_ASSOC);

// Vérifier si un thème a été sélectionné
$theme_id = isset($_GET['theme_id']) && is_numeric($_GET['theme_id']) ? (int)$_GET['theme_id'] : null;

// Requête SQL pour récupérer l'historique des réponses de l'utilisateur
$sql = "SELECT ru.DateReponse, q.Libelle AS Question, v.Nom_Valeur AS ReponseChoisie, v.Poids, quest.Libelle AS Questionnaire, t.Nom AS Theme
        FROM reponses_utilisateur ru
        JOIN question q ON ru.QuestionId = q.Id
        JOIN valeur v ON ru.ValeurId = v.Id
        JOIN questionnaire quest ON ru.QuestionnaireId = quest.Id
        JOIN theme t ON quest.ThemeId = t.Id
        WHERE ru.UtilisateurId = :user_id";

$params = [':user_id' => $user_id];

if ($theme_id) {
    $sql .= " AND quest.ThemeId = :theme_id";
    $params[':theme_id'] = $theme_id;
}

$stmt = $cnx->prepare($sql);
$stmt->execute($params);
$historique = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Historique des Réponses</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../CSS/style4.css">
</head>
<body>
    <div class="container">
        <h2 class="my-4">Historique des Réponses</h2>

        <!-- Formulaire de filtre par thème -->
        <form method="GET" class="mb-4">
            <div class="row">
                <div class="col-md-8">
                    <label for="theme_id" class="form-label">Filtrer par Thème :</label>
                    <select name="theme_id" id="theme_id" class="form-select">
                        <option value="">Tous les thèmes</option>
                        <?php foreach ($themes as $theme): ?>
                            <option value="<?= $theme['Id']; ?>" <?= ($theme_id == $theme['Id']) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($theme['Nom']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Filtrer</button>
                </div>
            </div>
        </form>

        <?php if (empty($historique)): ?>
            <p>Aucune réponse enregistrée.</p>
        <?php else: ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Thème</th>
                        <th>Questionnaire</th>
                        <th>Question</th>
                        <th>Réponse donnée</th>
                        <th>Poids</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($historique as $entry): ?>
                        <tr>
                            <td><?= htmlspecialchars($entry['Theme']); ?></td>
                            <td><?= htmlspecialchars($entry['Questionnaire']); ?></td>
                            <td><?= htmlspecialchars($entry['Question']); ?></td>
                            <td><?= htmlspecialchars($entry['ReponseChoisie']); ?></td>
                            <td><?= htmlspecialchars($entry['Poids']); ?> points</td>
                            <td><?= date("d/m/Y H:i", strtotime($entry['DateReponse'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <a href="accueil.php" class="btn btn-primary mt-4">Retour aux questionnaires</a>
    </div>
</body>
</html>
