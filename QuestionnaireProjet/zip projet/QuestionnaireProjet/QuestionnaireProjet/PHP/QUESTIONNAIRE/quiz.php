<?php
session_start();
include '../BDD/cnx.php';

// Vérifier si l'utilisateur est connecté et si un questionnaire est en cours
if (!isset($_SESSION['user_id']) || !isset($_SESSION['id_questionnaire'])) {
    header("Location: accueil.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$id_questionnaire = $_SESSION['id_questionnaire'];
$user_answers = $_SESSION['user_answers'];
$_SESSION['user_answers'] = $user_answers;

// Charger les questions si elles ne sont pas en session
if (!isset($_SESSION['questions'])) {
    $stmt = $cnx->prepare("SELECT Id, Libelle FROM question WHERE QuestionnaireId = :id_questionnaire");
    $stmt->bindParam(":id_questionnaire", $id_questionnaire, PDO::PARAM_INT);
    $stmt->execute();
    $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$questions) {
        $_SESSION['error'] = "Aucune question trouvée pour ce questionnaire.";
        header("Location: accueil.php");
        exit;
    }

    $_SESSION['questions'] = $questions;
    $_SESSION['current_question'] = 0;
}

// Vérifier s'il reste des questions
if ($_SESSION['current_question'] >= count($_SESSION['questions'])) {
    header("Location: resultat.php");
    exit;
}

// Récupérer la question actuelle
$question = $_SESSION['questions'][$_SESSION['current_question']];
$question_id = $question['Id'];
$libelle = $question['Libelle'];

$_SESSION['user_id'] = $user_id;
$_SESSION['current_questionnaire'] = $id_questionnaire;

// Récupérer les réponses possibles pour cette question
$stmt = $cnx->prepare("SELECT Id, Nom_Valeur FROM valeur WHERE QuestionId = :question_id");
$stmt->bindParam(":question_id", $question_id, PDO::PARAM_INT);
$stmt->execute();
$reponses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Questionnaire</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../CSS/style3.css">
    <script>
        // Fonction pour soumettre le formulaire dès qu'un bouton est cliqué
        function submitForm(reponseId) {
            var form = document.getElementById('quizForm');
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'reponse_id';
            input.value = reponseId;
            form.appendChild(input);
            form.submit();
        }
    </script>
</head>
<body>
    <div class="container">
        <h2 class="my-4">Question <?php echo $_SESSION['current_question'] + 1; ?> / <?php echo count($_SESSION['questions']); ?></h2>
        <p class="lead"><?php echo htmlspecialchars($libelle); ?></p>
        
        <form id="quizForm" action="traiter_reponse.php" method="POST">
            <div class="d-flex justify-content-center">
                <?php if (!empty($reponses)): ?>
                    <?php foreach ($reponses as $reponse): ?>
                        <!-- Bouton aligné horizontalement et soumission immédiate -->
                        <button type="button" class="btn btn-primary m-2" onclick="submitForm(<?php echo $reponse['Id']; ?>)">
                            <?php echo htmlspecialchars($reponse['Nom_Valeur']); ?>
                        </button>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-danger">Aucune réponse disponible pour cette question.</p>
                <?php endif; ?>
            </div>
        </form>
    </div>
</body>
</html>
