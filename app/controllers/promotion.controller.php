<?php

namespace App\Controllers;

require_once __DIR__ . '/controller.php';
require_once __DIR__ . '/../models/model.php';
require_once __DIR__ . '/../services/validator.service.php';
require_once __DIR__ . '/../services/session.service.php';
require_once __DIR__ . '/../services/file.service.php';
require_once __DIR__ . '/../translate/fr/error.fr.php';
require_once __DIR__ . '/../translate/fr/message.fr.php';
require_once __DIR__ . '/../enums/profile.enum.php';
require_once __DIR__ . '/../enums/status.enum.php'; // Ajout de cette ligne
require_once __DIR__ . '/../enums/messages.enum.php';

use App\Models;
use App\Services;
use App\Translate\fr;
use App\Enums;
use App\Enums\Status; // Ajout de cette ligne
use App\Enums\Messages;

// Affichage de la liste des promotions
function list_promotions() {
    global $model, $session_services;
    
    // Vérification si l'utilisateur est connecté
    $user = check_auth();
    
    // Récupérer les statistiques
    $stats = $model['get_statistics']();
    
    // Récupérer le terme de recherche depuis GET
    $search = $_GET['search'] ?? '';
    
    // Récupérer la page courante et le nombre d'éléments par page
    $current_page = isset($_GET['page_num']) ? (int)$_GET['page_num'] : 1;
    $items_per_page = 5; // Modification de 6 à 8 éléments par page
    
    // Récupérer toutes les promotions
    $promotions = $model['get_all_promotions']();
    
    // Filtrer les promotions si un terme de recherche est présent
    if (!empty($search)) {
        $promotions = array_filter($promotions, function($promotion) use ($search) {
            return stripos($promotion['name'], $search) !== false;
        });
    }
    
    // Calculer le nombre total de pages
    $total_items = count($promotions);
    $total_pages = ceil($total_items / $items_per_page);
    
    // S'assurer que la page courante est valide
    $current_page = max(1, min($current_page, $total_pages));
    
    // Calculer l'offset pour la pagination
    $offset = ($current_page - 1) * $items_per_page;
    
    // Récupérer les promotions pour la page courante
    $paginated_promotions = array_slice(array_values($promotions), $offset, $items_per_page);
    
    // Rendu de la vue avec les statistiques
    render('admin.layout.php', 'promotion/list.html.php', [
        'user' => $user,
        'promotions' => $paginated_promotions,
        'search' => $search,
        'active_menu' => 'promotions',
        'current_page' => $current_page,
        'total_pages' => $total_pages,
        'items_per_page' => $items_per_page,
        'total_items' => $total_items,
        'stats' => $stats  // S'assurer que les stats sont passées ici
    ]);
}

// Affichage du formulaire d'ajout d'une promotion
function add_promotion_form() {
    global $model;
    
    // Vérification des droits d'accès (Admin uniquement)
    $user = check_profile(Enums\ADMIN);
    
    // Affichage de la vue
    render('admin.layout.php', 'promotion/add.html.php', [
        'user' => $user
    ]);
}

// Traitement de l'ajout d'une promotion
function add_promotion_process() {
    global $model, $validator_services, $session_services, $error_messages, $success_messages;
    
    // Vérification des droits d'accès (Admin uniquement)
    $user = check_profile(Enums\ADMIN);
    
    // Récupération des données du formulaire
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $date_debut = $_POST['date_debut'] ?? '';
    $date_fin = $_POST['date_fin'] ?? '';
    $image = $_FILES['image'] ?? null;
    
    // Validation des données
    $errors = [];
    
    if ($validator_services['is_empty']($name)) {
        $errors['name'] = $error_messages['form']['required'];
    } elseif ($model['promotion_name_exists']($name)) {
        $errors['name'] = $error_messages['promotion']['name_exists'];
    }
    
    if ($validator_services['is_empty']($description)) {
        $errors['description'] = $error_messages['form']['required'];
    }
    
    if ($validator_services['is_empty']($date_debut)) {
        $errors['date_debut'] = $error_messages['form']['required'];
    }
    
    if ($validator_services['is_empty']($date_fin)) {
        $errors['date_fin'] = $error_messages['form']['required'];
    } elseif (strtotime($date_fin) <= strtotime($date_debut)) {
        $errors['date_fin'] = 'La date de fin doit être postérieure à la date de début';
    }
    
    if (empty($image) || empty($image['tmp_name'])) {
        $errors['image'] = $error_messages['form']['required'];
    } elseif (!$validator_services['is_valid_image']($image)) {
        $errors['image'] = $error_messages['form']['invalid_image'];
    }
    
    // S'il y a des erreurs, affichage du formulaire avec les erreurs
    if (!empty($errors)) {
        render('admin.layout.php', 'promotion/add.html.php', [
            'user' => $user,
            'errors' => $errors,
            'name' => $name,
            'description' => $description,
            'date_debut' => $date_debut,
            'date_fin' => $date_fin
        ]);
        return;
    }
    
    // Téléchargement de l'image
    $image_path = upload_image($image, 'promotions');
    
    if ($image_path === false) {
        $session_services['set_flash_message']('danger', 'Erreur lors du téléchargement de l\'image');
        render('admin.layout.php', 'promotion/add.html.php', [
            'user' => $user,
            'name' => $name,
            'description' => $description,
            'date_debut' => $date_debut,
            'date_fin' => $date_fin
        ]);
        return;
    }
    
    // Création de la promotion
    $promotion_data = [
        'name' => $name,
        'description' => $description,
        'date_debut' => $date_debut,
        'date_fin' => $date_fin,
        'image' => $image_path
    ];
    
    $result = $model['create_promotion']($promotion_data);
    
    if (!$result) {
        $session_services['set_flash_message']('danger', $error_messages['promotion']['create_failed']);
        render('admin.layout.php', 'promotion/add.html.php', [
            'user' => $user,
            'name' => $name,
            'description' => $description,
            'date_debut' => $date_debut,
            'date_fin' => $date_fin
        ]);
        return;
    }
    
    // Redirection vers la liste des promotions avec un message de succès
    $session_services['set_flash_message']('success', $success_messages['promotion']['created']);
    redirect('?page=promotions');
}

// Modification du statut d'une promotion (activation/désactivation)
function toggle_promotion_status() {
    global $model, $session_services;
    
    // Vérification de l'authentification
    check_auth();
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirect('?page=promotions');
        return;
    }
    
    $promotion_id = filter_input(INPUT_POST, 'promotion_id', FILTER_VALIDATE_INT);
    if (!$promotion_id) {
        $session_services['set_flash_message']('error', Messages::PROMOTION_ERROR->value);
        redirect('?page=promotions');
        return;
    }
    
    $result = $model['toggle_promotion_status']($promotion_id);
    
    if ($result) {
        $message = $result['status'] === Status::ACTIVE->value ? 
                  Messages::PROMOTION_ACTIVATED->value : 
                  Messages::PROMOTION_INACTIVE->value;
        $session_services['set_flash_message']('success', $message);
    } else {
        $session_services['set_flash_message']('error', Messages::PROMOTION_ERROR->value);
    }
    
    redirect('?page=promotions');
}

// Ajout d'une promotion
function add_promotion() {
    global $model, $session_services, $validator_services, $file_services;
    
    // Vérification de l'authentification
    $user = check_auth();
    
    // Vérification de la méthode POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $session_services['set_flash_message']('error', Messages::INVALID_REQUEST->value);
        redirect('?page=promotions');
        return;
    }
    
    // Validation des données
    $validation = $validator_services['validate_promotion']($_POST, $_FILES);
    
    if (!$validation['valid']) {
        $session_services['set_flash_message']('error', $validation['errors'][0]);
        redirect('?page=promotions');
        return;
    }
    
    // Traitement de l'image avec le service
    $image_path = $file_services['handle_promotion_image']($_FILES['image']);
    if (!$image_path) {
        $session_services['set_flash_message']('error', Messages::IMAGE_UPLOAD_ERROR->value);
        redirect('?page=promotions');
        return;
    }
    
    // Préparation des données
    $promotion_data = [
        'name' => htmlspecialchars($_POST['name']),
        'date_debut' => $_POST['date_debut'],
        'date_fin' => $_POST['date_fin'],
        'image' => $image_path,
        'status' => 'inactive',
        'apprenants' => []
    ];
    
    // Création de la promotion
    $result = $model['create_promotion']($promotion_data);
    
    if (!$result) {
        $session_services['set_flash_message']('error', Messages::PROMOTION_CREATE_ERROR->value);
        redirect('?page=promotions');
        return;
    }

    $session_services['set_flash_message']('success', Messages::PROMOTION_CREATED->value);
    redirect('?page=promotions');
}

// Recherche des référentiels
function search_referentiels() {
    global $model;
    
    // Vérification si l'utilisateur est connecté
    check_auth();
    
    $query = $_GET['q'] ?? '';
    $referentiels = $model['search_referentiels']($query);
    
    // Retourner les résultats en JSON
    header('Content-Type: application/json');
    echo json_encode(array_values($referentiels));
    exit;
}

// Affichage de la page de promotion
