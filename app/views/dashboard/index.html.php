<div class="dashboard-wrapper">
    <div class="dashboard-header">
        <h1>Tableau de bord</h1>
        <p>Bienvenue dans le système de gestion des apprenants</p>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-user-graduate"></i>
            </div>
            <div class="stat-content">
                <h3><?= $stats['active_learners'] ?? 0 ?></h3>
                <p>Apprenants actifs</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-book"></i>
            </div>
            <div class="stat-content">
                <h3><?= $stats['total_referentials'] ?? 0 ?></h3>
                <p>Référentiels</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-content">
                <h3><?= $stats['active_promotions'] ?? 0 ?></h3>
                <p>Promotions actives</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <div class="stat-content">
                <h3><?= $stats['total_promotions'] ?? 0 ?></h3>
                <p>Total des promotions</p>
            </div>
        </div>
    </div>

    <?php if (isset($current_promotion) && $current_promotion): ?>
    <div class="current-promotion-section">
        <h2>Promotion actuelle: <?= htmlspecialchars($current_promotion['name']) ?></h2>
        
        <div class="promotion-details">
            <div class="detail-card">
                <div class="detail-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="detail-content">
                    <p class="detail-label">Date de début</p>
                    <p class="detail-value"><?= htmlspecialchars($current_promotion['date_debut']) ?></p>
                </div>
            </div>
            
            <div class="detail-card">
                <div class="detail-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="detail-content">
                    <p class="detail-label">Date de fin</p>
                    <p class="detail-value"><?= htmlspecialchars($current_promotion['date_fin']) ?></p>
                </div>
            </div>
            
            <div class="detail-card">
                <div class="detail-icon">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <div class="detail-content">
                    <p class="detail-label">Apprenants</p>
                    <p class="detail-value"><?= $current_promotion['nb_apprenants'] ?? 0 ?></p>
                </div>
            </div>
            
            <div class="detail-card">
                <div class="detail-icon">
                    <i class="fas fa-book"></i>
                </div>
                <div class="detail-content">
                    <p class="detail-label">Référentiels</p>
                    <p class="detail-value"><?= count($current_promotion['referentiels'] ?? []) ?></p>
                </div>
            </div>
        </div>
        
        <div class="action-buttons">
            <a href="?page=apprenants" class="btn btn-primary">
                <i class="fas fa-users"></i> Voir les apprenants
            </a>
            <a href="?page=referentiels" class="btn btn-secondary">
                <i class="fas fa-book"></i> Voir les référentiels
            </a>
        </div>
    </div>
    <?php endif; ?>
    <div class="recent-section">
        <div class="recent-referentiels">
            <h2>Référentiels récents</h2>
            <?php if (isset($recent_referentiels) && !empty($recent_referentiels)): ?>
                <ul class="recent-list">
                    <?php foreach (array_slice($recent_referentiels ?? [], 0, 5) as $ref): ?>
                        <li>
                            <div class="item-name"><?= htmlspecialchars($ref['name']) ?></div>
                            <div class="item-meta"><?= count($ref['apprenants'] ?? []) ?> apprenants</div>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <a href="?page=referentiels" class="view-all">Voir tous les référentiels</a>
            <?php else: ?>
                <p class="no-data">Aucun référentiel disponible</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
    .dashboard-wrapper {
        padding: 20px;
        max-width: 1200px;
        margin: 0 auto;
    }

    .dashboard-header {
        margin-bottom: 30px;
    }

    .dashboard-header h1 {
        font-size: 28px;
        color: #0e8f7e;
        margin-bottom: 5px;
    }

    .dashboard-header p {
        color: #666;
        font-size: 16px;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background-color:a #FF6600;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        padding: 20px;
        display: flex;
        align-items: center;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .stat-icon {
        width: 50px;
        height: 50px;
        background-color:rgba(245, 165, 35, 0.36);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
    }

    .stat-icon i {
        color: white;
        font-size: 20px;
    }

    .stat-content h3 {
        font-size: 24px;
        color: white;
        margin: 0 0 5px 0;
    }

    .stat-content p {
        color: white;
        margin: 0;
    }

    .current-promotion-section {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        padding: 20px;
        margin-bottom: 30px;
        margin-top: 50px;
    }

    .current-promotion-section h2 {
        font-size: 22px;
        color: #0e8f7e;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 1px solid #eee;
    }

    .promotion-details {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 20px;
    }

    .detail-card {
        background-color: #f9f9f9;
        border-radius: 8px;
        padding: 15px;
        display: flex;
        align-items: center;
        transition: transform 0.2s;
    }

    .detail-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 3px 10px rgba(0,0,0,0.05);
    }

    .detail-icon {
        width: 40px;
        height: 40px;
        background-color: #f5a623;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
    }

    .detail-icon i {
        color: white;
        font-size: 18px;
    }

    .detail-content {
        flex: 1;
    }

    .detail-label {
        color: #666;
        font-size: 14px;
        margin: 0 0 5px 0;
    }

    .detail-value {
        color: #333;
        font-size: 18px;
        font-weight: 500;
        margin: 0;
    }

    .action-buttons {
        display: flex;
        gap: 15px;
        margin-top: 20px;
    }

    .btn {
        padding: 10px 18px;
        border-radius: 6px;
        font-size: 15px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        cursor: pointer;
        transition: all 0.2s;
        border: none;
    }

    .btn i {
        margin-right: 8px;
    }

    .btn-primary {
        background-color: #f5a623;
        color: white;
    }

    .btn-primary:hover {
        background-color: #e09612;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(245, 166, 35, 0.2);
    }

    .btn-secondary {
        background-color: #f0f0f0;
        color: #333;
    }

    .btn-secondary:hover {
        background-color: #e0e0e0;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    @media (max-width: 768px) {
        .promotion-details {
            grid-template-columns: 1fr 1fr;
        }
        
        .action-buttons {
            flex-direction: column;
        }
        
        .btn {
            width: 100%;
            justify-content: center;
        }
    }

    @media (max-width: 480px) {
        .promotion-details {
            grid-template-columns: 1fr;
        }
    }

    .recent-section {
        display: grid;
        grid-template-columns: 1fr;
        gap: 20px;
    }

    .recent-referentiels {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        padding: 20px;
    }

    .recent-referentiels h2 {
        font-size: 20px;
        color: #333;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 1px solid #eee;
    }

    .recent-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .recent-list li {
        padding: 12px 0;
        border-bottom: 1px solid #eee;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .recent-list li:last-child {
        border-bottom: none;
    }

    .item-name {
        font-weight: 500;
        color: #333;
    }

    .item-meta {
        color: #666;
        font-size: 14px;
    }

    .view-all {
        display: block;
        text-align: center;
        margin-top: 15px;
        color: #f5a623;
        text-decoration: none;
    }

    .view-all:hover {
        text-decoration: underline;
    }

    .no-data {
        color: #666;
        font-style: italic;
        text-align: center;
        padding: 20px 0;
    }

    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: 1fr 1fr;
        }
        
        .promotion-details {
            grid-template-columns: 1fr 1fr;
        }
    }

    @media (max-width: 480px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }
        
        .promotion-details {
            grid-template-columns: 1fr;
        }
    }
</style>
