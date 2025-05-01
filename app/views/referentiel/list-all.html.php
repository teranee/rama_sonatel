<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tous les référentiels</title>
    <link rel="stylesheet" href="/assets/css/referentiels.css">
    <link rel="stylesheet" href="/assets/css/all-ref.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Tous les Référentiels</h1>
            
            <!-- Boutons placés ici, directement sous le titre -->
            <div class="header-buttons">
                <a href="?page=create_referentiel" class="btn-create">Créer un référentiel</a>
                <a href="?page=referentiels" class="btn-back">Retour</a>
            </div>
        </div>

        <div class="cards-container">
            <?php if (empty($referentiels)): ?>
                <div class="no-data">Aucun référentiel disponible</div>
            <?php else: ?>
                <?php foreach ($referentiels as $ref): ?>
                    <div class="card">
                        <div class="card-image">
                            <img src="<?= $ref['image'] ?? 'assets/images/referentiels/default.jpg' ?>"
                                alt="<?= htmlspecialchars($ref['name']) ?>"
                                style="width: 100%; height: 200px; object-fit: cover;">
                        </div>
                        <div class="card-content">
                            <h3 class="card-title"><?= htmlspecialchars($ref['name']) ?></h3>
                            <p class="card-description"><?= htmlspecialchars($ref['description']) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php if ($pages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=all-referentiels&page_num=<?= $page - 1 ?>" class="pagination-link">Précédent</a>
                <?php endif; ?>
                
                <?php for ($i = 1; $i <= $pages; $i++): ?>
                    <a href="?page=all-referentiels&page_num=<?= $i ?>" class="pagination-link <?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
                <?php endfor; ?>
                
                <?php if ($page < $pages): ?>
                    <a href="?page=all-referentiels&page_num=<?= $page + 1 ?>" class="pagination-link">Suivant</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>

<style>
        /* Styles pour les boutons - avec !important pour surcharger d'autres styles */
         </style>