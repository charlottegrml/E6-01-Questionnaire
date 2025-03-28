<?php
session_start();
include '../BDD/cnx.php';
//Vérifie que l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    if ($role == "utilisateur")
    {
        header("Location: accueil.php");
        exit;
    }
    else if ($role == "admin")
    {
        header("Location: adminaccueil.php");
        exit;
    }
}
$_SESSION['user_answers'] = []; // Réinitialise les réponses avant de commencer

$questionnaire_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];
//Récupère questions du questionnaire en fonction de l'id
$stmt = $cnx->prepare("SELECT Id, Libelle, QuestionnaireId, TypeId FROM question WHERE question.QuestionnaireId = :questionnaire_id ORDER BY RAND()");
$stmt->bindParam(':questionnaire_id', $questionnaire_id);
$stmt->execute();
$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$questions) {
    die("Aucune question disponible pour ce questionnaire.");
}

$user_answers = array();
$_SESSION['user_answers'] = $user_answers;

$role = $_SESSION['Role'];
$_SESSION['Role'] = $role;

$_SESSION['questions'] = $questions;
$_SESSION['current_question'] = 0;
$_SESSION['score'] = 0;
$_SESSION['total_questions'] = count($questions);
$_SESSION['id_questionnaire'] = $questionnaire_id;
$_SESSION['user_answers'] = [];

header("Location: quiz.php");
exit;
?>
