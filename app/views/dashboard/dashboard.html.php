<div class="dashboard-container">
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-content">
                <h3><?= $stats['active_learners'] ?? 0 ?></h3>
                <p>Apprenants actifs</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-content">
                <h3><?= $stats['total_referentials'] ?? 0 ?></h3>
                <p>Total référentiels</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-content">
                <h3><?= $stats['active_promotions'] ?? 0 ?></h3>
                <p>Promotions actives</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-content">
                <h3><?= $stats['total_promotions'] ?? 0 ?></h3>
                <p>Total promotions</p>
            </div>
        </div>
    </div>
</div>