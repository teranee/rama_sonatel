<div class="container">
    <head>
        <link rel="stylesheet" href="/assets/css/assign.css">
    </head>
    <div class="header">
        <h1>Assigner des référentiels à <?= htmlspecialchars($current_promotion['name']) ?></h1>
        <a href="?page=referentiels" class="btn btn-back">Retour</a>
    </div>

    <form action="?page=assign-referentiels-process" method="POST">
        <div class="search-section">
            <div class="search-bar">
                <div class="search-icon">🔍</div>
                <input type="text" id="search" name="search" placeholder="Rechercher un référentiel...">
            </div>
        </div>

        <div class="cards-container">
            <?php if (empty($unassigned_referentiels)): ?>
                <div class="no-data">Aucun référentiel disponible pour l'assignation</div>
            <?php else: ?>
                <?php foreach ($unassigned_referentiels as $ref): ?>
                    <div class="card">
                        <div class="card-image">
                            <img src="<?= $ref['image'] ?? 'assets/images/referentiels/default.jpg' ?>" 
                                 alt="<?= htmlspecialchars($ref['name']) ?>"
                                 style="width: 20%; height: 200px; object-fit: cover;">
                        </div>
                        <div class="card-content">
                            <h3 class="card-title"><?= htmlspecialchars($ref['name']) ?></h3>
                            <p class="card-subtitle"><?= count($ref['modules'] ?? []) ?> modules</p>
                            <p class="card-description"><?= htmlspecialchars($ref['description']) ?></p>
                            <div class="form-check">
                                <input type="checkbox" name="referentiels[]" value="<?= $ref['id'] ?>" 
                                       id="ref_<?= $ref['id'] ?>">
                                <label for="ref_<?= $ref['id'] ?>">Sélectionner</label>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <?php if (!empty($unassigned_referentiels)): ?>
            <div class="action-buttons">
                <button type="submit" class="btn btn-teal">
                    <span>✓</span> Assigner les référentiels sélectionnés
                </button>
            </div>
        <?php endif; ?>
    </form>
</div>

<!-- Pagination -->
<?php if ($pages > 1): ?>
    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?page=assign-referentiels&page_num=<?= $page - 1 ?>" class="pagination-link">Précédent</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $pages; $i++): ?>
            <a href="?page=assign-referentiels&page_num=<?= $i ?>" class="pagination-link <?= $i === $page ? 'active' : '' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>

        <?php if ($page < $pages): ?>
            <a href="?page=assign-referentiels&page_num=<?= $page + 1 ?>" class="pagination-link">Suivant</a>
        <?php endif; ?>
    </div>
<?php endif; ?>