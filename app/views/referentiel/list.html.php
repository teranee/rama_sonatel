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
        <form action="" method="GET" class="search-bar">
            <div class="search-icon">🔍</div>
            <input type="hidden" name="page" value="referentiels">
            <input type="text" 
                   name="search" 
                   placeholder="Rechercher un référentiel..." 
                   value="<?= htmlspecialchars($search ?? '') ?>"
                   oninput="searchReferentiels(this.value)">
        </form>
        
       <div class="option">
       <button class="btn btn-orange" onclick="window.location.href='?page=all-referentiels'">
            <span>📋</span> Tous les référentiels
        </button>
        <button 
            class="btn <?= $current_promotion['etat'] !== 'en cours' ? 'btn-disabled' : 'btn-teal' ?>" 
            <?= $current_promotion['etat'] !== 'en cours' ? 'disabled' : '' ?>
            onclick="window.location.href='?page=manage-promos'">
            <span>⚙</span> Gérer les référentiels
        </button>
        <?php if ($current_promotion['etat'] !== 'en cours'): ?>
            <div class="alert alert-danger error-banner">
                Impossible de gérer les référentiels : la promotion n'est pas en cours.
            </div>
        <?php endif; ?>
       </div>
    </div>
    
    <div class="cards-container">
        <?php if (empty($referentiels)): ?>
            <div class="no-data">Aucun référentiel n'est assigné à cette promotion</div>
        <?php else: ?>
            <?php foreach ($referentiels as $ref): ?>
                <div class="card">
                    <div class="card-image">
                        <img src="<?= $ref['image'] ? '/' . $ref['image'] : '/assets/images/referentiels/default.jpg' ?>" 
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

<style>
   
</style>

<script>
let searchTimeout;

function searchReferentiels(query) {
    clearTimeout(searchTimeout);
    
    searchTimeout = setTimeout(() => {
        const container = document.querySelector('.cards-container');
        const cards = container.querySelectorAll('.card');
        
        if (query.length === 0) {
            cards.forEach(card => card.style.display = 'block');
            return;
        }
        
        cards.forEach(card => {
            const title = card.querySelector('.card-title').textContent.toLowerCase();
            const description = card.querySelector('.card-description').textContent.toLowerCase();
            query = query.toLowerCase();
            
            if (title.includes(query) || description.includes(query)) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
        
        // Afficher le message "Aucun résultat" si nécessaire
        const visibleCards = container.querySelectorAll('.card[style="display: block"]');
        const noDataMessage = container.querySelector('.no-data');
        
        if (visibleCards.length === 0) {
            if (!noDataMessage) {
                const message = document.createElement('div');
                message.className = 'no-data';
                message.textContent = 'Aucun référentiel trouvé';
                container.appendChild(message);
            }
        } else if (noDataMessage) {
            noDataMessage.remove();
        }
    }, 300); // Délai de 300ms pour éviter trop de recherches
}

// Optionnel : Soumettre le formulaire quand on appuie sur Entrée
document.querySelector('.search-bar').addEventListener('submit', function(e) {
    e.preventDefault();
    const query = this.querySelector('input[name="search"]').value;
    window.location.href = `?page=referentiels&search=${encodeURIComponent(query)}`;
});
</script>

</body>

</html>