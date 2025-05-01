<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un référentiel</title>
    <link rel="stylesheet" href="/assets/css/referentiels.css">
    <link rel="stylesheet" href="/assets/css/add-ref.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Créer un nouveau référentiel</h1>
        </div>
        
        <div class="form-container">
            <form action="?page=save_referentiel" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="name">Nom du référentiel</label>
                    <input type="text" id="name" name="name" required value="<?= isset($name) ? htmlspecialchars($name) : '' ?>">
                    <?php if (isset($errors) && isset($errors['name'])): ?>
                        <div class="error-message"><?= $errors['name'] ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="capacity">Capacité</label>
                        <input type="number" id="capacity" name="capacity" min="1" required value="<?= isset($capacity) ? htmlspecialchars($capacity) : '' ?>">
                        <?php if (isset($errors) && isset($errors['capacity'])): ?>
                            <div class="error-message"><?= $errors['capacity'] ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="sessions">Nombre de sessions</label>
                        <input type="number" id="sessions" name="sessions" min="1" required value="<?= isset($sessions) ? htmlspecialchars($sessions) : '' ?>">
                        <?php if (isset($errors) && isset($errors['sessions'])): ?>
                            <div class="error-message"><?= $errors['sessions'] ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" required><?= isset($description) ? htmlspecialchars($description) : '' ?></textarea>
                    <?php if (isset($errors) && isset($errors['description'])): ?>
                        <div class="error-message"><?= $errors['description'] ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="image">Image du référentiel</label>
                    <div class="image-upload">
                        <input type="file" id="image" name="image" accept="image/jpeg, image/png">
                        <p>(Formats acceptés: JPG, PNG - Max: 2MB)</p>
                    </div>
                    <?php if (isset($errors) && isset($errors['image'])): ?>
                        <div class="error-message"><?= $errors['image'] ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="btn-container">
                    <a href="?page=all-referentiels" class="btn-cancel">Annuler</a>
                    <button type="submit" class="btn-submit">Créer le référentiel</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>

<style>
        
    </style>