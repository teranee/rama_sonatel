<div class="container">
    <div class="header">
        <div class="header-title">
            <h1>Importer des apprenants</h1>
            <div class="header-subtitle">Importez plusieurs apprenants à partir d'un fichier Excel (CSV)</div>
        </div>
        <div class="header-actions">
            <a href="?page=apprenants" class="btn-back">
                <i class="fas fa-arrow-left"></i> Retour à la liste
            </a>
        </div>
    </div>

    <div class="form-container">
        <?php if (isset($success_message)): ?>
            <div class="success-message">
                <?= $success_message ?>
            </div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div class="error-message">
                <?= $error_message ?>
            </div>
        <?php endif; ?>

        <div class="import-instructions">
            <h3>Instructions</h3>
            <p>Veuillez suivre ces étapes pour importer correctement vos apprenants:</p>
            <ol>
                <li>Téléchargez le <a href="?page=download-template" class="template-link">modèle Excel</a></li>
                <li>Remplissez le fichier avec les informations des apprenants</li>
                <li>Enregistrez le fichier au format CSV (séparateur: virgule)</li>
                <li>Importez le fichier ci-dessous</li>
            </ol>
            <p class="note">Note: Les champs obligatoires sont: Prénom, Nom, Email, Téléphone et Référentiel</p>
        </div>

        <form action="?page=import-apprenants-process" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="csv_file">Fichier CSV</label>
                <input type="file" id="csv_file" name="csv_file" accept=".csv" required>
                <p class="file-format-info">Format accepté: CSV (séparateur: virgule)</p>
            </div>

            <div class="form-group">
                <label>Options d'importation</label>
                <div class="checkbox-group">
                    <input type="checkbox" id="skip_header" name="skip_header" checked>
                    <label for="skip_header">Ignorer la première ligne (en-têtes)</label>
                </div>
                <div class="checkbox-group">
                    <input type="checkbox" id="add_to_waiting_list" name="add_to_waiting_list" checked>
                    <label for="add_to_waiting_list">Ajouter les apprenants non conformes à la liste d'attente</label>
                </div>
            </div>

            <div class="form-actions">
                <a href="?page=apprenants" class="btn-cancel">Annuler</a>
                <button type="submit" class="btn-submit">Importer</button>
            </div>
        </form>

        <?php if (isset($import_results) && !empty($import_results)): ?>
            <div class="import-results">
                <h3>Résultats de l'importation</h3>
                
                <?php if (!empty($import_results['success'])): ?>
                    <div class="success-section">
                        <h4>Apprenants importés avec succès (<?= count($import_results['success']) ?>)</h4>
                        <table class="results-table">
                            <thead>
                                <tr>
                                    <th>Matricule</th>
                                    <th>Nom complet</th>
                                    <th>Email</th>
                                    <th>Référentiel</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($import_results['success'] as $apprenant): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($apprenant['matricule']) ?></td>
                                        <td><?= htmlspecialchars($apprenant['prenom'] . ' ' . $apprenant['nom']) ?></td>
                                        <td><?= htmlspecialchars($apprenant['email']) ?></td>
                                        <td><?= htmlspecialchars($apprenant['referentiel']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($import_results['waiting_list'])): ?>
                    <div class="waiting-list-section">
                        <h4>Apprenants ajoutés à la liste d'attente (<?= count($import_results['waiting_list']) ?>)</h4>
                        <table class="results-table">
                            <thead>
                                <tr>
                                    <th>Nom complet</th>
                                    <th>Email</th>
                                    <th>Référentiel</th>
                                    <th>Raison</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($import_results['waiting_list'] as $apprenant): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($apprenant['prenom'] . ' ' . $apprenant['nom']) ?></td>
                                        <td><?= htmlspecialchars($apprenant['email']) ?></td>
                                        <td><?= htmlspecialchars($apprenant['referentiel']) ?></td>
                                        <td><?= htmlspecialchars($apprenant['raison']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($import_results['errors'])): ?>
                    <div class="errors-section">
                        <h4>Lignes ignorées (<?= count($import_results['errors']) ?>)</h4>
                        <table class="results-table">
                            <thead>
                                <tr>
                                    <th>Ligne</th>
                                    <th>Données</th>
                                    <th>Erreur</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($import_results['errors'] as $error): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($error['line']) ?></td>
                                        <td><?= htmlspecialchars($error['data']) ?></td>
                                        <td><?= htmlspecialchars($error['message']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.import-instructions {
    background-color: #f8f9fa;
    padding: 20px;
    border-radius: 5px;
    margin-bottom: 20px;
}

.import-instructions h3 {
    margin-top: 0;
    color: #333;
}

.import-instructions ol {
    margin-left: 20px;
}

.import-instructions li {
    margin-bottom: 10px;
}

.template-link {
    color: #11a683;
    text-decoration: underline;
}

.note {
    font-style: italic;
    color: #666;
    margin-top: 15px;
}

.checkbox-group {
    margin-bottom: 10px;
}

.import-results {
    margin-top: 30px;
}

.import-results h3 {
    margin-bottom: 20px;
}

.import-results h4 {
    margin: 15px 0;
}

.results-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}

.results-table th {
    background-color: #f5a623;
    color: white;
    text-align: left;
    padding: 10px;
}

.results-table td {
    padding: 8px 10px;
    border-bottom: 1px solid #eee;
}

.success-section {
    margin-bottom: 30px;
}

.waiting-list-section {
    margin-bottom: 30px;
}

.errors-section {
    margin-bottom: 30px;
}

.success-message {
    background-color: #d4edda;
    color: #155724;
    padding: 15px;
    border-radius: 5px;
    margin-bottom: 20px;
}

.error-message {
    background-color: #f8d7da;
    color: #721c24;
    padding: 15px;
    border-radius: 5px;
    margin-bottom: 20px;
}
</style>