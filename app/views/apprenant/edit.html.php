<div class="container">
    <div class="header">
        <div class="header-title">
            <h1>Modifier un apprenant</h1>
            <div class="header-subtitle">Modifiez les informations de l'apprenant ci-dessous.</div>
        </div>
    </div>

    <div class="form-container">
        <form action="?page=edit-apprenant" method="post" enctype="multipart/form-data">
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
                           value="<?= isset($date_naissance) ? htmlspecialchars($date_naissance) : htmlspecialchars($apprenant['date_naissance']) ?>" required>
                    <?php if (isset($errors) && isset($errors['date_naissance'])): ?>
                        <div class="error-message"><?= $errors['date_naissance'] ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-group">
                <label for="adresse">Adresse</label>
                <textarea id="adresse" name="adresse" rows="3" placeholder="Adresse complète"><?= isset($adresse) ? htmlspecialchars($adresse) : htmlspecialchars($apprenant['adresse']) ?></textarea>
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
                            <option value="<?= $ref['id'] ?>" <?= (isset($referentiel_id) ? $referentiel_id : $apprenant['referentiel_id']) == $ref['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($ref['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errors) && isset($errors['referentiel_id'])): ?>
                        <div class="error-message"><?= $errors['referentiel_id'] ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="statut">Statut</label>
                    <select id="statut" name="statut" required>
                        <option value="Actif" <?= (isset($statut) ? $statut : $apprenant['statut']) === 'Actif' ? 'selected' : '' ?>>Actif</option>
                        <option value="Inactif" <?= (isset($statut) ? $statut : $apprenant['statut']) === 'Inactif' ? 'selected' : '' ?>>Inactif</option>
                    </select>
                    <?php if (isset($errors) && isset($errors['statut'])): ?>
                        <div class="error-message"><?= $errors['statut'] ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-group">
                <label for="photo">Photo</label>
                <?php if (!empty($apprenant['photo'])): ?>
                    <div class="current-photo">
                        <img src="<?= $apprenant['photo'] ?>" alt="Photo actuelle" style="max-width: 150px; max-height: 150px;">
                        <p>Photo actuelle</p>
                    </div>
                <?php endif; ?>
                <input type="file" id="photo" name="photo" accept="image/jpeg,image/png">
                <p class="file-format-info">Formats acceptés : JPG, PNG. Taille max : 2MB. Laissez vide pour conserver la photo actuelle.</p>
                <?php if (isset($errors) && isset($errors['photo'])): ?>
                    <div class="error-message"><?= $errors['photo'] ?></div>
                <?php endif; ?>
            </div>

            <div class="form-actions">
                <a href="?page=apprenants" class="btn-cancel">Annuler</a>
                <button type="submit" class="btn-submit">Mettre à jour</button>
            </div>
        </form>
    </div>
</div>