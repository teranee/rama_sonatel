<div class="container">
    <div class="header">
        <div class="header-title">
            <h1>Promotion</h1>
            <div class="header-subtitle">Gérer les promotions de l'école</div>
        </div>
        <button type="button" id="addPromotionBtn" class="add-button">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Ajouter une promotion
        </button>
    </div>

    <div class="stats-container">
        <div class="stat-card">
            <div>
                <div class="stat-number"><?= $stats['active_learners'] ?></div>
                <div class="stat-label">Apprenants actifs</div>
            </div>
            <div class="stat-icon">
                <svg viewBox="0 0 24 24" fill="white">
                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                </svg>
            </div>
        </div>
        
        <div class="stat-card">
            <div>
                <div class="stat-number"><?= $stats['total_referentials'] ?></div>
                <div class="stat-label">Référentiels</div>
            </div>
            <div class="stat-icon">
                <svg viewBox="0 0 24 24" fill="white">
                    <path d="M21 5c-1.11-.35-2.33-.5-3.5-.5-1.95 0-4.05.4-5.5 1.5-1.45-1.1-3.55-1.5-5.5-1.5-1.95 0-4.05.4-5.5 1.5v14.65c0 .25.25.5.5.5.1 0 .15-.05.25-.05C3.1 20.45 5.05 20 6.5 20c1.95 0 4.05.4 5.5 1.5 1.35-.85 3.8-1.5 5.5-1.5 1.65 0 3.35.3 4.75 1.05.1.05.15.05.25.05.25 0 .5-.25.5-.5V6c-.6-.45-1.25-.75-2-1z"/>
                </svg>
            </div>
        </div>
        
        <div class="stat-card">
            <div>
                <div class="stat-number"><?= $stats['active_promotions'] ?></div>
                <div class="stat-label">Promotions actives</div>
            </div>
            <div class="stat-icon">
                <svg viewBox="0 0 24 24" fill="white">
                    <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                </svg>
            </div>
        </div>
        
        <div class="stat-card">
            <div>
                <div class="stat-number"><?= $stats['total_promotions'] ?></div>
                <div class="stat-label">Total promotions</div>
            </div>
            <div class="stat-icon">
                <svg viewBox="0 0 24 24" fill="white">
                    <path d="M20 6h-8l-2-2H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2z"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="search-filter">
        <div class="search-bar">
            <span class="search-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
            </span>
           
        </div>
    </div>

    <div class="filter-container">

    <div class="formu">
            <form  action="?page=promotions" method="GET">
                <input type="hidden" name="page" value="promotions">
                <input id="form" type="text" name="search" placeholder="Rechercher une promotion..."  value="<?= htmlspecialchars($search ?? '') ?>">
            </form>
    </div>
        <div class="view-toggle">

        <select class="filter-dropdown">
            <option>Tous</option>
        </select>
            <button class="view-button active" onclick="switchView('grid')">Grille</button>
            <button class="view-button" onclick="switchView('list')">Liste</button>
        </div>
    </div>

    <div class="promotions-grid">
        <?php if (empty($promotions)): ?>
            <div class="no-results">Aucune promotion trouvée</div>
        <?php else: ?>
            <?php foreach ($promotions as $promotion): ?>
                <div class="promotion-card">
                    <div class="status-container">
                        <div class="status-badge <?= $promotion['status'] === 'active' ? 'active' : 'inactive' ?>">
                            <?= ucfirst($promotion['status']) ?>
                        </div>
                        <form action="?page=toggle_promotion" method="POST" class="toggle-form">
                            <input type="hidden" name="promotion_id" value="<?= $promotion['id'] ?>">
                            <button type="submit" class="toggle-button <?= $promotion['status'] === 'active' ? 'active' : '' ?>">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M18.36 6.64a9 9 0 1 1-12.73 0"></path>
                                    <line x1="12" y1="2" x2="12" y2="12"></line>
                                </svg>
                            </button>
                        </form>
                    </div>
                    <img src="assets/images/uploads/promotions/<?= htmlspecialchars($promotion['image']) ?>" 
                         alt="<?= htmlspecialchars($promotion['name']) ?>" 
                         class="promotion-avatar">
                    <div class="promotion-title"><?= htmlspecialchars($promotion['name']) ?></div>
                    <div class="promotion-date">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                        <?= date('d/m/Y', strtotime($promotion['date_debut'])) ?> - 
                        <?= date('d/m/Y', strtotime($promotion['date_fin'])) ?>
                    </div>
                    <div class="promotion-students">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                        <?= count($promotion['apprenants'] ?? []) ?> apprenants
                    </div>
                    <a href="?page=promotion&id=<?= $promotion['id'] ?>" class="view-details">
                        Voir détails
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#f97316" stroke-width="2">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="promotions-list">
        <?php if (!empty($promotions)): ?>
            <?php foreach ($promotions as $promotion): ?>
                <div class="promotion-card-list">
                    <div class="promotion-list-left">
                        <img src="assets/images/uploads/promotions/<?= htmlspecialchars($promotion['image']) ?>" 
                             alt="<?= htmlspecialchars($promotion['name']) ?>" 
                             class="promotion-avatar">
                        <div class="promotion-list-middle">
                            <div class="promotion-title"><?= htmlspecialchars($promotion['name']) ?></div>
                            <div class="promotion-date">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                </svg>
                                <?= date('d/m/Y', strtotime($promotion['date_debut'])) ?> - 
                                <?= date('d/m/Y', strtotime($promotion['date_fin'])) ?>
                            </div>
                            <div class="promotion-students">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                </svg>
                                <?= count($promotion['apprenants'] ?? []) ?> apprenants
                            </div>
                        </div>
                    </div>
                    <div class="promotion-list-right">
                        <div class="status-container">
                            <div class="status-badge <?= $promotion['status'] === 'active' ? 'active' : 'inactive' ?>">
                                <?= ucfirst($promotion['status']) ?>
                            </div>
                            <form action="?page=toggle_promotion" method="POST" class="toggle-form">
                                <input type="hidden" name="promotion_id" value="<?= $promotion['id'] ?>">
                                <button type="submit" class="toggle-button <?= $promotion['status'] === 'active' ? 'active' : '' ?>">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M18.36 6.64a9 9 0 1 1-12.73 0"></path>
                                        <line x1="12" y1="2" x2="12" y2="12"></line>
                                    </svg>
                                </button>
                            </form>
                        </div>
                        <a href="?page=promotion&id=<?= $promotion['id'] ?>" class="view-details">
                            Voir détails
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#f97316" stroke-width="2">
                                <polyline points="9 18 15 12 9 6"></polyline>
                            </svg>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="pagination">
        <?php if ($total_pages > 1): ?>
            <!-- Bouton précédent -->
            <?php if ($current_page > 1): ?>
                <a href="?page=promotions&page_num=<?= $current_page - 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" 
                   class="pagination-button prev">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="15 18 9 12 15 6"></polyline>
                    </svg>
                    Précédent
                </a>
            <?php endif; ?>

            <!-- Pages numérotées -->
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=promotions&page_num=<?= $i ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" 
                   class="pagination-button <?= $i === $current_page ? 'active' : '' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>

            <!-- Bouton suivant -->
            <?php if ($current_page < $total_pages): ?>
                <a href="?page=promotions&page_num=<?= $current_page + 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" 
                   class="pagination-button next">
                    Suivant
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </a>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <div id="addPromotionModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Créer une nouvelle promotion</h2>
                <span class="close-button" onclick="closeAddPromotionModal()">&times;</span>
            </div>
            
            <p class="modal-description">Remplissez les informations ci-dessous pour créer une nouvelle promotion.</p>
            
            <form class="promotion-form" action="?page=add_promotion" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="promotion-name">Nom de la promotion</label> <br> <br>
                    <input type="text" id="promotion-name" name="name" placeholder="Ex: Promotion 2025" required>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="start-date">Date de début</label> <br> <br>
                        <div class="date-input-container">
                            <input type="date" id="start-date" name="date_debut" required>
                            <span class="calendar-icon"></span>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="end-date">Date de fin</label> <br> <br>
                        <div class="date-input-container">
                            <input type="date" id="end-date" name="date_fin" required>
                            <span class="calendar-icon"></span>
                        </div>
                    </div>
                </div>
                <label>Photo de la promotion</label> <br> <br>
                <div class="form-group" id="taf">
                   
                    <div class="file-upload-container">
                        <input type="file" id="promotion-image" name="image" accept="image/png,image/jpeg" hidden>
                        <button type="button" class="upload-button" onclick="document.getElementById('promotion-image').click()">
                            Ajouter
                        </button>
                        <span class="upload-text">ou glisser</span>
                        
                    </div>
                    <p class="file-restrictions">Format JPG, PNG. Taille max 2MB</p>
                </div>
                
                <div class="form-group">
                    <label>Référentiels</label> <br> <br>
                    <div class="search-container">
                        <span class="search-icon"><i class="fa-solid fa-magnifying-glass"></i></span>
                        <input type="text" 
                               id="referentiel-search" 
                               placeholder="Rechercher un référentiel..."
                               onkeyup="searchReferentiels(this.value)">
                    </div>
                    <div id="referentiels-list" class="referentiels-container">
                        <!-- Les référentiels seront affichés ici -->
                    </div>
                    <input type="hidden" name="referentiels" id="selected-referentiels">
                </div>
                
                <div class="form-buttons">
                    <button type="button" class="cancel-button" onclick="closeAddPromotionModal()">Annuler</button>
                    <button type="submit" class="submit-button">Créer la promotion</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Vue en grille/liste
    function switchView(view) {
        const container = document.querySelector('.container');
        const buttons = document.querySelectorAll('.view-button');
        
        buttons.forEach(button => button.classList.remove('active'));
        if (view === 'list') {
            container.classList.add('show-list');
            document.querySelector('.view-button[onclick="switchView(\'list\')"]').classList.add('active');
        } else {
            container.classList.remove('show-list');
            document.querySelector('.view-button[onclick="switchView(\'grid\')"]').classList.add('active');
        }
    }

    // Gestion du modal
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('addPromotionModal');
        const addBtn = document.getElementById('addPromotionBtn');
        const closeBtn = document.querySelector('.close-button');
        const modalContent = document.querySelector('.modal-content');
        
        // Initialiser le modal comme caché
        modal.style.display = 'none';
        
        // Ouvrir le modal uniquement sur le clic du bouton Ajouter
        addBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            modal.style.display = 'flex';
            document.body.classList.add('modal-open');
        });
        
        // Fermer avec le bouton X
        closeBtn.addEventListener('click', function() {
            modal.style.display = 'none';
            document.body.classList.remove('modal-open');
        });
        
        // Fermer en cliquant à l'extérieur
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.style.display = 'none';
                document.body.classList.remove('modal-open');
            }
        });
        
        // Empêcher la fermeture en cliquant dans le modal
        modalContent.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    });

    async function searchReferentiels(query) {
        const response = await fetch(`?page=search_referentiels&q=${encodeURIComponent(query)}`);
        const referentiels = await response.json();
        
        const container = document.getElementById('referentiels-list');
        container.innerHTML = referentiels.map(ref => `
            <div class="referentiel-item">
                <input type="checkbox" 
                       name="referentiels[]" 
                       value="${ref.id}"
                       onchange="updateSelectedReferentiels()">
                <span>${ref.name}</span>
            </div>
        `).join('');
    }
    
    function updateSelectedReferentiels() {
        const selected = Array.from(document.querySelectorAll('input[name="referentiels[]"]:checked'))
                             .map(cb => cb.value);
        document.getElementById('selected-referentiels').value = JSON.stringify(selected);
    }
</script>
