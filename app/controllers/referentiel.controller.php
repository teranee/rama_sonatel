<?php

namespace App\Controllers;

require_once __DIR__ . '/controller.php';
require_once __DIR__ . '/../models/model.php';
require_once __DIR__ . '/../services/validator.service.php';
require_once __DIR__ . '/../services/session.service.php';
require_once __DIR__ . '/../translate/fr/error.fr.php';
require_once __DIR__ . '/../translate/fr/message.fr.php';
require_once __DIR__ . '/../enums/profile.enum.php';

use App\Models;
use App\Services;
use App\Translate\fr;
use App\Enums;
use Exception; // Ajoutez cette ligne

// Affichage de la liste des référentiels de la promotion en cours
function list_referentiels() {
    global $model, $session_services;

    // Vérifier l'authentification
    $user = $session_services['get_current_user']();
    if (!$user) {
        redirect('?page=login');
        return;
    }

    // Récupérer la promotion courante
    $current_promotion = $model['get_current_promotion']();

    // Vérifier si une promotion est active
    if (!$current_promotion) {
        $session_services['set_flash_message']('info', 'Aucune promotion active');
        redirect('?page=promotions');
        return;
    }

    // Récupérer les référentiels
    $referentiels = $model['get_referentiels_by_promotion']($current_promotion['id']);

    // Filtrer les référentiels selon la recherche
    $search = $_GET['search'] ?? '';
    if (!empty($search)) {
        $referentiels = array_filter($referentiels, function ($ref) use ($search) {
            return stripos($ref['name'], $search) !== false || 
                   stripos($ref['description'], $search) !== false;
        });
    }

    // Préparer les données pour la vue
    $data = [
        'current_promotion' => $current_promotion,
        'referentiels' => $referentiels,
        'search' => $search
    ];

    // Charger la vue
    render('admin.layout.php', 'referentiel/list.html.php', $data);
}

// Affichage de la liste de tous les référentiels
function all_referentiels_form() {
    global $model;

    // Récupération de tous les référentiels
    $all_referentiels = $model['get_all_referentiels']();

    // Pagination
    $page = isset($_GET['page_num']) ? (int)$_GET['page_num'] : 1;
    $limit = 6; // Nombre de cartes par page
    $total = count($all_referentiels);
    $pages = ceil($total / $limit);
    $offset = ($page - 1) * $limit;

    // Limiter les résultats pour la page courante
    $referentiels = array_slice($all_referentiels, $offset, $limit);

    // Affichage de la vue
    render('admin.layout.php', 'referentiel/list-all.html.php', [
        'referentiels' => $referentiels,
        'page' => $page,
        'pages' => $pages,
        'total' => $total
    ]);
}

// Affichage du formulaire de création de référentiel
function create_referentiel() {
    global $model, $session_services;
    
    // Vérifier si l'utilisateur est connecté et a les droits d'administrateur
    $user = check_profile(\App\Enums\ADMIN);
    
    // Afficher le formulaire
    render('admin.layout.php', 'referentiel/create_referentiel.html.php', [
        'user' => $user,
        'active_menu' => 'referentiels'
    ]);
}

// Traitement du formulaire de création de référentiel
function save_referentiel() {
    global $model, $session_services, $validator_services, $file_services;
    
    // Vérifier si l'utilisateur est connecté et a les droits d'administrateur
    $user = check_profile(\App\Enums\ADMIN);
    
    // Récupération des données du formulaire
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $capacity = $_POST['capacity'] ?? '';
    $sessions = $_POST['sessions'] ?? '';
    
    // Validation des données
    $errors = [];
    
    // Validation du nom
    if (empty(trim($name))) {
        $errors['name'] = 'Le nom du référentiel est obligatoire';
    } elseif ($model['referentiel_name_exists']($name)) {
        $errors['name'] = 'Ce nom de référentiel existe déjà';
    }
    
    // Validation de la description
    if (empty(trim($description))) {
        $errors['description'] = 'La description est obligatoire';
    }
    
    // Validation de la capacité
    if (empty($capacity)) {
        $errors['capacity'] = 'La capacité est obligatoire';
    } elseif (!is_numeric($capacity) || $capacity < 1) {
        $errors['capacity'] = 'La capacité doit être un nombre positif';
    }
    
    // Validation du nombre de sessions
    if (empty($sessions)) {
        $errors['sessions'] = 'Le nombre de sessions est obligatoire';
    } elseif (!is_numeric($sessions) || $sessions < 1) {
        $errors['sessions'] = 'Le nombre de sessions doit être un nombre positif';
    }
    
    // Validation de l'image
    $image_path = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png'];
        $max_size = 2 * 1024 * 1024; // 2MB
        
        if (!in_array($_FILES['image']['type'], $allowed_types)) {
            $errors['image'] = 'Le format de l\'image doit être JPG ou PNG';
        } elseif ($_FILES['image']['size'] > $max_size) {
            $errors['image'] = 'L\'image ne doit pas dépasser 2MB';
        } else {
            // Traiter l'image
            $image_result = $file_services['handle_referentiel_image']($_FILES['image']);
            if ($image_result) {
                $image_path = $image_result;
            } else {
                $errors['image'] = 'Erreur lors du traitement de l\'image';
            }
        }
    }
    
    // S'il y a des erreurs, réafficher le formulaire avec les erreurs
    if (!empty($errors)) {
        render('admin.layout.php', 'referentiel/create_referentiel.html.php', [
            'user' => $user,
            'active_menu' => 'referentiels',
            'errors' => $errors,
            'name' => $name,
            'description' => $description,
            'capacity' => $capacity,
            'sessions' => $sessions
        ]);
        return;
    }
    
    // Création du référentiel
    $referentiel_data = [
        'name' => $name,
        'description' => $description,
        'capacity' => (int)$capacity,
        'sessions' => (int)$sessions,
        'image' => $image_path ?? 'assets/images/referentiels/default.jpg'
    ];
    
    if ($model['create_referentiel']($referentiel_data)) {
        $session_services['set_flash_message']('success', 'Le référentiel a été créé avec succès');
        redirect('?page=all-referentiels');
    } else {
        $session_services['set_flash_message']('danger', 'Erreur lors de la création du référentiel');
        render('admin.layout.php', 'referentiel/create_referentiel.html.php', [
            'user' => $user,
            'active_menu' => 'referentiels',
            'name' => $name,
            'description' => $description,
            'capacity' => $capacity,
            'sessions' => $sessions
        ]);
    }
}

// Affichage du formulaire d'affectation de référentiels à une promotion
/* function assign_referentiels_form() {
    global $model, $session_services;

    // Vérification des droits d'accès (Admin uniquement)
    $user = check_profile(Enums\ADMIN);

    // Récupération de la promotion courante
    $current_promotion = $model['get_current_promotion']();

    if (!$current_promotion) {
        $session_services['set_flash_message']('info', 'Aucune promotion active. Veuillez d\'abord activer une promotion.');
        redirect('?page=promotions');
        return;
    }

    // Récupération de tous les référentiels
    $all_referentiels = $model['get_all_referentiels']();

    // Récupération des référentiels déjà affectés à la promotion
    $assigned_referentiels = $model['get_referentiels_by_promotion']($current_promotion['id']);
    $assigned_ids = array_map(function($ref) {
        return $ref['id'];
    }, $assigned_referentiels);

    // Filtrer les référentiels non affectés
    $unassigned_referentiels = array_filter($all_referentiels, function($ref) use ($assigned_ids) {
        return !in_array($ref['id'], $assigned_ids);
    });

    // Pagination
    $page = isset($_GET['page_num']) ? (int)$_GET['page_num'] : 1;
    $limit = 3; // Nombre de cartes par page
    $total = count($unassigned_referentiels);
    $pages = ceil($total / $limit);
    $offset = ($page - 1) * $limit;

    // Limiter les résultats pour la page courante
    $unassigned_referentiels = array_slice($unassigned_referentiels, $offset, $limit);

    // Affichage de la vue
    render('admin.layout.php', 'referentiel/assign.html.php', [
        'user' => $user,
        'current_promotion' => $current_promotion,
        'unassigned_referentiels' => array_values($unassigned_referentiels),
        'page' => $page,
        'pages' => $pages,
        'total' => $total
    ]);
}

// Traitement de l'affectation de référentiels à une promotion
function assign_referentiels_process() {
    global $model, $session_services, $error_messages, $success_messages;
    
    // Vérification des droits d'accès (Admin uniquement)
    check_profile(Enums\ADMIN);
    
    // Récupération de la promotion courante
    $current_promotion = $model['get_current_promotion']();
    
    if (!$current_promotion) {
        $session_services['set_flash_message']('info', 'Aucune promotion active. Veuillez d\'abord activer une promotion.');
        redirect('?page=promotions');
        return;
    }
    
    // Récupération des référentiels sélectionnés
    $selected_referentiels = $_POST['referentiels'] ?? [];
    
    if (empty($selected_referentiels)) {
        $session_services['set_flash_message']('info', 'Aucun référentiel sélectionné.');
        redirect('?page=assign-referentiels');
        return;
    }
    
    // Affectation des référentiels à la promotion
    $result = $model['assign_referentiels_to_promotion']($current_promotion['id'], $selected_referentiels);
    
    if (!$result) {
        $session_services['set_flash_message']('danger', $error_messages['referentiel']['update_failed']);
        redirect('?page=assign-referentiels');
        return;
    }
    
    // Redirection vers la liste des référentiels de la promotion avec un message de succès
    $session_services['set_flash_message']('success', $success_messages['referentiel']['assigned']);
    redirect('?page=referentiels');
} */

function assign_referentiels_to_promotion() {
    global $model, $session_services;
    
    $promotion_id = $_POST['promotion_id'] ?? null;
    $referentiel_ids = $_POST['referentiel_ids'] ?? [];
    
    if (!$promotion_id || !is_array($referentiel_ids)) {
        $session_services['set_flash_message']('error', 'Données invalides');
        redirect('?page=referentiels');
        return;
    }
    
    $result = $model['assign_referentiels_to_promotion']($promotion_id, $referentiel_ids);
    
    if ($result) {
        $session_services['set_flash_message']('success', 'Référentiels assignés avec succès');
    } else {
        $session_services['set_flash_message']('error', 'Erreur lors de l\'assignation');
    }
    
    redirect('?page=referentiels');
}
function remove_from_promo() {
    global $model, $session_services;

    // Vérification des droits d'accès (Admin uniquement)
    check_profile(\App\Enums\ADMIN);

    // Récupérer l'ID du référentiel et de la promotion active
    $referentiel_id = $_GET['referentiel_id'] ?? null;
    $current_promotion = $model['get_current_promotion']();

    if (!$referentiel_id || !$current_promotion) {
        $session_services['set_flash_message']('error', 'Référentiel ou promotion introuvable.');
        redirect('?page=referentiels');
        return;
    }

    // Désaffecter le référentiel de la promotion
    $result = $model['remove_referentiel_from_promo']($current_promotion['id'], $referentiel_id);

    if ($result) {
        $session_services['set_flash_message']('success', 'Référentiel désaffecté avec succès.');
    } else {
        $session_services['set_flash_message']('error', 'Erreur lors de la désaffectation du référentiel.');
    }

    redirect('?page=referentiels');
}

function manage_promos() {
    global $model, $session_services;

    // Vérification des droits d'accès (Admin uniquement)
    check_profile(\App\Enums\ADMIN);

    // Récupérer la promotion active
    $current_promotion = $model['get_current_promotion']();

    if (!$current_promotion) {
        $session_services['set_flash_message']('info', 'Aucune promotion active.');
        redirect('?page=promotions');
        return;
    }

    // Récupérer les référentiels assignés et non assignés
    $all_referentiels = $model['get_all_referentiels']();
    $assigned_referentiels = $model['get_referentiels_by_promotion']($current_promotion['id']);
    $assigned_ids = array_map(fn($ref) => $ref['id'], $assigned_referentiels);

    $unassigned_referentiels = array_filter($all_referentiels, fn($ref) => !in_array($ref['id'], $assigned_ids));

    // Affichage de la vue
    render('admin.layout.php', 'referentiel/manage-promos.html.php', [
        'current_promotion' => $current_promotion,
        'assigned_referentiels' => $assigned_referentiels,
        'unassigned_referentiels' => $unassigned_referentiels,
    ]);
}
function manage_promos_process() {
    global $model, $session_services;

    // Vérification des droits d'accès (Admin uniquement)
    check_profile(\App\Enums\ADMIN);

    // Récupérer la promotion active
    $current_promotion = $model['get_current_promotion']();

    if (!$current_promotion) {
        $session_services['set_flash_message']('error', 'Aucune promotion active.');
        redirect('?page=promotions');
        return;
    }

    // Récupérer l'action
    $action = $_POST['action'] ?? null;

    // Gérer les actions
    if ($action === 'add') {
        $assign_referentiel = $_POST['assign_referentiel'] ?? null;
        if ($assign_referentiel) {
            $model['assign_referentiels_to_promotion']($current_promotion['id'], [$assign_referentiel]);
        }
    } elseif ($action === 'remove') {
        $remove_referentiel = $_POST['remove_referentiel'] ?? null;
        if ($remove_referentiel) {
            $model['remove_referentiel_from_promo']($current_promotion['id'], $remove_referentiel);
        }
    } elseif ($action === 'finish') {
        $session_services['set_flash_message']('success', 'Informations gérées avec succès.');
        redirect('?page=referentiels');
        return;
    }

    // Recharger la page sans redirection
    redirect('?page=assign-referentiels');
}
function manage_referentiels($promotion_id) {
    global $model, $session_services;

    // Récupérer la promotion
    $promotion = $model['get_promotion_by_id']($promotion_id);

    // Vérifier si la promotion est en cours
    if (!$promotion || $promotion['etat'] !== 'en cours') {
        $session_services['set_flash_message']('error', 'Impossible de modifier les référentiels : la promotion n\'est pas en cours.');
        redirect('?page=referentiels');
        return;
    }

    // Charger les référentiels associés
    $referentiels = $model['get_all_referentiels']();

    // Afficher la page de gestion des référentiels
    render('admin.layout.php', 'referentiel/manage.html.php', [
        'referentiels' => $referentiels,
        'promotion' => $promotion
    ]);
}