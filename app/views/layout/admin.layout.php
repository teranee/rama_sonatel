<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Apprenants ODC</title>
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link rel="stylesheet" href="assets/css/promo.css">
    <link rel="stylesheet" href="assets/css/modal.css">
    <link rel="stylesheet" href="assets/css/modal-form.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Permettre l'inclusion de CSS spécifiques à chaque page -->
    <?php if (isset($additional_css)): ?>
        <?= $additional_css ?>
    <?php endif; ?>
</head>
<body>
    <div class="app-container">
        <!-- Sidebar / Menu latéral -->
        <div class="sidebar">
            <div class="logo-container">
                <div class="logo">
                <img style="width: 100px; display: block; margin: auto;" src="assets/images/sonatel-logo.png" alt="Sonatel Academy">
                    <div class="logo-text">
                        
                    </div>
                </div>
                <div class="promotion">
                    <?php 
                    global $model;
                    $current_promotion = $model['get_current_promotion']();
                    echo $current_promotion ? htmlspecialchars($current_promotion['name']) : 'Aucune promotion active';
                    ?>
                </div>
            </div>
            
            <!-- Menu de navigation -->
            <nav class="main-nav">
                <ul>
                    <li class="<?= isset($active_menu) && $active_menu === 'dashboard' ? 'active' : '' ?>">
                        <a href="?page=dashboard">
                            <span class="icon"></span>
                            <span>Tableau de bord</span>
                        </a>
                    </li>
                    <li class="<?= isset($active_menu) && $active_menu === 'promotions' ? 'active' : '' ?>">
                        <a href="?page=promotions">
                            <span class="icon"></span>
                            <span>Gestion des promotions</span>
                        </a>
                    </li>
                    <li class="<?= isset($active_menu) && $active_menu === 'referentiels' ? 'active' : '' ?>">
                        <a href="?page=referentiels">
                            <span class="icon"></span>
                            <span>Gestion des référentiels</span>
                        </a>
                    </li>
                    <li class="<?= isset($active_menu) && $active_menu === 'apprenants' ? 'active' : '' ?>">
                        <a href="?page=apprenants">
                            <span class="icon"></span>
                            <span>Gestion des apprenants</span>
                        </a>
                    </li>
                    <li class="<?= isset($active_menu) && $active_menu === 'presences' ? 'active' : '' ?>">
                        <a href="?page=presences">
                            <span class="icon"></span>
                            <span>Gestion des présences</span>
                        </a>
                    </li>
                    <li class="<?= isset($active_menu) && $active_menu === 'kits' ? 'active' : '' ?>">
                        <a href="?page=kits">
                            <span class="icon"></span>
                            <span>Kits & Laptops</span>
                        </a>
                    </li>
                    <li class="<?= isset($active_menu) && $active_menu === 'rapports' ? 'active' : '' ?>">
                        <a href="?page=rapports">
                            <span class="icon"></span>
                            <span>Rapports & Stats</span>
                        </a>
                    </li>
                </ul>
            </nav>
            <div id="bouton">
                <a href="?page=logout">
                    <button>Déconnexion</button>
                </a>
            </div>
        </div>
        
        <!-- Contenu principal -->
        <div class="main-content">
            <!-- Header / Entête -->
            <header class="top-header">
                <div class="search-bar">
                    <i id="icon" class="fa-solid fa-magnifying-glass"></i>
                    <input id="inp" type="text" placeholder="Rechercher...">
                </div>
                
                <div class="user-menu">
                    <div class="notifications">
                        <span></span>
                    </div>
                    <div class="user-profile">
                        <div class="user-info">
                            <span class="user-name"><?= isset($user['name']) ? $user['name'] : 'Utilisateur' ?></span>
                            <span class="user-role"><?= isset($user['profile']) ? $user['profile'] : 'Invité' ?></span>
                        </div>
                         <div class="avatar">
                            <img src="assets/images/avatar.png" alt="Avatar">
                        </div> 
                       <div class="dropdown-menu">
                            <ul>
                                <li><a href="?page=profile">Profil</a></li>
                                <li><a href="?page=change-password">Changer mot de passe</a></li>
                                <li><a href="?page=logout">Déconnexion</a></li>
                            </ul>
                        </div> 

                        
                    </div>
                </div>
            </header>
            
            <!-- Messages flash -->
            <?php if (isset($flash) && $flash): ?>
                <div class="alert alert-<?= $flash['type'] ?>">
                    <?= $flash['message'] ?>
                </div>
            <?php endif; ?>
            
            <!-- Contenu de la page -->
            <main>
                <?php echo $content; ?>
            </main>
        </div>
    </div>
</body>
</html>