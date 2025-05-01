<div class="container">
    <div class="header">
        <div class="header-title">
            <h1>Détails de l'apprenant</h1>
            <div class="header-subtitle">Informations complètes sur l'apprenant.</div>
        </div>
        <div class="header-actions">
            <a href="?page=apprenants" class="btn-back">
                <i class="fas fa-arrow-left"></i> Retour à la liste
            </a>
        </div>
    </div>

    <div class="apprenant-details">
        <div class="apprenant-header">
            <div class="apprenant-photo">
                <img src="<?= !empty($apprenant['photo']) ? $apprenant['photo'] : 'assets/images/default-avatar.png' ?>" 
                     alt="Photo de <?= htmlspecialchars($apprenant['prenom'] . ' ' . $apprenant['nom']) ?>">
            </div>
            <div class="apprenant-info">
                <h2><?= htmlspecialchars($apprenant['prenom'] . ' ' . $apprenant['nom']) ?></h2>
                <p class="matricule">Matricule: <?= htmlspecialchars($apprenant['matricule']) ?></p>
                <p class="status <?= strtolower($apprenant['statut']) ?>"><?= htmlspecialchars($apprenant['statut']) ?></p>
            </div>
            <div class="apprenant-actions">
                <a href="?page=edit-apprenant-form&id=<?= $apprenant['id'] ?>" class="btn-edit">
                    <i class="fas fa-edit"></i> Modifier
                </a>
                <a href="?page=delete-apprenant&id=<?= $apprenant['id'] ?>" 
                   class="btn-delete"
                   onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet apprenant ?')">
                    <i class="fas fa-trash"></i> Supprimer
                </a>
            </div>
        </div>

        <div class="details-section">
            <h3>Informations personnelles</h3>
            <div class="details-grid">
                <div class="detail-item">
                    <span class="detail-label">Email</span>
                    <span class="detail-value"><?= htmlspecialchars($apprenant['email']) ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Téléphone</span>
                    <span class="detail-value"><?= htmlspecialchars($apprenant['telephone']) ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Date de naissance</span>
                    <span class="detail-value"><?= htmlspecialchars($apprenant['date_naissance']) ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Adresse</span>
                    <span class="detail-value"><?= htmlspecialchars($apprenant['adresse']) ?></span>
                </div>
            </div>
        </div>

        <div class="details-section">
            <h3>Informations académiques</h3>
            <div class="details-grid">
                <div class="detail-item">
                    <span class="detail-label">Promotion</span>
                    <span class="detail-value"><?= htmlspecialchars($promotion['name']) ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Référentiel</span>
                    <span class="detail-value"><?= htmlspecialchars($referentiel['name']) ?></span>
                </div>
            </div>
        </div>
    </div>
</div>