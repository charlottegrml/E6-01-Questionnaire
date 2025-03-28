<?php
session_start();
include '../BDD/cnx.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== "admin") {
    header("Location: accueil.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['user_id'], $_POST['new_role'])) {
    $user_id = intval($_POST['user_id']);
    $new_role = ($_POST['new_role'] === 'admin') ? 'admin' : 'utilisateur';

    // Vérifier que l'utilisateur existe
    $stmt = $cnx->prepare("SELECT Id FROM utilisateurs WHERE Id = :id");
    $stmt->execute(['id' => $user_id]);

    if ($stmt->fetch()) {
        // Mettre à jour le rôle
        $update = $cnx->prepare("UPDATE utilisateurs SET Role = :role WHERE Id = :id");
        $update->execute([
            'role' => $new_role,
            'id' => $user_id
        ]);

        $_SESSION['success'] = "Le rôle a été mis à jour avec succès.";
    } else {
        $_SESSION['error'] = "Utilisateur introuvable.";
    }
}

// Redirection vers la liste des utilisateurs
header("Location: ../ADMIN/utilisateurs.php");
exit;
