<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: Arial, sans-serif;
}

body {
    background-color: #f9f9f9;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.logo {
    display: flex;
    align-items: center;
}

.logo h1 {
    color: #11a683;
    font-size: 24px;
    margin-right: 10px;
}

.logo span {
    background-color: #f8f0e5;
    color: #f5a623;
    padding: 4px 8px;
    border-radius: 15px;
    font-size: 12px;
}

.search-filters {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
}

.search-bar {
    position: relative;
    width: 240px;
}

.search-bar input {
    width: 100%;
    padding: 10px 10px 10px 35px;
    border: 1px solid #ddd;
    border-radius: 4px;
    outline: none;
}

.search-bar::before {
    content: "🔍";
    position: absolute;
    left: 10px;
    top: 50%;
    transform: translateY(-50%);
    color: #aaa;
}

.filter-select {
    width: 170px;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    background-color: white;
    color: #888;
    appearance: none;
    background-image: url("data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23888%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E");
    background-repeat: no-repeat;
    background-position: right 10px center;
    background-size: 10px;
}

.action-buttons {
    display: flex;
    gap: 10px;
}

.btn {
    padding: 4px 15px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    display: flex;
    gap: 5px;
    text-decoration: none;
  text-align: center;
}
.btn :hover {
    scale: 1.05;
    color: rgba(255, 255, 255, 0.84) ;
}

.btn-download {
    background-color: #1e1e1e;
    color: white;
}
.btn-download :hover {
 
    background-color:rgba(30, 30, 30, 0.69) ;
}

.btn-add {
    background-color: #11a683;
    color: white;
}

.tabs {
    display: flex;
    margin-bottom: 0;
    border-bottom: 2px solid #eee;
}

.tab {
    padding: 15px;
    cursor: pointer;
    font-weight: bold;
    color: #888;
    text-decoration: none;
}

.tab.active {
    color: #11a683;
    border-bottom: 3px solid #f5a623;
    margin-bottom: -2px;
}

table {
    width: 100%;
    border-collapse: collapse;
    background-color: white;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
}

th {
    background-color: #f5a623;
    color: white;
    text-align: left;
    padding: 15px;
    font-weight: bold;
}

tr {
    border-bottom: 1px solid #eee;
}

td {
    padding: 15px;
    color: #333;
}

td.ref-dev, td.ref-devwebmobile {
    color: #11a683;
    font-weight: bold;
}

td.ref-dig, td.ref-refdig {
    color: #4a90e2;
    font-weight: bold;
}

td.ref-data, td.ref-devdata {
    color: #9013fe;
    font-weight: bold;
}

td.ref-aws {
    color: #f5a623;
    font-weight: bold;
}

td.ref-hackeuse {
    color: #e91e63;
    font-weight: bold;
}

.status {
    padding: 5px 10px;
    border-radius: 15px;
    font-size: 12px;
    font-weight: bold;
    display: inline-block;
    text-align: center;
    min-width: 80px;
}

.status-active {
    background-color: #d4f5e9;
    color: #11a683;
}

.status-removed {
    background-color: #ffdfdf;
    color: #e53935;
}

.actions-menu {
    text-align: center;
    color: #888;
    font-weight: bold;
    cursor: pointer;
    position: relative;
}

.dropdown {
    position: relative;
    display: inline-block;
}

.dropdown-toggle {
    cursor: pointer;
}

.dropdown-menu {
    display: none;
    position: absolute;
    left: 0;
    top: 100%;
    background-color: white;
    min-width: 160px;
    box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
    z-index: 1;
    border-radius: 4px;
    overflow: hidden;
}

.dropdown:hover .dropdown-menu {
    display: block;
}

.dropdown-menu a, .dropdown-menu button {
    color: #333;
    padding: 12px 16px;
    text-decoration: none;
    display: block;
    text-align: left;
    font-weight: normal;
    border: none;
    background: none;
    width: 100%;
    cursor: pointer;
    font-size: 14px;
}

.dropdown-menu a:hover, .dropdown-menu button:hover {
    background-color: #f1f1f1;
}

.profile-pic {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background-color: #eee;
    overflow: hidden;
}

.profile-pic img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.pagination {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 20px;
}

.pagination-info {
    color: #888;
    font-size: 14px;
}

.pagination-controls {
    display: flex;
    align-items: center;
    gap: 5px;
}

.page-btn {
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid #ddd;
    background-color: white;
    cursor: pointer;
    border-radius: 4px;
    text-decoration: none;
    color: #333;
}

.page-btn.active {
    background-color: #f5a623;
    color: white;
    border-color: #f5a623;
}

.page-btn.nav {
    color: #888;
}

.page-btn.disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.text-center {
    text-align: center;
}

/* Styles responsifs pour les écrans plus petits */
@media (max-width: 1024px) {
    .search-filters {
        flex-wrap: wrap;
    }
    
    .search-bar {
        width: 100%;
        margin-bottom: 10px;
    }
    
    .filter-select {
        flex-grow: 1;
    }
    
    .action-buttons {
        width: 100%;
        justify-content: space-between;
        margin-top: 10px;
    }
    
    table {
        display: block;
        overflow-x: auto;
    }
}

/* Style pour le formulaire de suppression */
.delete-form {
    margin: 0;
    padding: 0;
}

.menu-button {
    text-align: left;
    width: 100%;
    background: none;
    border: none;
    padding: 12px 16px;
    cursor: pointer;
    font-size: 14px;
    color: #e53935;
}

.menu-button:hover {
    background-color: #f1f1f1;
}
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<!-- <link rel="stylesheet" href="/assets/css/apprenants.css">
 --><div class="container">
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