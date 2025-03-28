<?php
session_start();
include '../BDD/cnx.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== "admin") {
    header("Location: accueil.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $userId = $_POST["userId"];
    $groupesSelectionnes = $_POST["groupes"] ?? [];

    // Supprimer les groupes actuels
    $stmt = $cnx->prepare("DELETE FROM utilisateurs_groupes WHERE UtilisateurId = ?");
    $stmt->execute([$userId]);

    // Insérer les nouveaux groupes sélectionnés
    $stmt = $cnx->prepare("INSERT INTO utilisateurs_groupes (UtilisateurId, GroupeId) VALUES (?, ?)");
    foreach ($groupesSelectionnes as $groupeId) {
        $stmt->execute([$userId, $groupeId]);
    }

    $_SESSION["success"] = "Groupes mis à jour !";
    header("Location: utilisateurs.php");
    exit;
}
?>
