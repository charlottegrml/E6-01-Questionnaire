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
$role = $_SESSION['Role'];

// Récupérer tous les thèmes
$themes = $cnx->query("SELECT Id, Nom FROM theme")->fetchAll(PDO::FETCH_ASSOC);

// Vérifier si un thème a été sélectionné
$theme_id = isset($_GET['theme_id']) && is_numeric($_GET['theme_id']) ? (int)$_GET['theme_id'] : null;

// Requête SQL pour récupérer l'historique des réponses de l'utilisateur
$sql = "SELECT ru.DateReponse, q.Libelle AS Question, v.Nom_Valeur AS ReponseChoisie, 
        v.Poids, quest.Libelle AS Questionnaire, t.Nom AS Theme
        FROM reponses_utilisateur ru
        INNER JOIN question q ON ru.QuestionId = q.Id
        INNER JOIN valeur v ON ru.ValeurId = v.Id
        INNER JOIN questionnaire quest ON ru.QuestionnaireId = quest.Id
        INNER JOIN theme t ON quest.ThemeId = t.Id
        WHERE ru.UtilisateurId = :user_id";


$params = [':user_id' => $user_id];

if ($theme_id) {
    $sql .= " AND quest.ThemeId = :theme_id";
    $params[':theme_id'] = $theme_id;
}

$sql .= " ORDER BY ru.DateReponse DESC, ru.QuestionnaireId";

$stmt = $cnx->prepare($sql);
$stmt->execute($params);
$historique = $stmt->fetchAll(PDO::FETCH_ASSOC);


$groupe_historique = [];
foreach ($historique as $parquiz) {
    $key = $parquiz['Questionnaire'] . ' - ' . date("d/m/Y H:i", strtotime($parquiz['DateReponse']));
    $groupe_historique[$key][] = $parquiz;
}

if($role == "admin"){
    $accueil = "../ADMIN/adminaccueil.php";
    }
else if ($role == "utilisateur"){
    $accueil = "accueil.php";
    }

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
                <a href="<?= $accueil ?>" class="btn btn-primary mt-4">Retour aux questionnaires</a>
            </div>
        </form>
        <?php if (empty($historique)): ?>
    <p>Aucune réponse enregistrée.</p>
<?php else: ?>
    <?php foreach ($groupe_historique as $key => $responses): ?>
        <h4 class="mt-4"><?= htmlspecialchars($key); ?></h4>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Thème</th>
                    <th>Question</th>
                    <th>Réponse donnée</th>
                    <th>Poids</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($responses as $parquiz): ?>
                    <tr>
                        <td><?= htmlspecialchars($parquiz['Theme']); ?></td>
                        <td><?= htmlspecialchars($parquiz['Question']); ?></td>
                        <td><?= htmlspecialchars($parquiz['ReponseChoisie']); ?></td>
                        <td><?= htmlspecialchars($parquiz['Poids']); ?> point(s)</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endforeach; ?>
<?php endif; ?>
    </div>
</body>
</html>
