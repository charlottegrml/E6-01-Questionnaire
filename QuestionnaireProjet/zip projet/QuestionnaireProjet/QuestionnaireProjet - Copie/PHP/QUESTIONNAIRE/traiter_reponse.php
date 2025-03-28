<?php
session_start();
include '../BDD/cnx.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['questions']) || !isset($_SESSION['current_questionnaire'])) {
    header("Location: accueil.php");
    exit;
}

if (!isset($_POST['reponse_id']) || !isset($_SESSION['current_question'])) {
    header("Location: quiz.php");
    exit;
}

// Si l'utilisateur commence un nouveau questionnaire
if (!isset($_SESSION['user_answers'])) {
    $_SESSION['user_answers'] = []; // On vide les réponses précédentes
}
$user_answers = $_SESSION['user_answers'];
$_SESSION['user_answers'] = $user_answers;

$reponse_id = $_POST['reponse_id'];
$_SESSION['reponse_id']= $reponse_id;
$questionIndex = $_SESSION['current_question'];
$question = $_SESSION['questions'][$questionIndex];
$id_questionnaire = $_SESSION['questionnaire_id']; // Vérifie que cette variable existe
$id_question = $question['Id'];

// Récupérer le poids et la valeur associée
$stmt = $cnx->prepare("SELECT Poids, Nom_Valeur, Correct, Id FROM valeur WHERE Id = :reponse_id");
$stmt->bindParam(':reponse_id', $reponse_id);
$stmt->execute();
$reponse = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$reponse) {
    $_SESSION['error'] = "Réponse invalide.";
    header("Location: quiz.php");
    exit;
}

$poids = $reponse['Poids'];
$id_valeur = $reponse['Id']; 
$correct = $reponse['Correct'];
$chosen_answer = $reponse['Nom_Valeur'];

// Insérer la réponse dans la base de données
$sql = "INSERT INTO reponses_utilisateur (UtilisateurId, QuestionnaireId, QuestionId, Poids, ValeurId)
        VALUES (:user, :questionnaire, :question, :poids, :valeur)";
$stmt = $cnx->prepare($sql);
$stmt->bindParam(":user", $_SESSION["user_id"]);
$stmt->bindParam(":questionnaire", $id_questionnaire);
$stmt->bindParam(":question", $id_question);
$stmt->bindParam(":poids", $poids);
$stmt->bindParam(":valeur", $id_valeur);
$stmt->execute();

$_SESSION['poids'] = $poids;
// Mettre à jour le score en session
if (!isset($_SESSION['score'])) {
    $_SESSION['score'] = 0;
}

if ($correct == 1) {
    $_SESSION['score'] += $poids;
}



// Stocker les réponses pour ce questionnaire uniquement
$_SESSION['user_answers'][] = [
    'question' => $question['Libelle'],
    'chosen_answer' => $chosen_answer,
    'correct' => $correct,
    'poids' => $poids
];

//$_SESSION['question_id'] = $id_question;
// Passer à la question suivante
$_SESSION['current_question']++;

if ($_SESSION['current_question'] >= $_SESSION['total_questions']) {
    header("Location: resultat.php");
} else {
    header("Location: quiz.php");
}
exit;
?>
