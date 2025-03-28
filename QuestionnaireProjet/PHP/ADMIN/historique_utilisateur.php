<?php 
session_start();
include '../BDD/cnx.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== "admin") {
    header("Location: accueil.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: utilisateurs.php");
    exit;
}

$user_id = $_GET['id'];

// Récupérer les réponses de l'utilisateur
$stmt = $cnx->prepare("SELECT r.DateReponse, q.Libelle AS Questionnaire, v.Nom_Valeur 
                        FROM reponses_utilisateur r
                        JOIN questionnaire q ON r.QuestionnaireId = q.Id
                        JOIN valeur v ON r.ValeurId = v.Id
                        WHERE r.UtilisateurId = :user_id");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$historique = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
