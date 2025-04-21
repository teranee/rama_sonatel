<div id="bouldof">
<h1 class="auth-title">Se connecter</h1>


<form class="auth-form" action="?page=login-process" method="post">
    <div class="form-group">
        <label for="email">Login</label>
        <input type="text" id="email" name="email" class="form-control" placeholder="Matricule ou email" value="<?= isset($email) ? $email : '' ?>">
        <?php if (isset($errors) && isset($errors['email'])): ?>
            <div class="error-message"><?= $errors['email'] ?></div>
        <?php endif; ?>
    </div>
    
    <div class="form-group">
        <label for="password">Mot de passe</label>
        <input type="password" id="password" name="password" class="form-control" placeholder="Mot de passe">
        <?php if (isset($errors) && isset($errors['password'])): ?>
            <div class="error-message"><?= $errors['password'] ?></div>
        <?php endif; ?>
    </div>
    
    <div class="forgot-password">
        <a href="?page=forgot-password">Mot de passe oublié ?</a>
    </div>
    
    <button type="submit" class="btn-login">Se connecter</button>
</form>

</div>