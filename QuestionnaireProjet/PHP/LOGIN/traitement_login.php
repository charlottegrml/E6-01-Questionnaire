<?php
session_start();
include '../BDD/cnx.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //recupère les valeurs
    $nomUtilisateur = $_POST['nomUtilisateur'];
    $motDePasse = $_POST['motDePasse'];

    $sql = $cnx->prepare("SELECT id, MotDePasse, Nom, Prenom, Role, email FROM utilisateurs WHERE NomUtilisateur = :nomUtilisateur");
    $sql->bindParam(":nomUtilisateur", $nomUtilisateur);
    $sql->execute();
    $user = $sql->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($motDePasse, $user['MotDePasse'])) {
        $_SESSION['user_id'] = $user['id']; // Stocke l'ID utilisateur en session
        $_SESSION['username'] = $user['NomUtilisateur'];
        $_SESSION['prenom'] = $user['Prenom'];
        $_SESSION['nom'] = $user['Nom'];
        $_SESSION['role'] = $user['Role'];
        $role = $user['Role'];
        $_SESSION['email'] = $user['email'];
        $user['email'] = decryptEmail($user['email']);
        if ($role == "admin")
        {
            header("Location: ../ADMIN/adminaccueil.php"); 
        }
        else
        {
            header("Location: ../QUESTIONNAIRE/accueil.php"); 
        }
        exit();
    } else {
        echo "Identifiants incorrects !";
    }
}
?>
