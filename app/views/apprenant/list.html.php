<style>

</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<link rel="stylesheet" href="/assets/css/apprenants.css">
<div class="container">
    <header>
        <div class="logo">
            <h1>Apprenants</h1>
            <span><?= count($apprenants) ?> apprenants</span>
        </div>
    </header>

    <form action="?page=apprenants" method="GET" class="search-filters">
        <input type="hidden" name="page" value="apprenants">
        
        <div class="search-bar">
            <input type="text" name="search" placeholder="Rechercher..." value="<?= htmlspecialchars($search ?? '') ?>">
        </div>
        
        <select class="filter-select" name="referentiel" onchange="this.form.submit()">
            <option value="all">Filtre par référentiel</option>
            <?php foreach ($referentiels as $ref): ?>
                <option value="<?= $ref['id'] ?>" <?= isset($referentiel_filter) && $referentiel_filter == $ref['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($ref['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        
        <select class="filter-select" name="status" onchange="this.form.submit()">
            <option value="all">Filtre par statut</option>
            <option value="Actif" <?= isset($status_filter) && $status_filter == 'Actif' ? 'selected' : '' ?>>Actif</option>
            <option value="Renvoyé" <?= isset($status_filter) && $status_filter == 'Renvoyé' ? 'selected' : '' ?>>Renvoyé</option>
         </select>
        
        <div style="flex-grow: 1;"></div>
        
       <div class="action-buttons">
            <div class="dropdown">
                <a class="btn btn-download">
                    <i class="fas fa-download"></i> 
                    <span>Télécharger la liste</span>
                </a>
                <div class="dropdown-menu">
                    <a href="?page=export-apprenants-pdf<?= !empty($search) ? '&search='.htmlspecialchars($search) : '' ?><?= isset($referentiel_filter) && $referentiel_filter !== 'all' ? '&referentiel='.htmlspecialchars($referentiel_filter) : '' ?><?= isset($status_filter) && $status_filter !== 'all' ? '&status='.htmlspecialchars($status_filter) : '' ?><?= isset($_GET['waiting_list']) ? '&waiting_list=true' : '' ?>">Format PDF</a>
                    <a href="?page=export-apprenants-excel<?= !empty($search) ? '&search='.htmlspecialchars($search) : '' ?><?= isset($referentiel_filter) && $referentiel_filter !== 'all' ? '&referentiel='.htmlspecialchars($referentiel_filter) : '' ?><?= isset($status_filter) && $status_filter !== 'all' ? '&status='.htmlspecialchars($status_filter) : '' ?><?= isset($_GET['waiting_list']) ? '&waiting_list=true' : '' ?>">Format Excel</a>
                </div>
            </div>
            <a href="?page=import-apprenants-form" class="btn btn-download">
                <i class="fas fa-upload"></i> 
                <span>Importer</span>
            </a>
            <a href="?page=add-apprenant-form" class="btn btn-add">
                <i class="fas fa-plus"></i> 
                <span>Ajouter apprenant</span>
            </a>
        </div> 
      <!--   <div class="action-buttons">
    <div class="dropdown">
        <button class="btn btn-download">
            <span>Télécharger la liste</span>
            <span>⬇️</span>
        </button>
        <div class="dropdown-menu">
            <a href="?page=export-apprenants-pdf<?= !empty($search) ? '&search='.htmlspecialchars($search) : '' ?><?= $referentiel_filter !== 'all' ? '&referentiel='.htmlspecialchars($referentiel_filter) : '' ?><?= $statut_filter !== 'all' ? '&status='.htmlspecialchars($statut_filter) : '' ?>">Format PDF</a>
            <a href="?page=export-apprenants-excel<?= !empty($search) ? '&search='.htmlspecialchars($search) : '' ?><?= $referentiel_filter !== 'all' ? '&referentiel='.htmlspecialchars($referentiel_filter) : '' ?><?= $statut_filter !== 'all' ? '&status='.htmlspecialchars($statut_filter) : '' ?>">Format Excel</a>
        </div>
    </div>
    <a href="?page=add-apprenant-form" class="btn btn-add">
        <span>Ajouter apprenant</span>
        <span>➕</span>
    </a>
</div> -->
    </form>

    <div class="tabs">
        <a href="?page=apprenants" class="tab <?= !isset($_GET['waiting_list']) ? 'active' : '' ?>">Liste des apprenants</a>
        <a href="?page=apprenants&waiting_list=true" class="tab <?= isset($_GET['waiting_list']) ? 'active' : '' ?>">Liste d'attente</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>Photo</th>
                <th>Matricule</th>
                <th>Nom Complet</th>
                <th>Adresse</th>
                <th>Téléphone</th>
                <th>Référentiel</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (isset($apprenants) && !empty($apprenants)): ?>
                <?php foreach ($apprenants as $apprenant): ?>
                    <tr>
                        <td>
                            <div class="profile-pic">
                                <img src="<?= !empty($apprenant['photo']) ? $apprenant['photo'] : 'assets/images/default-avatar.png' ?>" alt="Photo de <?= htmlspecialchars($apprenant['prenom'] . ' ' . $apprenant['nom']) ?>">
                            </div>
                        </td>
                        <td><?= htmlspecialchars($apprenant['matricule']) ?></td>
                        <td><?= htmlspecialchars($apprenant['prenom'] . ' ' . $apprenant['nom']) ?></td>
                        <td><?= htmlspecialchars($apprenant['adresse']) ?></td>
                        <td><?= htmlspecialchars($apprenant['telephone']) ?></td>
                        <td class="ref-<?= strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $apprenant['referentiel'])) ?>">
                            <?= htmlspecialchars($apprenant['referentiel']) ?>
                        </td>
                        <td>
                            <span class="status status-<?= strtolower($apprenant['statut']) === 'actif' ? 'active' : 'removed' ?>">
                                <?= htmlspecialchars($apprenant['statut']) ?>
                            </span>
                        </td>
                        <td class="actions-menu">
                            <div class="dropdown">
                                <span class="dropdown-toggle">•••</span>
                                <div class="dropdown-menu">
                                    <a href="?page=apprenant-details&id=<?= $apprenant['id'] ?>">Voir détails</a>
                                    <a href="?page=edit-apprenant-form&id=<?= $apprenant['id'] ?>">Modifier</a>
                                    <form action="?page=apprenants" method="post" class="delete-form" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet apprenant ?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="apprenant_id" value="<?= $apprenant['id'] ?>">
                                        <button type="submit" class="menu-button">Supprimer</button>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" class="text-center">Aucun apprenant trouvé</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="pagination">
        <div class="pagination-info">
            <span>Apprenants/page</span>
            <form action="?page=apprenants" method="GET" style="display: inline;">
                <input type="hidden" name="page" value="apprenants">
                <input type="hidden" name="search" value="<?= htmlspecialchars($search ?? '') ?>">
                <input type="hidden" name="referentiel" value="<?= htmlspecialchars($referentiel_filter ?? 'all') ?>">
                <input type="hidden" name="status" value="<?= htmlspecialchars($status_filter ?? 'all') ?>">
                <select name="items_per_page" onchange="this.form.submit()" style="margin-left: 5px; padding: 2px 5px; border: 1px solid #ddd; border-radius: 4px;">
                    <option value="10" <?= isset($items_per_page) && $items_per_page == 10 ? 'selected' : '' ?>>10</option>
                    <option value="20" <?= isset($items_per_page) && $items_per_page == 20 ? 'selected' : '' ?>>20</option>
                    <option value="50" <?= isset($items_per_page) && $items_per_page == 50 ? 'selected' : '' ?>>50</option>
                </select>
            </form>
        </div>
        
        <div style="color: #888;">
            <?php
            // Définir des valeurs par défaut si les variables ne sont pas définies
            $items_per_page = $items_per_page ?? 10;
            $offset = $offset ?? 0;
            $total_items = $total_items ?? count($apprenants);
            $current_page = $current_page ?? 1;
            $total_pages = $total_pages ?? ceil($total_items / $items_per_page);
            ?>
            <?= ($offset + 1) ?> à <?= min($offset + $items_per_page, $total_items) ?> apprenants pour <?= $total_items ?>
        </div>
        
        <div class="pagination-controls">
            <?php if ($current_page > 1): ?>
                <a href="?page=apprenants&page_num=<?= $current_page - 1 ?>&search=<?= htmlspecialchars($search ?? '') ?>&referentiel=<?= htmlspecialchars($referentiel_filter ?? 'all') ?>&status=<?= htmlspecialchars($status_filter ?? 'all') ?>&items_per_page=<?= $items_per_page ?>" class="page-btn nav"><</a>
            <?php else: ?>
                <div class="page-btn nav disabled"><</div>
            <?php endif; ?>
            
            <?php
            // Calculate which page numbers to show
            $start_page = max(1, $current_page - 2);
            $end_page = min($total_pages, $current_page + 2);
            
            // Always show first page
            if ($start_page > 1) {
                echo '<a href="?page=apprenants&page_num=1&search=' . htmlspecialchars($search ?? '') . '&referentiel=' . htmlspecialchars($referentiel_filter ?? 'all') . '&status=' . htmlspecialchars($status_filter ?? 'all') . '&items_per_page=' . $items_per_page . '" class="page-btn">1</a>';
                if ($start_page > 2) {
                    echo '<div class="page-btn">...</div>';
                }
            }
            
            // Show page numbers
            for ($i = $start_page; $i <= $end_page; $i++) {
                $active = $i == $current_page ? 'active' : '';
                echo '<a href="?page=apprenants&page_num=' . $i . '&search=' . htmlspecialchars($search ?? '') . '&referentiel=' . htmlspecialchars($referentiel_filter ?? 'all') . '&status=' . htmlspecialchars($status_filter ?? 'all') . '&items_per_page=' . $items_per_page . '" class="page-btn ' . $active . '">' . $i . '</a>';
            }
            
            // Always show last page
            if ($end_page < $total_pages) {
                if ($end_page < $total_pages - 1) {
                    echo '<div class="page-btn">...</div>';
                }
                echo '<a href="?page=apprenants&page_num=' . $total_pages . '&search=' . htmlspecialchars($search ?? '') . '&referentiel=' . htmlspecialchars($referentiel_filter ?? 'all') . '&status=' . htmlspecialchars($status_filter ?? 'all') . '&items_per_page=' . $items_per_page . '" class="page-btn">' . $total_pages . '</a>';
            }
            ?>
            
            <?php if ($current_page < $total_pages): ?>
                <a href="?page=apprenants&page_num=<?= $current_page + 1 ?>&search=<?= htmlspecialchars($search ?? '') ?>&referentiel=<?= htmlspecialchars($referentiel_filter ?? 'all') ?>&status=<?= htmlspecialchars($status_filter ?? 'all') ?>&items_per_page=<?= $items_per_page ?>" class="page-btn nav">></a>
            <?php else: ?>
                <div class="page-btn nav disabled">></div>
            <?php endif; ?>
        </div>
    </div>
</div>