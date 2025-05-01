<?php
require_once __DIR__ . '/../../../app/models/model.php';
/* render('admin.layout.php', 'promotion/details.html.php', [
    'promotion' => $promotion,
    'model' => $model
]); */
$id = $_GET['id'] ?? null;
if (!$id) {
    die('ID de la promotion manquant.');
}

// Charger les données des promotions depuis le modèle
$promotions = $model['get_all_promotions']();

// Trouver la promotion correspondante
$promotion = array_filter($promotions, function ($promo) use ($id) {
    return $promo['id'] == $id;
});

if (empty($promotion)) {
    die('Promotion introuvable.');
}

$promotion = reset($promotion);

// Gestion de l'état en fonction de la date actuelle
$today = date('Y-m-d');
if ($promotion['date_fin'] < $today) {
    $promotion['etat'] = 'terminé';
} else {
    $promotion['etat'] = 'en cours';
}

// Si le bouton "Terminer" est cliqué, mettre à jour l'état
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['terminer'])) {
    if (isset($model['update_promotion']) && is_callable($model['update_promotion'])) {
        $promotion['etat'] = 'terminé';
        $promotion['status'] = 'inactive';
        $promotion['date_fin'] = date('Y-m-d'); // Met à jour la date de fin à aujourd'hui
        $model['update_promotion']($promotion);
        header('Location: ?page=promotion_details&id=' . $promotion['id']);
        exit();
    } else {
        die('La fonction update_promotion n\'est pas définie.');
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de la promotion</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9fafb;
            margin: 0;
            padding: 0;
            height: 100vh;
        }

        .details-container {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 30px;
            width: 60%;
            margin-left: 20%;
            margin-top: 2%;
        }

        .details-container h1 {
            text-align: center;
            color: orangered;
            margin-bottom: 30px;
        }

        .details-container table {
            width: 100%;
            border-collapse: collapse;
        }

        .details-container table th,
        .details-container table td {
            text-align: left;
            padding: 15px;
            border: 1px solid #ddd;
        }

        .details-container table th {
            background-color: rgba(172, 170, 169, 0.21);
            color: #555;
            font-weight: bold;
        }

        .details-container table td {
            color: #333;
        }

        .status-badge,
        .etat-badge {
            display: inline-block;
            padding: 8px 15px;
            border-radius: 10px;
            font-size: 15px;
            font-weight: bold;
            color: white;
        }

        .status-badge.active {
            background-color: #38a169; /* Vert */
        }

        .status-badge.inactive {
            background-color: rgba(229, 62, 62, 0.94); /* Rouge */
        }

        .etat-badge.en-cours {
            background-color: #3182ce; /* Bleu */
        }

        .etat-badge.termine {
            background-color: #718096; /* Gris */
        }

        .referentiel-tag {
            display: inline-block;
            background-color: #edf2f7;
            color: #2d3748;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
            margin-right: 5px;
            margin-bottom: 5px;
        }

        .details-container .back-link,
        .details-container .terminate-button {
            display: inline-block;
            text-align: center;
            margin-top: 20px;
            text-decoration: none;
            color: #fff;
            font-weight: bold;
            padding: 10px 20px;
            border-radius: 5px;
        }

        .details-container .back-link {
            background-color:rgb(241, 67, 14);
        }

        .details-container .back-link:hover {
            background-color:rgba(241, 67, 14, 0.75)
        }

        .details-container .terminate-button {
            background-color:rgb(241, 67, 14);
            margin-left:30px;
            border: none;
            font-size: 16px;
        }

        .details-container .terminate-button:hover {
            background-color:rgba(241, 67, 14, 0.75);
        }
    </style>
</head>
<body>
    <div class="details-container">
        <h1>Détails de la promotion</h1>
        <table>
            <tr>
                <th>Nom</th>
                <td><?= htmlspecialchars($promotion['name']) ?></td>
            </tr>
            <tr>
                <th>Statut</th>
                <td>
                    <span class="status-badge <?= htmlspecialchars($promotion['status']) ?>">
                        <?= htmlspecialchars($promotion['status'] === 'active' ? 'Actif' : 'Inactif') ?>
                    </span>
                </td>
            </tr>
            <tr>
                <th>État</th>
                <td>
                    <span class="etat-badge <?= htmlspecialchars($promotion['etat'] === 'en cours' ? 'en-cours' : 'termine') ?>">
                        <?= htmlspecialchars($promotion['etat']) ?>
                    </span>
                </td>
            </tr>
            <tr>
                <th>Date de début</th>
                <td><?= htmlspecialchars($promotion['date_debut']) ?></td>
            </tr>
            <tr>
                <th>Date de fin</th>
                <td><?= htmlspecialchars($promotion['date_fin']) ?></td>
            </tr>
            <tr>
                <th>Référentiels associés</th>
                <td>
                    <?= !empty($promotion['referentiels']) && is_array($promotion['referentiels']) 
                        ? count($promotion['referentiels']) . ' référentiel(s)' 
                        : 'Aucun référentiel associé' ?>
                </td>
            </tr>
            <tr>
                <th>Nombre d'apprenants</th>
                <td><?= count($promotion['apprenants'] ?? []) ?></td>
            </tr>
        </table>
        <a href="?page=promotions" class="back-link">Retour</a>
        <form method="POST" style="display: inline;">
            <button type="submit" name="terminer" class="terminate-button" onclick="return confirm('Voulez-vous vraiment terminer cette promotion ?')">Terminer la promotion</button>
        </form>
    </div>
</body>
</html>