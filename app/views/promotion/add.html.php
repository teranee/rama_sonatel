 <!-- ?php
if (isset($result) && $result) {
    // Supprimez l'image temporaire si elle existe
    if (isset($_SESSION['uploaded_promotion_image'])) {
        unlink($_SESSION['uploaded_promotion_image']);
        unset($_SESSION['uploaded_promotion_image']);
        unset($_SESSION['uploaded_promotion_image_name']);
    }
}
?>
 -->
<link rel="stylesheet" href="assets/css/promotion-form.css">
<link rel="stylesheet" href="assets/css/add-promo.css">
<div class="container">
    <div class="header">
        <div class="header-title">
            <h1>Créer une nouvelle promotion</h1>
            <div class="header-subtitle">Remplissez les informations ci-dessous pour créer une nouvelle promotion.</div>
        </div>
    </div>

    <div class="form-container">
        <!-- Formulaire principal pour les données de la promotion -->
        <form class="promotion-form" action="?page=add_promotion" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="promotion-name">Nom de la promotion</label>
                <input type="text" id="promotion-name" name="name" placeholder="Ex: Promotion 2025" value="<?= isset($name) ? htmlspecialchars($name) : '' ?>">
                <?php if (isset($errors) && isset($errors['name'])): ?>
                    <div class="error-message"><?= $errors['name'] ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="start-date">Date de début (jj/mm/aaaa)</label>
                    <div class="date-input-container">
                        <input type="text" id="start-date" name="date_debut" 
                               placeholder="jj/mm/aaaa" 
                               value="<?= isset($date_debut) ? htmlspecialchars($date_debut) : '' ?>">
                        <span class="calendar-icon"></span>
                    </div>
                    <?php if (isset($errors) && isset($errors['date_debut'])): ?>
                        <div class="error-message"><?= $errors['date_debut'] ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="end-date">Date de fin (jj/mm/aaaa)</label>
                    <div class="date-input-container">
                        <input type="text" id="end-date" name="date_fin" 
                               placeholder="jj/mm/aaaa" 
                               value="<?= isset($date_fin) ? htmlspecialchars($date_fin) : '' ?>">
                        <span class="calendar-icon"></span>
                    </div>
                    <?php if (isset($errors) && isset($errors['date_fin'])): ?>
                        <div class="error-message"><?= $errors['date_fin'] ?></div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Modifiez la section d'upload d'image -->
            <div class="form-group">
                <!-- <label for="promotion-image">Image (optionnelle)</label> -->
                <!-- <p>Une image par défaut sera utilisée si aucune image n'est sélectionnée.</p> -->
                <input type="file" id="promotion-image" name="image" accept="image/png,image/jpeg" class="styled-file-input">
                <p class="file-restrictions">Formats acceptés : JPG, PNG. Taille max : 2MB.</p>
                <?php if (isset($errors) && isset($errors['image'])): ?>
                    <div class="error-message"><?= $errors['image'] ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label>Référentiels</label>
                <div class="referentiels-list">
                    <?php if (isset($referentiels) && !empty($referentiels)): ?>
                        <?php foreach ($referentiels as $referentiel): ?>
                            <div class="referentiel-item">
                                <input type="checkbox" id="ref_<?= $referentiel['id'] ?>" name="referentiels[]" value="<?= $referentiel['id'] ?>"
                                    <?= isset($referentiels_selected) && in_array($referentiel['id'], $referentiels_selected) ? 'checked' : '' ?>>
                                <label for="ref_<?= $referentiel['id'] ?>">
                                    <?= htmlspecialchars($referentiel['name']) ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>Aucun référentiel disponible.</p>
                    <?php endif; ?>
                </div>
                <?php if (isset($errors) && isset($errors['referentiels'])): ?>
                    <div class="error-message"><?= $errors['referentiels'] ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-buttons">
                <a href="?page=promotions" class="cancel-button">Annuler</a>
                <button type="submit" class="submit-button">Créer la promotion</button>
            </div>
        </form>
    </div>
</div>

<style>
/* Styles supplémentaires pour les champs de date personnalisés */

</style>
