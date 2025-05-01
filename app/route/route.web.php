<?php

namespace App\Route;

require_once __DIR__ . '/../controllers/auth.controller.php';
require_once __DIR__ . '/../controllers/promotion.controller.php';
require_once __DIR__ . '/../controllers/referentiel.controller.php';
require_once __DIR__ . '/../controllers/dashboard.controller.php';

use App\Controllers;

// Définition des routes
$routes = [
    // Routes pour l'authentification
    'login' => 'App\Controllers\login_page',
    'login-process' => 'App\Controllers\login_process',
    'logout' => 'App\Controllers\logout',
    'change-password' => 'App\Controllers\change_password_page',
    'change-password-process' => 'App\Controllers\change_password_process',
    'forgot-password' => 'App\Controllers\forgot_password_page',
    'forgot-password-process' => 'App\Controllers\forgot_password_process',
    'reset-password' => 'App\Controllers\reset_password_page',
    'reset-password-process' => 'App\Controllers\reset_password_process',
    
    // Routes pour les promotions
    'promotions' => 'App\Controllers\list_promotions',
    'add_promotion_form' => 'App\Controllers\add_promotion_form', // Correction du nom de la route
    'add_promotion' => 'App\Controllers\add_promotion_process',   // Correction du nom de la route
    'toggle_promotion_status' => 'App\Controllers\toggle_promotion_status',
    'promotion' => 'App\Controllers\promotion_details',
    
    // Routes pour les référentiels
    'referentiels' => 'App\Controllers\list_referentiels',
    'all-referentiels' => 'App\Controllers\all_referentiels_form',
    'add-referentiel' => 'App\Controllers\add_referentiel_form',
    'add-referentiel-process' => 'App\Controllers\add_referentiel_process',
    'assign-referentiels' => 'App\Controllers\assign_referentiels_form',
    'assign-referentiels-process' => 'App\Controllers\assign_referentiels_process',
    'add-to-promo' => 'App\Controllers\add_to_promo',
    'remove-from-promo' => 'App\Controllers\remove_from_promo',
    // Autres routes...
    'manage-promos' => 'App\Controllers\manage_promos',
    'manage-promos-process' => 'App\Controllers\manage_promos_process',
    // Route par défaut pour le tableau de bord
    'dashboard' => 'App\Controllers\dashboard',
    
    // Route pour les erreurs
    'forbidden' => 'App\Controllers\forbidden',
    
    // Route par défaut (page non trouvée)
    '404' => 'App\Controllers\not_found',
    'test_upload' => 'App\Controllers\test_upload',
    'test_upload_process' => 'App\Controllers\test_upload_process',
    // Ajouter ces routes dans le tableau des routes
    'apprenants' => 'App\Controllers\list_apprenants',
    'add-apprenant-form' => 'App\Controllers\add_apprenant_form',
    'add-apprenant' => 'App\Controllers\add_apprenant',
    'view-apprenant' => 'App\Controllers\view_apprenant',
    'edit-apprenant-form' => 'App\Controllers\edit_apprenant_form',
    'edit-apprenant' => 'App\Controllers\edit_apprenant',
    'delete-apprenant' => 'App\Controllers\delete_apprenant',
    'export-apprenants' => 'App\Controllers\export_apprenants',
    // Routes pour l'exportation
    'export-apprenants-pdf' => 'App\Controllers\export_apprenants_pdf',
    'export-apprenants-excel' => 'App\Controllers\export_apprenants_excel',
];

/**
 * Fonction de routage qui exécute le contrôleur correspondant à la page demandée
 *
 * @param string $page La page demandée
 * @return mixed Le résultat de la fonction contrôleur
 */
function route($page) {
    global $routes;
    
    // Vérifie si la route demandée existe
    $route_exists = array_key_exists($page, $routes);
    
    // Si la route n'existe pas, renvoyer vers la page 404
    if (!$route_exists) {
        return call_user_func($routes['404']);
    }
    
    // Exécute la fonction contrôleur
    return call_user_func($routes[$page]);
}