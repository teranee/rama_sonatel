<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>list ref</title>
    <link rel="stylesheet" href="/assets/css/referentiels.css">
</head>
<body>

<!-- app/views/referentiel/list.html.php -->
<!-- app/views/referentiel/list.html.php -->

<!-- app/views/referentiel/list.html.php -->
<div class="container">
    <div class="header">
        <h1>Référentiels de <?= htmlspecialchars($current_promotion['name']) ?></h1>
        <p>Gérer les référentiels de la promotion</p>
    </div>
    
    <div class="search-section">
        <div class="search-bar">
            <div class="search-icon">🔍</div>
            <input type="text" name="search" placeholder="Rechercher un référentiel..." 
                   value="<?= htmlspecialchars($search ?? '') ?>">
        </div>
        
        <button class="btn btn-orange" onclick="window.location.href='?page=all-referentiels'">
            <span>📋</span> Tous les référentiels
        </button>
        
        <button class="btn btn-teal" onclick="window.location.href='?page=assign-referentiels'">
            <span>+</span> Ajouter à la promotion
        </button>
    </div>
    
    <div class="cards-container">
        <?php if (empty($referentiels)): ?>
            <div class="no-data">Aucun référentiel n'est assigné à cette promotion</div>
        <?php else: ?>
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
        <?php endif; ?>
    </div>
</div>

</body>

</html>