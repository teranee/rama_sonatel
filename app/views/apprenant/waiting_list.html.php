<div class="container">
    <header>
        <div class="logo">
            <h1>Liste d'attente</h1>
            <span><?= count($waiting_list) ?> apprenants</span>
        </div>
    </header>

    <div class="search-filters">
        <div class="search-bar">
            <input type="text" name="search" placeholder="Rechercher..." value="<?= htmlspecialchars($search ?? '') ?>">
        </div>
        
        <select class="filter-select" name="referentiel">
            <option value="all">Filtre par référentiel</option>
            <?php foreach ($referentiels as $ref): ?>
                <option value="<?= $ref['id'] ?>" <?= isset($referentiel_filter) && $referentiel_filter == $ref['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($ref['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        
        <div style="flex-grow: 1;"></div>
        
        <div class="action-buttons">
            <a href="?page=apprenants" class="btn btn-add">
                <i class="fas fa-list"></i>
                <span>Liste principale</span>
            </a>
        </div>
    </div>

    <div class="tabs">
        <a href="?page=apprenants" class="tab">Liste des apprenants</a>
        <a href="?page=apprenants&waiting_list=true" class="tab active">Liste d'attente</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>Nom Complet</th>
                <th>Email</th>
                <th>Téléphone</th>
                <th>Référentiel</th>
                <th>Raison</th>
                <th>Date d'ajout</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (isset($waiting_list) && !empty($waiting_list)): ?>
                <?php foreach ($waiting_list as $apprenant): ?>
                    <tr>
                        <td><?= htmlspecialchars($apprenant['prenom'] . ' ' . $apprenant['nom']) ?></td>
                        <td><?= htmlspecialchars($apprenant['email']) ?></td>
                        <td><?= htmlspecialchars($apprenant['telephone']) ?></td>
                        <td class="ref-<?= strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $apprenant['referentiel'])) ?>">
                            <?= htmlspecialchars($apprenant['referentiel']) ?>
                        </td>
                        <td><?= htmlspecialchars($apprenant['raison']) ?></td>
                        <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($apprenant['date_ajout']))) ?></td>
                        <td class="actions-menu">
                            <div class="dropdown">
                                <span class="dropdown-toggle">•••</span>
                                <div class="dropdown-menu">
                                    <a href="?page=approve-waiting-apprenant&id=<?= $apprenant['id'] ?>">Approuver</a>
                                    <a href="?page=edit-waiting-apprenant&id=<?= $apprenant['id'] ?>">Modifier</a>
                                    <form action="?page=apprenants" method="post" class="delete-form" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet apprenant de la liste d\'attente ?');">
                                        <input type="hidden" name="action" value="delete_waiting">
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
                    <td colspan="7" class="text-center">Aucun apprenant en liste d'attente</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="pagination">
        <div class="pagination-info">
            <span>Apprenants/page</span>
            <form action="?page=apprenants" method="GET" style="display: inline;">
                <input type="hidden" name="page" value="apprenants">
                <input type="hidden" name="waiting_list" value="true">
                <input type="hidden" name="search" value="<?= htmlspecialchars($search ?? '') ?>">
                <input type="hidden" name="referentiel" value="<?= htmlspecialchars($referentiel_filter ?? 'all') ?>">
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
            $total_items = $total_items ?? count($waiting_list);
            $current_page = $current_page ?? 1;
            $total_pages = $total_pages ?? ceil($total_items / $items_per_page);
            ?>
            <?= ($offset + 1) ?> à <?= min($offset + $items_per_page, $total_items) ?> apprenants pour <?= $total_items ?>
        </div>
        
        <div class="pagination-controls">
            <?php if ($current_page > 1): ?>
                <a href="?page=apprenants&waiting_list=true&page_num=<?= $current_page - 1 ?>&search=<?= htmlspecialchars($search ?? '') ?>&referentiel=<?= htmlspecialchars($referentiel_filter ?? 'all') ?>&items_per_page=<?= $items_per_page ?>" class="page-btn nav"><</a>
            <?php else: ?>
                <div class="page-btn nav disabled"><</div>
            <?php endif; ?>
            
            <?php
            // Calculate which page numbers to show
            $start_page = max(1, $current_page - 2);
            $end_page = min($total_pages, $current_page + 2);
            
            // Always show first page
            if ($start_page > 1) {
                echo '<a href="?page=apprenants&waiting_list=true&page_num=1&search=' . htmlspecialchars($search ?? '') . '&referentiel=' . htmlspecialchars($referentiel_filter ?? 'all') . '&items_per_page=' . $items_per_page . '" class="page-btn">1</a>';
                if ($start_page > 2) {
                    echo '<div class="page-btn">...</div>';
                }
            }
            
            // Show page numbers
            for ($i = $start_page; $i <= $end_page; $i++) {
                $active = $i == $current_page ? 'active' : '';
                echo '<a href="?page=apprenants&waiting_list=true&page_num=' . $i . '&search=' . htmlspecialchars($search ?? '') . '&referentiel=' . htmlspecialchars($referentiel_filter ?? 'all') . '&items_per_page=' . $items_per_page . '" class="page-btn ' . $active . '">' . $i . '</a>';
            }
            
            // Always show last page
            if ($end_page < $total_pages) {
                if ($end_page < $total_pages - 1) {
                    echo '<div class="page-btn">...</div>';
                }
                echo '<a href="?page=apprenants&waiting_list=true&page_num=' . $total_pages . '&search=' . htmlspecialchars($search ?? '') . '&referentiel=' . htmlspecialchars($referentiel_filter ?? 'all') . '&items_per_page=' . $items_per_page . '" class="page-btn">' . $total_pages . '</a>';
            }
            ?>
            
            <?php if ($current_page < $total_pages): ?>
                <a href="?page=apprenants&waiting_list=true&page_num=<?= $current_page + 1 ?>&search=<?= htmlspecialchars($search ?? '') ?>&referentiel=<?= htmlspecialchars($referentiel_filter ?? 'all') ?>&items_per_page=<?= $items_per_page ?>" class="page-btn nav">></a>
            <?php else: ?>
                <div class="page-btn nav disabled">></div>
            <?php endif; ?>
        </div>
    </div>
</div>