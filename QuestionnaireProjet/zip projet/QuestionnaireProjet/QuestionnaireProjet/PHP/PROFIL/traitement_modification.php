<?php
session_start();
include '../BDD/cnx.php';
include '../BDD/config.php';

// Vérification si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Récupérer les données envoyées par le formulaire
$errors = [];
$old = $_POST; // Récupérer les anciennes valeurs pour les afficher en cas d'erreur

// Récupérer les données du formulaire
$nomUtilisateur = trim($_POST['nomUtilisateur']);
$email = trim($_POST['email']);
$motDePasse = trim($_POST['motDePasse']);
$nom = trim($_POST['nom']);
$prenom = trim($_POST['prenom']);

// Valider les champs
if (empty($nomUtilisateur)) {
    $errors['nomUtilisateur'] = "Le nom d'utilisateur est requis.";
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = "L'email est invalide.";
}

if (empty($nom)) {
    $errors['nom'] = "Le nom est requis.";
}

if (empty($prenom)) {
    $errors['prenom'] = "Le prénom est requis.";
}

if (!empty($motDePasse) && strlen($motDePasse) < 6) {
    $errors['motDePasse'] = "Le mot de passe doit comporter au moins 6 caractères.";
}

// Si pas d'erreurs, procéder à la mise à jour de l'utilisateur
if (empty($errors)) {
    // Hash du mot de passe si changé
    if (!empty($motDePasse)) {
        $motDePasseHash = password_hash($motDePasse, PASSWORD_BCRYPT);
        $sql = "UPDATE utilisateurs SET NomUtilisateur = :nomUtilisateur, email = :email, Nom = :nom, Prenom = :prenom, MotDePasse = :motDePasse WHERE id = :user_id";
        $stmt = $cnx->prepare($sql);
        $stmt->execute([
            'nomUtilisateur' => $nomUtilisateur,
            'email' => $email,
            'nom' => $nom,
            'prenom' => $prenom,
            'motDePasse' => $motDePasseHash,
            'user_id' => $_SESSION['user_id']
        ]);
    } else {
        // Mettre à jour sans changer le mot de passe
        $sql = "UPDATE utilisateurs SET NomUtilisateur = :nomUtilisateur, email = :email, Nom = :nom, Prenom = :prenom WHERE id = :user_id";
        $stmt = $cnx->prepare($sql);
        $stmt->execute([
            'nomUtilisateur' => $nomUtilisateur,
            'email' => encryptEmail($email),
            'nom' => $nom,
            'prenom' => $prenom,
            'user_id' => $_SESSION['user_id']
        ]);
        var_dump(encryptEmail($email));
    }

    // Rediriger l'utilisateur vers une page de succès ou la page de profil
    $_SESSION['success'] = "Les modifications ont été enregistrées avec succès.";
    header('Location: ../QUESTIONNAIRE/accueil.php');
    exit;
}

// Si des erreurs, les stocker dans la session pour les afficher dans le formulaire
$_SESSION['errors'] = $errors;
$_SESSION['old'] = $old;

// Rediriger vers le formulaire de modification avec les erreurs et anciennes données
header('Location: modifierProfile.php');
var_dump($_POST);
exit;
exit;

