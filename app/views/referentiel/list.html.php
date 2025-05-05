<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>list ref</title>
    <link rel="stylesheet" href="/assets/css/referentiels.css">
</head>
<body>





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


                   value="<?= htmlspecialchars($search ?? '') ?>">
            <button type="submit" class="search-button">Rechercher</button>
        </form>
        
       <div class="option">
       <button class="btn btn-orange" onclick="window.location.href='?page=all-referentiels'">
            <span>📋</span> Tous les référentiels
        </button>
        <button 
            class="btn <?= $current_promotion['etat'] !== 'en cours' ? 'btn-disabled' : 'btn-teal' ?>" 
            <?= $current_promotion['etat'] !== 'en cours' ? 'disabled' : '' ?>
            onclick="window.location.href='?page=assign-referentiels'">
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


        <?php 
        // Filtrer les référentiels en fonction de la recherche
        $filtered_referentiels = $referentiels;
        if (isset($search) && !empty($search)) {
            $filtered_referentiels = array_filter($referentiels, function($ref) use ($search) {
                $search_lower = strtolower($search);
                $name_lower = strtolower($ref['name']);
                $description_lower = strtolower($ref['description']);
                
                return strpos($name_lower, $search_lower) !== false || 
                       strpos($description_lower, $search_lower) !== false;
            });
        }
        
        if (empty($filtered_referentiels)): 
        ?>
            <div class="no-data">
                <?= isset($search) && !empty($search) ? 'Aucun référentiel trouvé pour votre recherche' : 'Aucun référentiel n\'est assigné à cette promotion' ?>
            </div>
        <?php else: ?>

            <?php foreach ($filtered_referentiels as $ref): ?>
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

    /* Styles pour les cartes de référentiels */
    .card-image {
        height: 180px;
        overflow: hidden;
        border-radius: 8px 8px 0 0;
    }
    
    .card-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }
    
    .card:hover .card-image img {
        transform: scale(1.05);
    }
    
    /* Style pour le bouton de recherche */
    .search-button {
        background-color: #f5a623;
        color: white;
        border: none;
        padding: 8px 15px;
        border-radius: 0 4px 4px 0;
        cursor: pointer;
        transition: background-color 0.3s;
    }
    
    .search-button:hover {
        background-color: #e09612;
    }
    
    /* Ajustement du champ de recherche */
    .search-bar {
        display: flex;
        align-items: center;
    }
    
    .search-bar input[type="text"] {
        border-radius: 4px 0 0 4px;
        border-right: none;
    }
</style>





















































</body>

</html>