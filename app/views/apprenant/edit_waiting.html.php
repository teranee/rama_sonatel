<div class="container">
    <div class="header">
        <div class="header-title">
            <h1>Modifier un apprenant en liste d'attente</h1>
            <div class="header-subtitle">Corrigez les informations pour pouvoir approuver cet apprenant.</div>
        </div>
        <div class="header-actions">
            <a href="?page=apprenants&waiting_list=true" class="btn-back">
                <i class="fas fa-arrow-left"></i> Retour à la liste d'attente
            </a>
        </div>
    </div>

    <div class="form-container">
        <form action="?page=edit-waiting-apprenant-process" method="post">
            <input type="hidden" name="id" value="<?= $apprenant['id'] ?>">
            
            <div class="form-row">
                <div class="form-group">
                    <label for="prenom">Prénom</label>
                    <input type="text" id="prenom" name="prenom" placeholder="Prénom" 
                           value="<?= isset($prenom) ? htmlspecialchars($prenom) : htmlspecialchars($apprenant['prenom']) ?>" required>
                    <?php if (isset($errors) && isset($errors['prenom'])): ?>
                        <div class="error-message"><?= $errors['prenom'] ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="nom">Nom</label>
                    <input type="text" id="nom" name="nom" placeholder="Nom" 
                           value="<?= isset($nom) ? htmlspecialchars($nom) : htmlspecialchars($apprenant['nom']) ?>" required>
                    <?php if (isset($errors) && isset($errors['nom'])): ?>
                        <div class="error-message"><?= $errors['nom'] ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Email" 
                       value="<?= isset($email) ? htmlspecialchars($email) : htmlspecialchars($apprenant['email']) ?>" required>
                <?php if (isset($errors) && isset($errors['email'])): ?>
                    <div class="error-message"><?= $errors['email'] ?></div>
                <?php endif; ?>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="telephone">Téléphone</label>
                    <input type="tel" id="telephone" name="telephone" placeholder="Téléphone" 
                           value="<?= isset($telephone) ? htmlspecialchars($telephone) : htmlspecialchars($apprenant['telephone']) ?>" required>
                    <?php if (isset($errors) && isset($errors['telephone'])): ?>
                        <div class="error-message"><?= $errors['telephone'] ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="date_naissance">Date de naissance</label>
                    <input type="date" id="date_naissance" name="date_naissance" 
                           value="<?= isset($date_naissance) ? htmlspecialchars($date_naissance) : htmlspecialchars($apprenant['date_naissance']) ?>">
                    <?php if (isset($errors) && isset($errors['date_naissance'])): ?>
                        <div class="error-message"><?= $errors['date_naissance'] ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-group">
                <label for="adresse">Adresse</label>
                <textarea id="adresse" name="adresse" placeholder="Adresse"><?= isset($adresse) ? htmlspecialchars($adresse) : htmlspecialchars($apprenant['adresse']) ?></textarea>
                <?php if (isset($errors) && isset($errors['adresse'])): ?>
                    <div class="error-message"><?= $errors['adresse'] ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="referentiel_id">Référentiel</label>
                <select id="referentiel_id" name="referentiel_id" required>
                    <option value="">Sélectionner un référentiel</option>
                    <?php foreach ($referentiels as $referentiel): ?>
                        <option value="<?= $referentiel['id'] ?>" <?= (isset($referentiel_id) && $referentiel_id == $referentiel['id']) || $apprenant['referentiel_id'] == $referentiel['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($referentiel['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($errors) && isset($errors['referentiel_id'])): ?>
                    <div class="error-message"><?= $errors['referentiel_id'] ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Raison du rejet</label>
                <div class="reason-box">
                    <?= htmlspecialchars($apprenant['raison']) ?>
                </div>
            </div>

            <div class="form-actions">
                <a href="?page=apprenants&waiting_list=true" class="btn-cancel">Annuler</a>
                <button type="submit" class="btn-submit">Mettre à jour</button>
            </div>
        </form>
    </div>
</div>

<style>
.reason-box {
    background-color: #f8d7da;
    border: 1px solid #f5c6cb;
    color: #721c24;
    padding: 10px;
    border-radius: 4px;
    margin-top: 5px;
}
</style>