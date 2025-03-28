<?php
session_start();
include '../BDD/cnx.php';
include '../BDD/config.php';


$_SESSION["old"] = [
    "nomUtilisateur" => $nomUtilisateur,
    "email" => $email,
    "nom" => $nom,
    "prenom" => $prenom
];

$admin_creation = isset($_POST["admin_creation"]) ? true : false;

// if (isset($_SESSION[("user_id")]))
// {
//     $user_id=$_SESSION[("user_id")];
// }


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nomUtilisateur = trim($_POST["nomUtilisateur"]);
    $email = trim($_POST["email"]);
    $motDePasse = $_POST["motDePasse"];
    $nom = trim($_POST["nom"]);
    $prenom = trim($_POST["prenom"]);

    $_SESSION["errors"] = [];

    // Vérifier si le nom d'utilisateur existe déjà
    $stmt = $cnx->prepare("SELECT COUNT(*) FROM utilisateurs WHERE NomUtilisateur = :nomUtilisateur");
    $stmt->bindParam(":nomUtilisateur", $nomUtilisateur);
    $stmt->execute();
    if ($stmt->fetchColumn() > 0) {
        $_SESSION["errors"]['nomUtilisateur'] = "Ce nom d'utilisateur est déjà pris.";
    }

    // Vérifier si l'email existe déjà (attention, il est chiffré)
    $emailCrypte = encryptEmail($email);
    $stmt = $cnx->prepare("SELECT COUNT(*) FROM utilisateurs WHERE Email = :email");
    $stmt->bindParam(":email", $emailCrypte);
    $stmt->execute();
    if ($stmt->fetchColumn() > 0) {
        $_SESSION["errors"]['email'] = "Cet email est déjà utilisé.";
    }

    // Vérifier la complexité du mot de passe
    if (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\W).{10,}$/", $motDePasse)) {
        $_SESSION["errors"]['motDePasse'] = "Le mot de passe doit contenir au moins 10 caractères, une minuscule, une majuscule et un caractère spécial.";
    }

    // Rediriger si erreur
    if (!empty($_SESSION["errors"])) {
        if ($admin_creation) {
            header("Location: ../ADMIN/creer_utilisateur.php");
        } else {
        header("Location: inscription.php");
        }
        exit;
    }

    // Hacher le mot de passe
    $motDePasseHash = password_hash($motDePasse, PASSWORD_BCRYPT);
    
    try {
        $sql = "INSERT INTO utilisateurs (NomUtilisateur, Email, MotDePasse, Nom, Prenom) 
                VALUES (:nomUtilisateur, :email, :motDePasse, :nom, :prenom)";
        $stmt = $cnx->prepare($sql);
        $stmt->bindParam(":nomUtilisateur", $nomUtilisateur);
        $stmt->bindParam(":email", $emailCrypte);
        $stmt->bindParam(":motDePasse", $motDePasseHash);
        $stmt->bindParam(":nom", $nom);
        $stmt->bindParam(":prenom", $prenom);
        $stmt->execute();

        if ($admin_creation) {
            header("Location: ../ADMIN/adminaccueil.php");
        } else {
            $_SESSION["success"] = "Inscription réussie ! Vous pouvez maintenant vous connecter.";
            header("Location: login.php");
        }
        exit;
        
    } catch (PDOException $e) {
        $_SESSION["error"] = "Erreur lors de l'inscription : " . $e->getMessage();
        if ($admin_creation) {
            header("Location: ../ADMIN/creer_utilisateur.php");
        } else {
        header("Location: inscription.php");
        }
        exit;
    }
} else {
    if ($admin_creation) {
        header("Location: ../ADMIN/creer_utilisateur.php");
    } else {
    header("Location: inscription.php");
    }
    exit;
}
?>
