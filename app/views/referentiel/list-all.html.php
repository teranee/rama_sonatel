<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tous les référentiels</title>
    <link rel="stylesheet" href="/assets/css/referentiels.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Tous les Référentiels</h1>
            <a href="?page=referentiels" class="btn btn-back">Retour</a>
        </div>

        <div class="cards-container">
            <?php if (!empty($referentiels)): ?>
                <?php foreach ($referentiels as $ref): ?>
                    <div class="card">
                        <div class="card-image">
                            <img src="<?= $ref['image'] ?? 'assets/images/referentiels/default.jpg' ?>" 
                                 alt="<?= htmlspecialchars($ref['name']) ?>">
                        </div>
                        <div class="card-content">
                            <h3 class="card-title"><?= htmlspecialchars($ref['name']) ?></h3>
                            <p class="card-subtitle"><?= count($ref['modules'] ?? []) ?> modules</p>
                            <p class="card-description"><?= htmlspecialchars($ref['description']) ?></p>
                        </div>
                        <div class="card-footer">
                            <div class="card-avatars">
                                <?php for($i = 0; $i < min(3, count($ref['apprenants'] ?? [])); $i++): ?>
                                    <div class="avatar"></div>
                                <?php endfor; ?>
                            </div>
                            <div class="card-learners">
                                <?= count($ref['apprenants'] ?? []) ?> apprenants
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-data">Aucun référentiel trouvé</div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>