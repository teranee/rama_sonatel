<?php

namespace App\Controllers;

require_once __DIR__ . '/controller.php';
require_once __DIR__ . '/../models/model.php';
require_once __DIR__ . '/../services/session.service.php';
require_once __DIR__ . '/../enums/profile.enum.php';

use App\Models;
use App\Services;
use App\Enums;

// Affichage du tableau de bord
function dashboard() {
    global $session_services, $model;
    
    $session_services['start_session']();
    
    // Vérification de la connexion
    if (!$session_services['is_logged_in']()) {
        redirect('?page=login');
        return;
    }
    
    // Récupération des données
    $user = $session_services['get_current_user']();
    $data = $model['read_data']();
    
    // Calcul des statistiques
    $stats = [
        'active_learners' => count(array_filter($data['apprenants'] ?? [], function($apprenant) {
            return $apprenant['statut'] === 'Actif';
        })),
        'total_referentials' => count($data['referentiels'] ?? []),
        'active_promotions' => count(array_filter($data['promotions'] ?? [], function($promotion) {
            return $promotion['status'] === 'active';
        })),
        'total_promotions' => count($data['promotions'] ?? [])
    ];
    
    // Rendu de la vue avec toutes les données
    render('admin.layout.php', 'dashboard/dashboard.html.php', [
        'user' => $user,
        'active_menu' => 'dashboard',
        'stats' => $stats
    ]);
}

function dashboard_page() {
    global $model;
    
    // Lire les données du fichier JSON
    $data = $model['read_data']();
    
    // Calculer les statistiques
    $stats = [
        'active_learners' => count(array_filter($data['apprenants'] ?? [], function($apprenant) {
            return $apprenant['statut'] === 'active';
        })),
        'total_referentials' => count($data['referentiels'] ?? []),
        'active_promotions' => count(array_filter($data['promotions'] ?? [], function($promotion) {
            return $promotion['status'] === 'active';
        })),
        'total_promotions' => count($data['promotions'] ?? [])
    ];
    
    // Rendre la vue avec les statistiques
    render('admin.layout.php', 'dashboard/index.html.php', [
        'stats' => $stats
    ]);
}

  /* function show_dashboard() {
    global $model;

    $data = $model['read_data']();
    $promotions = $data['promotions'] ?? [];

    // Trouver la promo active
    $active_promotion = null;
    foreach ($promotions as $promotion) {
        if ($promotion['status'] === 'active') {
            $active_promotion = $promotion;
            break;
        }
    }

  // Calculer les stats uniquement à partir de la promo active
    $stats = [
        'active_learners' => 0,
        'total_referentials' => 0,
        'active_promotions' => 0,
        'total_promotions' => count($promotions)
    ];

    if ($active_promotion) {
        $stats['active_promotions'] = 1;
        $stats['active_learners'] = count($active_promotion['apprenants'] ?? []);
        $stats['total_referentials'] = count($active_promotion['referentiels'] ?? []);
    }

    render('admin.layout.php', 'dashboard/dashboard.html.php', [
        'stats' => $stats
    ]);
}
 */