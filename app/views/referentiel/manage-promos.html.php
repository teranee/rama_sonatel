<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="/assets/css/referentiels.css">
    <link rel="stylesheet" href="/assets/css/manage-promo.css">
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Gérer la promotion : <?= htmlspecialchars($current_promotion['name']) ?></h1>
        <a href="?page=referentiels" class="btn btn-back">Retour</a>
    </div>

    <form action="?page=assign-referentiels-process" method="POST">
        <input type="hidden" name="action" value="">

        <!-- Section pour l'ajout de référentiels -->
        <h2>Ajouter un référentiel</h2>
        <div class="add-section">
            <label for="assign_referentiel">Libellé référentiel</label>
            <div class="add-container">
                <select name="assign_referentiel" id="assign_referentiel" class="select-input">
                    <option value="" disabled selected>Choisir un référentiel</option>
                    <?php foreach ($unassigned_referentiels as $ref): ?>
                        <option value="<?= $ref['id'] ?>"><?= htmlspecialchars($ref['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" name="action" value="add" class="btn btn-teal ajout">Ajouter</button>
            </div>
        </div>

        <!-- Section pour la suppression de référentiels -->
        <h2>Référentiels assignés</h2>
        <div class="tags-container">
            <?php if (empty($assigned_referentiels)): ?>
                <div class="no-data">Aucun référentiel assigné à cette promotion</div>
            <?php else: ?>
                <?php foreach ($assigned_referentiels as $index => $ref): ?>
                    <div class="tag tag-<?= $index % 5 ?>">
                        <?= htmlspecialchars($ref['name']) ?>
                        <button type="submit" name="remove_referentiel" value="<?= $ref['id'] ?>" class="tag-remove" onclick="document.querySelector('input[name=action]').value='remove';">×</button>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="action-buttons">
            <button type="submit" name="action" value="finish" class="btn btn-teal" onclick="document.getElementById('form_action').value='finish';">Terminer</button>
        </div>
    </form>
</div>
</body>
</html>

<style>
    /* Conteneur principal */
  
</style>