<?php
require_once "database.php";
session_start();

// Redirige l'utilisateur connecté vers la page d'accueil
if (isset($_SESSION["employe"])) {
   header("Location: index.php");
   exit(); // Arrête l'exécution du script après la redirection
}

$errors = array(); // Initialisation du tableau des erreurs

if (isset($_POST["submit"])) {
    // Récupération des données du formulaire
    $raisonsociale = $_POST["raisonsociale"];
    $telephone_salon = $_POST["telephone_salon"];
    $adresse_salon = $_POST["adresse_salon"];
    $nom = $_POST["nom"];
    $prenom = $_POST["prenom"];
    $telephone_employe = $_POST["telephone_employe"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $passwordRepeat = $_POST["repeat_password"];

    // Vérification des champs requis
    $required_fields = array("nom" => "Nom", "prenom" => "Prénom", "telephone_employe" => "Téléphone", "email" => "Email", "password" => "Mot de passe", "repeat_password" => "Confirmer le mot de passe", "raisonsociale" => "Raison sociale", "telephone_salon" => "Téléphone du salon", "adresse_salon" => "Adresse du salon");

    foreach ($required_fields as $field_name => $field_label) {
        if (empty($_POST[$field_name])) {
            $errors[] = "Le champ \"$field_label\" est requis.";
        }
    }

    // Validation de l'adresse email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "L'adresse email n'est pas valide.";
    }

    // Vérification de la longueur du mot de passe
    if (strlen($password) < 8) {
        $errors[] = "Le mot de passe doit comporter au moins 8 caractères.";
    }

    // Vérification de la correspondance des mots de passe
    if ($password !== $passwordRepeat) {
        $errors[] = "Les mots de passe ne correspondent pas.";
    }

    // Vérification si l'email existe déjà dans la base de données
    $sql = "SELECT * FROM employe WHERE email = ?";
    $stmt = mysqli_stmt_init($conn);
    if (mysqli_stmt_prepare($stmt, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $errors[] = "L'email existe déjà.";
        }
    } else {
        $errors[] = "Une erreur s'est produite lors de la vérification de l'email.";
    }

    // S'il n'y a pas d'erreurs, insérer les données dans la base de données
    if (empty($errors)) {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        // Insérer les données du salon
        $sql_salon = "INSERT INTO salon (raisonsociale, telephone, adresse) VALUES (?, ?, ?)";
        $stmt_salon = mysqli_stmt_init($conn);
        if (mysqli_stmt_prepare($stmt_salon, $sql_salon)) {
            mysqli_stmt_bind_param($stmt_salon, "sss", $raisonsociale, $telephone_salon, $adresse_salon);
            mysqli_stmt_execute($stmt_salon);
            $last_idsalon = mysqli_insert_id($conn);
        } else {
            $errors[] = "Une erreur s'est produite lors de l'insertion du salon.";
        }

        // Insérer les données de l'employé
        $sql_employe = "INSERT INTO employe (idsalon, nom, prenom, telephone, email, password) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt_employe = mysqli_stmt_init($conn);
        if (mysqli_stmt_prepare($stmt_employe, $sql_employe)) {
            mysqli_stmt_bind_param($stmt_employe, "isssss", $last_idsalon, $nom, $prenom, $telephone_employe, $email, $passwordHash);
            mysqli_stmt_execute($stmt_employe);
            echo "<div class='alert alert-success'>Vous êtes inscrit avec succès.</div>";
        } else {
            $errors[] = "Une erreur s'est produite lors de l'insertion de l'employé.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire d'inscription</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">

<?php
    // Affichage des erreurs
    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo "<div class='alert alert-danger'>$error</div>";
        }
    }
?>
        <form action="registration.php" method="post">
            <h3>Informations du salon :</h3>
            <div class="form-group">
                <input type="text" class="form-control" name="raisonsociale" placeholder="Raison sociale">
            </div>
            <div class="form-group">
                <input type="text" class="form-control" name="telephone_salon" placeholder="Téléphone">
            </div>
            <div class="form-group">
                <input type="text" class="form-control" name="adresse_salon" placeholder="Adresse">
            </div>
            <h3>Informations du propriétaire :</h3>
            <div class="form-group">
                <input type="text" class="form-control" name="nom" placeholder="Nom">
            </div>
            <div class="form-group">
                <input type="text" class="form-control" name="prenom" placeholder="Prénom">
            </div>
            <div class="form-group">
                <input type="tel" class="form-control" name="telephone_employe" placeholder="Téléphone">
            </div>
            <div class="form-group">
                <input type="email" class="form-control" name="email" placeholder="Email">
            </div>
            <div class="form-group">
                <input type="password" class="form-control" name="password" placeholder="Mot de passe">
            </div>
            <div class="form-group">
                <input type="password" class="form-control" name="repeat_password" placeholder="Confirmer le mot de passe">
            </div>
            <div class="form-btn">
                <input type="submit" class="btn btn-primary" value="S'inscrire" name="submit">
            </div>
        </form>
        <br>
        <div>
            <p>Déjà inscrit ? <a href="login.php">Connectez-vous ici</a></p>
        </div>
    </div>
</body>
</html>
