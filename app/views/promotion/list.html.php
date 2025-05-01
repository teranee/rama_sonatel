<!DOCTYPE html>

<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Gestion des promotions</title>
    <link rel="stylesheet" href="/assets/css/promo.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
<?php 
$current_view = $view_mode ?? 'grid';
$current_page = $current_page ?? 1;
$total_pages = $total_pages ?? 1;
$search = $search ?? '';
?>

<div class="container <?= $current_view === 'list' ? 'show-list' : '' ?>">
    <div class="header">
        <div class="header-title">
            <div class="title-container">
                <h1>Promotion</h1>
                <?php if ($current_view === 'list'): ?>
                    <span class="apprenant-count"><?= $page_stats['total_apprenants'] ?> apprenants</span>
                <?php endif; ?>
            </div>
            <p class="header-subtitle">Gérer les promotions de l'école</p>
        </div>

        <a href="?page=add_promotion_form" class="add-button">
            <i class="fas fa-plus"></i> Ajouter une promotion
        </a>
    </div>


    <?php if ($current_view === 'list'): ?>


    <!-- Filtres spécifiques au mode liste -->
    <div class="filters">
        <div class="search-filter">
            <form action="?page=promotions" method="GET" class="search-form-list">
                <input type="hidden" name="page" value="promotions">
                <input type="hidden" name="view" value="list">
                <?php if (isset($_GET['referentiel'])): ?>
                <input type="hidden" name="referentiel" value="<?= htmlspecialchars($_GET['referentiel']) ?>">
                <?php endif; ?>
                <?php if (isset($_GET['status'])): ?>
                <input type="hidden" name="status" value="<?= htmlspecialchars($_GET['status']) ?>">
                <?php endif; ?>
                <i class="fas fa-search"></i>
                <input type="search" 
                       name="search" 
                       placeholder="Rechercher une promotion..."
                       value="<?= htmlspecialchars($search) ?>">
            </form>
        </div>
        
        <select class="dropdown-filter" onchange="window.location.href=this.value">
            <option value="?page=promotions&view=list&search=<?= htmlspecialchars($search) ?><?= isset($_GET['status']) ? '&status='.htmlspecialchars($_GET['status']) : '' ?>">Tous les référentiels</option>
            <?php foreach ($referentiels as $ref): ?>
            <option value="?page=promotions&view=list&search=<?= htmlspecialchars($search) ?><?= isset($_GET['status']) ? '&status='.htmlspecialchars($_GET['status']) : '' ?>&referentiel=<?= $ref['id'] ?>" 
                <?= (isset($_GET['referentiel']) && $_GET['referentiel'] == $ref['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($ref['name']) ?>
            </option>
            <?php endforeach; ?>
        </select>
        
        <select class="dropdown-filter" onchange="window.location.href=this.value">
            <option value="?page=promotions&view=list&search=<?= htmlspecialchars($search) ?><?= isset($_GET['referentiel']) ? '&referentiel='.htmlspecialchars($_GET['referentiel']) : '' ?>">Tous les statuts</option>
            <option value="?page=promotions&view=list&search=<?= htmlspecialchars($search) ?>&status=active<?= isset($_GET['referentiel']) ? '&referentiel='.htmlspecialchars($_GET['referentiel']) : '' ?>"
                <?= (isset($_GET['status']) && $_GET['status'] === 'active') ? 'selected' : '' ?>>
                Active
            </option>
            <option value="?page=promotions&view=list&search=<?= htmlspecialchars($search) ?>&status=inactive<?= isset($_GET['referentiel']) ? '&referentiel='.htmlspecialchars($_GET['referentiel']) : '' ?>"
                <?= (isset($_GET['status']) && $_GET['status'] === 'inactive') ? 'selected' : '' ?>>
                Inactives
            </option>
        </select>
    </div>
    <?php endif; ?>


    <!-- Statistiques avec style différent selon le mode -->
    <div class="stats-container">
        <?php if ($current_view === 'grid'): ?>
            <!-- Style original pour le mode grille -->
            <div class="stat-card">
                <h3><?= $page_stats['total_apprenants'] ?></h3>
                <p>Apprenants actifs</p>
            </div>
            <div class="stat-card">
                <h3><?= $page_stats['total_referentiels'] ?></h3>
                <p>Référentiels</p>
            </div>
            <div class="stat-card">
                <h3><?= $page_stats['active_promotions'] ?></h3>
                <p>Promotions actives</p>
            </div>
            <div class="stat-card">
                <h3><?= $page_stats['total_promotions'] ?></h3>
                <p>Total promotions</p>
            </div>
        <?php else: ?>
            <!-- Style nouveau pour le mode liste -->
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <div>
                    <div class="stat-number"><?= $page_stats['total_apprenants'] ?></div>
                    <div class="stat-label">Apprenants</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-book"></i>
                </div>
                <div>
                    <div class="stat-number"><?= $page_stats['total_referentiels'] ?></div>
                    <div class="stat-label">Référentiels</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-flag"></i>
                </div>
                <div>
                    <div class="stat-number"><?= $page_stats['active_promotions'] ?></div>
                    <div class="stat-label">Promotions actives</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div>
                    <div class="stat-number"><?= $page_stats['total_promotions'] ?></div>
                    <div class="stat-label">Total promotions</div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($current_view !== 'list'): ?>
    <!-- Contrôles de recherche et de vue pour le mode grille -->
    <div class="controls-container">
        <form action="?page=promotions" method="GET" class="search-form">
            <input type="hidden" name="page" value="promotions">
            <input type="hidden" name="view" value="<?= $current_view ?>">
            <input type="hidden" name="status" value="<?= htmlspecialchars($status_filter) ?>">
            <input type="search" 
                   name="search" 
                   placeholder="Rechercher une promotion..."
                   value="<?= htmlspecialchars($search) ?>">
            <button type="submit">
                <i class="fas fa-search"></i>
            </button>
        </form>

        <div class="view-controls">
            <!-- Filtre de statut pour le mode grille -->
            <select class="status-filter" onchange="window.location.href=this.value">
                <option value="?page=promotions&view=grid&search=<?= htmlspecialchars($search) ?>&status=all" 
                    <?= $status_filter === 'all' ? 'selected' : '' ?>>
                    Tous les statuts
                </option>
                <option value="?page=promotions&view=grid&search=<?= htmlspecialchars($search) ?>&status=active" 
                    <?= $status_filter === 'active' ? 'selected' : '' ?>>
                    Active
                </option>
                <option value="?page=promotions&view=grid&search=<?= htmlspecialchars($search) ?>&status=inactive" 
                    <?= $status_filter === 'inactive' ? 'selected' : '' ?>>
                    Inactives
                </option>
            </select>

            <a href="?page=promotions&view=grid&search=<?= htmlspecialchars($search) ?>&status=<?= htmlspecialchars($status_filter) ?>" 
               class="view-button <?= $current_view === 'grid' ? 'active' : '' ?>">
                <i class="fas fa-th"></i> Grille
            </a>
            <a href="?page=promotions&view=list&search=<?= htmlspecialchars($search) ?>&status=<?= htmlspecialchars($status_filter) ?>" 
               class="view-button <?= $current_view === 'list' ? 'active' : '' ?>">
                <i class="fas fa-list"></i> Liste
            </a>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($current_view === 'list'): ?>
    <!-- Affichage en mode liste (tableau) -->
    <table class="promotions-table">
        <thead>
            <tr>
                <th>Photo</th>
                <th>Promotion</th>
                <th>Date de début</th>
                <th>Date de fin</th>
                <th>Référentiel</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($promotions)): ?>
                <tr>
                    <td colspan="7" class="no-results">Aucune promotion trouvée</td>
                </tr>
            <?php else: ?>
                <?php foreach ($promotions as $promotion): ?>
                <tr>
                    <td>
                        <img src="<?= isset($promotion['image']) ? '/assets/images/uploads/promotions/' . $promotion['image'] : '/assets/images/default-promotion.jpg' ?>" 
                             alt="<?= htmlspecialchars($promotion['name']) ?>" 
                             class="profile-img">
                    </td>
                    <td><?= htmlspecialchars($promotion['name']) ?></td>
                    <td><?= isset($promotion['date_debut']) ? $promotion['date_debut'] : 'Non définie' ?></td>
                    <td><?= isset($promotion['date_fin']) ? $promotion['date_fin'] : 'Non définie' ?></td>
                    <td class="referentiels-cell">
                        <?php if (!empty($promotion['referentiels'])): ?>
                            <?php 
                            $colors = ['dev', 'ref', 'data', 'aws', 'hack'];
                            $color_index = 0;
                            foreach ($promotion['referentiels'] as $ref_id): 
                                $ref_name = "Référentiel";
                                foreach ($referentiels as $ref) {
                                    if ($ref['id'] == $ref_id) {
                                        $ref_name = $ref['name'];
                                        break;
                                    }
                                }
                                $color_class = $colors[$color_index % count($colors)];
                                $color_index++;
                            ?>
                                <span class="tech-tag tag-<?= $color_class ?>"><?= htmlspecialchars($ref_name) ?></span>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <span class="no-referentiel">Aucun référentiel</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="status-<?= $promotion['status'] === 'active' ? 'active' : 'inactive' ?>">
                            <span class="status-dot"></span>
                            <?= $promotion['status'] === 'active' ? 'Active' : 'Inactive' ?>
                        </div>
                    </td>
                    <td class="actions">
                        <div class="hamburger-menu">
                            <i class="fas fa-ellipsis-v"></i>
                            <div class="hamburger-menu-content">
                                <a href="?page=promotion&action=details&id=<?= $promotion['id'] ?>">Voir détails</a>
                                <a href="?page=promotion&action=edit&id=<?= $promotion['id'] ?>">Modifier</a>
                                <form action="?page=promotions" method="POST" class="status-form">
                                    <input type="hidden" name="action" value="toggle_status">
                                    <input type="hidden" name="promotion_id" value="<?= $promotion['id'] ?>">
                                    <button type="submit" class="menu-button">
                                        <?= $promotion['status'] === 'active' ? 'Désactiver' : 'Activer' ?>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    <?php else: ?>
    <!-- Affichage en mode grille -->
    <div class="promotions-grid">
        <?php if (empty($promotions)): ?>
            <div class="no-results">Aucune promotion trouvée</div>
        <?php else: ?>
            <?php foreach ($promotions as $promotion): ?>
                <div class="promotion-card">
                    <div class="promotion-header">
                        <img src="<?= isset($promotion['image']) ? '/assets/images/uploads/promotions/' . $promotion['image'] : '/assets/images/default-promotion.jpg' ?>" 
                             alt="<?= htmlspecialchars($promotion['name']) ?>" 
                             class="promotion-avatar">
                        <div class="status-container">
                            <form action="?page=promotions" method="POST" class="status-form">
                                <input type="hidden" name="action" value="toggle_status">
                                <input type="hidden" name="promotion_id" value="<?= $promotion['id'] ?>">
                                <button type="submit" class="status-toggle" <?= $promotion['status'] === 'active' ? 'disabled' : '' ?>>
                                    <span class="status-text <?= $promotion['status'] === 'active' ? 'active' : 'inactive' ?>">
                                        <?= $promotion['status'] === 'active' ? 'Active' : 'Inactive' ?>
                                    </span>
                                    <i class="fas fa-power-off"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <div class="promotion-content">
                        <h3><?= htmlspecialchars($promotion['name']) ?></h3>
                        <p class="promotion-date">
                            <?= isset($promotion['date_debut']) ? $promotion['date_debut'] : 'Non définie' ?> - 
                            <?= isset($promotion['date_fin']) ? $promotion['date_fin'] : 'Non définie' ?>
                        </p>
                        <div class="promotion-students">
                            <i class="fas fa-users"></i>
                            <?= $promotion['nb_apprenants'] ?? 0 ?> apprenants
                        </div>
                    </div>

                    <div class="promotion-footer">
                        <a href="?page=promotion&action=details&id=<?= $promotion['id'] ?>" class="voir-details">
                            Voir détails <i class="fas fa-chevron-right"></i>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- Pagination avec informations détaillées -->
    <div class="pagination">
        <?php if ($current_view === 'list'): ?>
            <!-- Suppression de l'information "page X de Y pour Z" -->
        <?php endif; ?>
    
        <div class="page-controls">
            <?php if ($total_pages > 1): ?>
                <?php if ($current_page > 1): ?>
                    <a href="?page=promotions&page_num=<?= $current_page - 1 ?>&view=<?= $current_view ?>&search=<?= htmlspecialchars($search) ?><?= isset($_GET['status']) ? '&status='.htmlspecialchars($_GET['status']) : '' ?><?= isset($_GET['referentiel']) ? '&referentiel='.htmlspecialchars($_GET['referentiel']) : '' ?>" 
                       class="page-button prev">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=promotions&page_num=<?= $i ?>&view=<?= $current_view ?>&search=<?= htmlspecialchars($search) ?><?= isset($_GET['status']) ? '&status='.htmlspecialchars($_GET['status']) : '' ?><?= isset($_GET['referentiel']) ? '&referentiel='.htmlspecialchars($_GET['referentiel']) : '' ?>" 
                       class="page-button <?= $i === $current_page ? 'active' : '' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>

                <?php if ($current_page < $total_pages): ?>
                    <a href="?page=promotions&page_num=<?= $current_page + 1 ?>&view=<?= $current_view ?>&search=<?= htmlspecialchars($search) ?><?= isset($_GET['status']) ? '&status='.htmlspecialchars($_GET['status']) : '' ?><?= isset($_GET['referentiel']) ? '&referentiel='.htmlspecialchars($_GET['referentiel']) : '' ?>" 
                       class="page-button next">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>
