<?php 
session_start();
include '../BDD/cnx.php';
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../CSS/style.css">
</head>
<body>
    <div class="container">
        <h1 class="mt-5">Connexion</h1>
        <div id="container2">
            <form action="traitement_login.php" method="POST">
                <div class="mb-3">
                    <label for="nomUtilisateur" class="form-label">Nom d'utilisateur :</label>
                    <input type="text" class="form-control" name="nomUtilisateur" required>
                </div>
                <div class="mb-3">
                    <label for="motDePasse" class="form-label">Mot de passe :</label>
                    <input type="password" class="form-control" name="motDePasse" required>
                </div>
                <div class="mt-2">
                <a href="MDPoublie.php">Mot de passe oublié ?</a>
            </div>
                <div class="d-flex justify-content-between mt-2">
                <button type="submit" class="btn btn-primary">Se connecter</button>
            </form>

            <div class="d-flex justify-content-between mt-2">
            
                <form action="inscription.php" method="POST">
                    <button type="submit" class="btn btn-primary" id="btnInscription">S'inscrire</button>
                </form>
                
            </div>
        </div>
            
        </div>
    </div>
</body>
</html>
