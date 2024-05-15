<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prise de Rendez-Vous</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="employee/employee.css">
</head>
<body>
    <?php
    session_start(); // Démarrage de la session au début du fichier
    include "employee/header.php";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $conn = new mysqli("localhost", "root", "", "salon");
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];
        $telephone = $_POST['telephone'];
        $dateRendezVous = $_POST['dateRendezVous'];
        $heureRendezVous = $_POST['heureRendezVous'];
        $services = $_POST['services'] ?? [];

        // Insertion du client
        $sql = "INSERT INTO client (nom, prenom, telephone) VALUES ('$nom', '$prenom', '$telephone')";
        if ($conn->query($sql) === TRUE) {
            $last_idclient = $conn->insert_id;

            // Insertion du rendez-vous
            $sql = "INSERT INTO rendezvous (idclient, date, heure_debut) VALUES ('$last_idclient', '$dateRendezVous', '$heureRendezVous')";
            if ($conn->query($sql) === TRUE) {
                $last_idrendezvous = $conn->insert_id;

                // Insertion des services choisis dans la table choisir
                foreach ($services as $idservice) {
                    $sql = "INSERT INTO choisir (idrendezvous, idservice) VALUES ('$last_idrendezvous', '$idservice')";
                    $conn->query($sql);
                }

                // Préparation des données pour la page de résumé
                $_SESSION['nom'] = $nom;
                $_SESSION['prenom'] = $prenom;
                $_SESSION['date'] = $dateRendezVous;
                $_SESSION['heure'] = $heureRendezVous;
                $_SESSION['service'] = $service;

                $_SESSION['services'] = [];
foreach ($services as $idservice) {
    $sql = "SELECT designation, tarif, duree FROM service WHERE idservice = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $idservice);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        while ($service = $result->fetch_assoc()) {
            $_SESSION['services'][] = $service;
        }
    }
    $stmt->close();
}


                // Redirection vers la page de résumé
                $conn->close();
                header("Location: rendezvous-summary.php");
                exit();
            }
        }
        $conn->close(); // Fermez la connexion ici, après toutes les opérations
    }
    ?>

    <div class="container mt-5">
        <h2>Prise de Rendez-Vous</h2>
        <form method="post">
            <div class="mb-3">
                <label for="nom" class="form-label">Nom:</label>
                <input type="text" class="form-control" id="nom" name="nom" required>
            </div>
            <div class="mb-3">
                <label for="prenom" class="form-label">Prénom:</label>
                <input type="text" class="form-control" id="prenom" name="prenom" required>
            </div>
            <div class="mb-3">
                <label for="telephone" class="form-label">Téléphone:</label>
                <input type="text" class="form-control" id="telephone" name="telephone" required>
            </div>
            <div class="mb-3">
                <label for="dateRendezVous" class="form-label">Date du rendez-vous:</label>
                <input type="date" class="form-control" id="dateRendezVous" name="dateRendezVous" required>
            </div>
            <div class="mb-3">
                <label for="heureRendezVous" class="form-label">Heure du rendez-vous:</label>
                <input type="time" class="form-control" id="heureRendezVous" name="heureRendezVous" required>
            </div>
            <div class="mb-3">
                <h4>Services disponibles :</h4>
                <?php
                // La connexion devrait être réutilisée d'en haut, assurez-vous de ne pas la fermer prématurément
                $sql = "SELECT * FROM service";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                ?>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="services[]" id="service<?= $row['idservice'] ?>" value="<?= $row['idservice'] ?>">
                    <label class="form-check-label" for="service<?= $row['idservice'] ?>">
                        <?= htmlspecialchars($row['designation']) ?> (<?= htmlspecialchars($row['tarif']) ?>€ - <?= htmlspecialchars($row['duree']) ?>min)
                    </label>
                </div>
                <?php
                    }
                }
                $conn->close();
                ?>
            </div>
            <button type="submit" class="btn btn-primary">Prendre rendez-vous</button>
        </form>
    </div>
</body>
</html>
