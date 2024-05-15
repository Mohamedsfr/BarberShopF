<?php
session_start();

if (!isset($_SESSION['services']) || !is_array($_SESSION['services'])) {
    echo "Erreur: Aucun service sélectionné ou format incorrect.";
    exit; // Ou rediriger vers une autre page
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Résumé du Rendez-vous</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa; /* Couleur de fond légère */
        }
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1); /* Ombre légère */
            margin-top: 40px;
        }
        h2 {
            color: #0d6efd; /* Bleu Bootstrap */
            text-align: center;
            margin-bottom: 20px;
        }
        ul {
            list-style-type: none; /* Retire les puces */
            padding: 0;
        }
        li {
            margin-bottom: 10px; /* Espace entre les éléments */
        }
        .summary-item {
            padding: 10px;
            background-color: #e9ecef;
            border-radius: 5px;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Résumé du Rendez-vous</h2>
        <div class="summary-item">
            <strong>Nom:</strong> <?php echo htmlspecialchars($_SESSION['nom']); ?>
        </div>
        <div class="summary-item">
            <strong>Prénom:</strong> <?php echo htmlspecialchars($_SESSION['prenom']); ?>
        </div>
        <div class="summary-item">
            <strong>Date du rendez-vous:</strong> <?php echo htmlspecialchars($_SESSION['date']); ?>
        </div>
        <div class="summary-item">
            <strong>Heure du rendez-vous:</strong> <?php echo htmlspecialchars($_SESSION['heure']); ?>
        </div>
        <div class="summary-item">
            <strong>Services Choisis:</strong>
            <ul>
                <?php
                $total = 0;
                foreach ($_SESSION['services'] as $service) {
                    echo "<li>" . htmlspecialchars($service['designation']) . " - " . htmlspecialchars($service['tarif']) . "€ (" . htmlspecialchars($service['duree']) . " min)</li>";
                    $total += $service['tarif'];
                }
                ?>
            </ul>
        </div>
        <div class="summary-item">
            <strong>Total à Payer:</strong> <?php echo $total; ?> €
        </div>
    </div>
</body>
</html>
