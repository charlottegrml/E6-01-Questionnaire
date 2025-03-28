<?php

session_start();
include '../BDD/cnx.php';

if (!isset($_SESSION['user_answers'])) {
    $_SESSION['user_answers'] = [];
}

$user_answers = $_SESSION['user_answers'];
$score = $_SESSION['score'] ?? 0;
$total_points = 0; // Stocke le total des points

foreach ($user_answers as $answer_data) {
    // Vérifier si le poids existe pour chaque réponse donnée
    $poids = $answer_data['poids'] ?? 0; // Si 'poids' n'est pas défini, on met 0
    $total_points += abs($poids); // On additionne le poids absolu (pour éviter les soustractions)
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Résultat</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../CSS/style4.css">
</head>
<body>
    <div class="container">
        <h2 class="my-4">Résultats du questionnaire</h2>
        <p class="lead">Score : <strong><?php echo $score . " / " . $total_points; ?></strong></p>

        <h3 class="mt-4">Détail des réponses :</h3>
        <ul class="list-group">
            <?php foreach ($user_answers as $reponse_id => $answer_data): ?>
                <li class="list-group-item">
                    <h5><?php echo htmlspecialchars($answer_data['question'] ?? 'Question inconnue'); ?></h5>
                    <ul>
                        <li class="p-2 <?php echo $answer_data['correct'] ? 'bg-success text-white' : 'bg-danger text-white'; ?>">
                            (<?php echo htmlspecialchars($answer_data['poids'] ?? 0); ?> points) 
                            Réponse choisie : <?php echo htmlspecialchars($answer_data['chosen_answer'] ?? 'Réponse non enregistrée'); ?>
                        </li>
                    </ul>
                </li>
            <?php endforeach; ?>
        </ul>

        <a href="accueil.php" class="btn btn-primary mt-4">Retour aux questionnaires</a>
        <form action="historique.php" method="GET">
            <input type="hidden" name="id_questionnaire" value="<?= $id_questionnaire ?>">
            <button type="submit" class="mb-3 btn btn-secondary">Historique</button>
        </form>
    </div>
</body>
</html>
