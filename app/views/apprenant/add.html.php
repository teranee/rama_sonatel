<div class="container">
    <div class="header">
        <div class="header-title">
            <h1>Ajouter un apprenant</h1>
            <div class="header-subtitle">Promotion: <?= htmlspecialchars($current_promotion['name']) ?></div>
        </div>
        <div class="header-actions">
            <a href="?page=apprenants" class="btn-back">
                <i class="fas fa-arrow-left"></i> Retour à la liste
            </a>
        </div>
    </div>

    <div class="form-container">
        <form action="?page=add-apprenant" method="post" enctype="multipart/form-data">
            <div class="form-row">
                <div class="form-group">
                    <label for="prenom">Prénom</label>
                    <input type="text" id="prenom" name="prenom" placeholder="Prénom" value="<?= isset($prenom) ? htmlspecialchars($prenom) : '' ?>" required>
                    <?php if (isset($errors) && isset($errors['prenom'])): ?>
                        <div class="error-message"><?= $errors['prenom'] ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="nom">Nom</label>
                    <input type="text" id="nom" name="nom" placeholder="Nom" value="<?= isset($nom) ? htmlspecialchars($nom) : '' ?>" required>
                    <?php if (isset($errors) && isset($errors['nom'])): ?>
                        <div class="error-message"><?= $errors['nom'] ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Email" value="<?= isset($email) ? htmlspecialchars($email) : '' ?>" required>
                <?php if (isset($errors) && isset($errors['email'])): ?>
                    <div class="error-message"><?= $errors['email'] ?></div>
                <?php endif; ?>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="telephone">Téléphone</label>
                    <input type="tel" id="telephone" name="telephone" placeholder="Téléphone" value="<?= isset($telephone) ? htmlspecialchars($telephone) : '' ?>" required>
                    <?php if (isset($errors) && isset($errors['telephone'])): ?>
                        <div class="error-message"><?= $errors['telephone'] ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="date_naissance">Date de naissance</label>
                    <input type="date" id="date_naissance" name="date_naissance" value="<?= isset($date_naissance) ? htmlspecialchars($date_naissance) : '' ?>" required>
                    <?php if (isset($errors) && isset($errors['date_naissance'])): ?>
                        <div class="error-message"><?= $errors['date_naissance'] ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-group">
                <label for="adresse">Adresse</label>
                <textarea id="adresse" name="adresse" rows="3" placeholder="Adresse complète"><?= isset($adresse) ? htmlspecialchars($adresse) : '' ?></textarea>
                <?php if (isset($errors) && isset($errors['adresse'])): ?>
                    <div class="error-message"><?= $errors['adresse'] ?></div>
                <?php endif; ?>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="referentiel_id">Référentiel</label>
                    <select id="referentiel_id" name="referentiel_id" required>
                        <option value="">Sélectionner un référentiel</option>
                        <?php foreach ($referentiels as $ref): ?>
                            <option value="<?= $ref['id'] ?>" <?= (isset($referentiel_id) && $referentiel_id == $ref['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($ref['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errors) && isset($errors['referentiel_id'])): ?>
                        <div class="error-message"><?= $errors['referentiel_id'] ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="photo">Photo (optionnelle)</label>
                    <input type="file" id="photo" name="photo" accept="image/jpeg,image/png">
                    <p class="file-format-info">Formats acceptés : JPG, PNG. Taille max : 2MB.</p>
                    <?php if (isset($errors) && isset($errors['photo'])): ?>
                        <div class="error-message"><?= $errors['photo'] ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-actions">
                <a href="?page=apprenants" class="btn-cancel">Annuler</a>
                <button type="submit" class="btn-submit">Ajouter l'apprenant</button>
            </div>
        </form>
    </div>
</div>