<link rel="stylesheet" href="assets/css/error404.css">
    <div class="error-container">
        <div class="particles">
            <?php for($i = 0; $i < 50; $i++): ?>
                <div class="particle" style="
                    left: <?= rand(0, 100) ?>%;
                    top: <?= rand(0, 100) ?>%;
                    animation-delay: <?= rand(0, 20) / 10 ?>s;"></div>
            <?php endfor; ?>
        </div>
        
        <h1 class="error-number">404</h1>
        <h2 class="error-text">Page non trouvée</h2>
        <p class="error-description">
            Fi nga dougou bakhoul delloul 
        </p>
        <a href="?page=login" class="back-button">
            Retour à la page de connexion
        </a>
    </div>

