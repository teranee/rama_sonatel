<?php

namespace App\Route;

// Inclure les fichiers nécessaires
require_once __DIR__ . '/../controllers/auth.controller.php';
require_once __DIR__ . '/../controllers/promotion.controller.php';
require_once __DIR__ . '/../controllers/referentiel.controller.php';
require_once __DIR__ . '/../controllers/dashboard.controller.php';
require_once __DIR__ . '/../controllers/export.controller.php';
require_once __DIR__ . '/../controllers/apprenant.controller.php';
require_once __DIR__ . '/../services/session.service.php';
require_once __DIR__ . '/../models/model.php';

use App\Controllers;
use App\Services;

// Démarrer la session
global $session_services;
if (isset($session_services) && is_array($session_services) && isset($session_services['start_session'])) {
    $session_services['start_session']();
} else {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

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
    'add_promotion_form' => 'App\Controllers\add_promotion_form',
    'add_promotion' => 'App\Controllers\add_promotion_process',
    'toggle_promotion_status' => 'App\Controllers\toggle_promotion_status',
    'promotion' => 'App\Controllers\promotion_details',
    
    // Routes pour les référentiels
    'referentiels' => 'App\Controllers\list_referentiels',
    'all-referentiels' => 'App\Controllers\all_referentiels_form',
    'add-referentiels' => 'App\Controllers\create_referentiel',
    'add-referentiel-process' => 'App\Controllers\save_referentiel',
    'assign-referentiels' => 'App\Controllers\manage_promos',
    'assign-referentiels-process' => 'App\Controllers\manage_promos_process',
    
    // Routes pour les apprenants
    'apprenants' => 'App\Controllers\list_apprenants',
    'add-apprenant-form' => 'App\Controllers\add_apprenant_form',
    'add-apprenant-process' => 'App\Controllers\add_apprenant_process',
    'edit-apprenant-form' => 'App\Controllers\edit_apprenant_form',
    'edit-apprenant' => 'App\Controllers\edit_apprenant_process',
    'delete-apprenant' => 'App\Controllers\delete_apprenant',
    'approve-apprenant' => 'App\Controllers\approve_apprenant',
    'edit-waiting-apprenant-form' => 'App\Controllers\edit_waiting_apprenant_form',
    'edit-waiting-apprenant-process' => 'App\Controllers\edit_waiting_apprenant_process',
    'delete-waiting-apprenant' => 'App\Controllers\delete_waiting_apprenant',
    'import-apprenants' => 'App\Controllers\import_apprenants_form',
    'import-apprenants-process' => 'App\Controllers\import_apprenants_process',
    'download-template' => 'App\Controllers\download_template',
    'export-apprenants' => 'App\Controllers\export_apprenants',
    'export-apprenants-html' => 'App\Controllers\export_apprenants_html',
    'export-apprenants-pdf' => 'App\Controllers\export_apprenants_pdf',
    
    // Route pour le tableau de bord

    'dashboard' =>'\App\Controllers\dashboard_page',
    
    
    // Routes pour les erreurs
    '403' => 'App\Controllers\forbidden',
    '404' => 'App\Controllers\not_found',
];

/**
 * Fonction pour gérer les requêtes et router vers le bon contrôleur
 */
function route() {
    global $routes, $session_services;
    
    // Liste des pages qui ne nécessitent pas d'authentification
    $public_pages = ['login', 'login-process', 'forgot-password', 'forgot-password-process', 'reset-password', 'reset-password-process'];
    
    // Récupération de la page demandée
    $page = isset($_GET['page']) ? $_GET['page'] : 'login';
    
    // Vérifier si l'utilisateur doit être connecté pour accéder à la page
    if (!in_array($page, $public_pages)) {
        $is_logged_in = false;
        
        // Vérifier si l'utilisateur est connecté
        if (isset($session_services) && is_array($session_services) && isset($session_services['is_logged_in'])) {
            $is_logged_in = $session_services['is_logged_in']();
        } else {
            $is_logged_in = isset($_SESSION['user']);
        }
        
        if (!$is_logged_in) {
            // Définir un message flash
            if (isset($session_services) && is_array($session_services) && isset($session_services['set_flash_message'])) {
                $session_services['set_flash_message']('danger', 'Veuillez vous connecter pour accéder à cette page');
            } else {
                $_SESSION['flash_message'] = [
                    'type' => 'danger',
                    'message' => 'Veuillez vous connecter pour accéder à cette page'
                ];
            }
            
            // Rediriger vers la page de connexion
            header('Location: ?page=login');
            exit;
        }
    }
    
    // Vérifier si la route existe
    if (isset($routes[$page])) {
        // Appeler la fonction correspondante
        call_user_func($routes[$page]);
    } else {
        // Route non trouvée, afficher la page 404
        header("HTTP/1.0 404 Not Found");
        if (isset($routes['404'])) {
            call_user_func($routes['404']);
        } else {
            echo "Page non trouvée";
        }
    }
}