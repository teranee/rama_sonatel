<?php

namespace App\Controllers;

require_once __DIR__ . '/controller.php';
require_once __DIR__ . '/../models/model.php';
require_once __DIR__ . '/../services/validator.service.php';
require_once __DIR__ . '/../services/session.service.php';
require_once __DIR__ . '/../services/file.service.php';

use App\Models;
use App\Services;

// Affichage de la liste des promotions
function list_promotions() {
    global $model;
    
    // Gestion du toggle de statut
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'toggle_status') {
        $promotion_id = $_POST['promotion_id'] ?? null;
        if ($promotion_id) {
            $model['toggle_promotion_status']((int)$promotion_id);
        }
        // Rediriger pour éviter la resoumission du formulaire
        header('Location: ?page=promotions');
        exit;
    }

    // Récupérer les paramètres
    $search = $_GET['search'] ?? '';
    $status_filter = $_GET['status'] ?? 'all';
    $referentiel_filter = $_GET['referentiel'] ?? 'all';
    $current_page = isset($_GET['page_num']) ? (int)$_GET['page_num'] : 1;
    $view_mode = $_GET['view'] ?? 'grid';
    
    // Nombre d'éléments par page selon le mode et le filtre
    $items_per_page = 5; // Par défaut pour le mode liste
    
    // En mode grille, ajuster selon le filtre
    if ($view_mode === 'grid') {
        if ($status_filter === 'inactive') {
            $items_per_page = 3; // 3 promotions inactives par page
        } else {
            $items_per_page = 2; // 2 promotions inactives par page (+ la promotion active)
        }
    }

    // Récupérer tous les référentiels pour les filtres
    $referentiels = $model['get_all_referentiels']();

    // Récupérer toutes les promotions
    $data = $model['read_data']();
    $all_promotions = array_map(function($promotion) {
        // Calculer le nombre d'apprenants pour chaque promotion
        $nb_apprenants = isset($promotion['apprenants']) ? count($promotion['apprenants']) : 0;
        
        // Formatage des dates
        $start_date = isset($promotion['date_debut']) ? $promotion['date_debut'] : 'Non définie';
        $end_date = isset($promotion['date_fin']) ? $promotion['date_fin'] : 'Non définie';
        
        // S'assurer que referentiels est un tableau
        if (!isset($promotion['referentiels'])) {
            $promotion['referentiels'] = [];
        }
        
        return array_merge([
            'start_date' => $start_date,
            'end_date' => $end_date,
            'apprenants' => [],
            'nb_apprenants' => $nb_apprenants,
            'status' => 'inactive',
            'referentiels' => []
        ], $promotion);
    }, $data['promotions'] ?? []);

    // Séparer la promotion active des promotions inactives
    $active_promotion = null;
    $inactive_promotions = [];
    
    foreach ($all_promotions as $promotion) {
        if ($promotion['status'] === 'active') {
            $active_promotion = $promotion;
        } else {
            $inactive_promotions[] = $promotion;
        }
    }
    
    // Préparer les promotions à filtrer et paginer
    $promotions_to_filter = [];
    
    // Logique de filtrage selon le mode et le statut
    if ($view_mode === 'grid') {
        if ($status_filter === 'active') {
            // En mode grille avec filtre "actifs", on ne filtre rien car on affichera uniquement la promotion active
            $promotions_to_filter = [];
        } else if ($status_filter === 'inactive') {
            // En mode grille avec filtre "inactifs", on filtre toutes les promotions inactives
            $promotions_to_filter = $inactive_promotions;
        } else {
            // En mode grille avec filtre "tous", on filtre uniquement les promotions inactives
            $promotions_to_filter = $inactive_promotions;
        }
    } else { // Mode liste
        if ($status_filter === 'active') {
            $promotions_to_filter = array_filter($all_promotions, function($p) {
                return $p['status'] === 'active';
            });
        } else if ($status_filter === 'inactive') {
            $promotions_to_filter = $inactive_promotions;
        } else {
            $promotions_to_filter = $all_promotions;
        }
    }
    
    // Pour le mode liste, s'assurer que la promotion active est toujours en haut
    if ($view_mode === 'list') {
        // Filtrer les promotions selon les critères
        $filtered_promotions = array_filter($all_promotions, function($promotion) use ($search, $status_filter, $referentiel_filter) {
            // Filtre par statut
            if ($status_filter !== 'all' && $promotion['status'] !== $status_filter) {
                return false;
            }
            
            // Filtre par nom
            if (!empty($search) && stripos($promotion['name'], $search) === false) {
                return false;
            }
            
            // Filtre par référentiel
            if ($referentiel_filter !== 'all' && !in_array($referentiel_filter, $promotion['referentiels'])) {
                return false;
            }
            
            return true;
        });
        
        // Séparer la promotion active des autres
        $active_promotion = null;
        $other_promotions = [];
        
        foreach ($filtered_promotions as $promotion) {
            if ($promotion['status'] === 'active') {
                $active_promotion = $promotion;
            } else {
                $other_promotions[] = $promotion;
            }
        }
        
        // Calculer la pagination pour les promotions non-actives
        $total_items = count($other_promotions);
        $total_pages = ceil($total_items / $items_per_page);
        $current_page = max(1, min($current_page, $total_pages > 0 ? $total_pages : 1));
        $offset = ($current_page - 1) * $items_per_page;
        
        // Extraire les promotions pour la page actuelle
        $paginated_promotions = array_slice($other_promotions, $offset, $items_per_page);
        
        // Préparer le tableau final des promotions à afficher
        $promotions_to_display = [];
        
        // Ajouter la promotion active en premier si elle existe et si le filtre le permet
        if ($active_promotion && ($status_filter === 'all' || $status_filter === 'active')) {
            $promotions_to_display[] = $active_promotion;
        }
        
        // Ajouter les autres promotions paginées
        foreach ($paginated_promotions as $promotion) {
            $promotions_to_display[] = $promotion;
        }
    } else { // Mode grille
        // Appliquer les filtres de recherche et de référentiel
        $filtered_promotions = array_filter($promotions_to_filter, function($promotion) use ($search, $referentiel_filter) {
            // Filtre par nom
            if (!empty($search) && stripos($promotion['name'], $search) === false) {
                return false;
            }
            
            // Filtre par référentiel
            if ($referentiel_filter !== 'all' && !in_array($referentiel_filter, $promotion['referentiels'])) {
                return false;
            }
            
            return true;
        });
        
        // Calculer la pagination
        $total_items = count($filtered_promotions);
        $total_pages = ceil($total_items / $items_per_page);
        $current_page = max(1, min($current_page, $total_pages > 0 ? $total_pages : 1));
        $offset = ($current_page - 1) * $items_per_page;
        
        // Extraire les promotions pour la page actuelle
        $paginated_promotions = array_slice($filtered_promotions, $offset, $items_per_page);
        
        // Préparer le tableau final des promotions à afficher
        $promotions_to_display = [];
        
        // En mode grille, gérer l'affichage selon le filtre
        if ($view_mode === 'grid') {
            if ($status_filter === 'active') {
                // En mode grille avec filtre "actifs", on affiche uniquement la promotion active
                if ($active_promotion) {
                    $promotions_to_display[] = $active_promotion;
                }
            } else if ($status_filter === 'inactive') {
                // En mode grille avec filtre "inactifs", on affiche uniquement les promotions inactives paginées
                $promotions_to_display = $paginated_promotions;
            } else {
                // En mode grille avec filtre "tous", on affiche la promotion active + les promotions inactives paginées
                if ($active_promotion) {
                    $promotions_to_display[] = $active_promotion;
                }
                foreach ($paginated_promotions as $promotion) {
                    $promotions_to_display[] = $promotion;
                }
            }
        } else { // Mode liste
            $promotions_to_display = $paginated_promotions;
        }
    }
    
    // Calculer les statistiques
    if ($view_mode === 'grid') {
        // En mode grille, les statistiques concernent uniquement la promotion active
        if ($active_promotion) {
            $total_apprenants = isset($active_promotion['nb_apprenants']) ? 
                $active_promotion['nb_apprenants'] : 
                (isset($active_promotion['apprenants']) ? count($active_promotion['apprenants']) : 0);
        
            $total_referentiels = isset($active_promotion['referentiels']) ? 
                count($active_promotion['referentiels']) : 0;
            
            $active_promotions = 1;
        } else {
            $total_apprenants = 0;
            $total_referentiels = 0;
            $active_promotions = 0;
        }
    } else {
        // En mode liste, les statistiques concernent toutes les promotions
        $total_apprenants = 0;
        $all_referentiels_ids = [];
        
        foreach ($all_promotions as $promotion) {
            // Compter les apprenants
            if (isset($promotion['nb_apprenants'])) {
                $total_apprenants += $promotion['nb_apprenants'];
            } else if (isset($promotion['apprenants']) && is_array($promotion['apprenants'])) {
                $total_apprenants += count($promotion['apprenants']);
            }
        
            // Collecter les IDs de référentiels
            if (isset($promotion['referentiels']) && is_array($promotion['referentiels'])) {
                $all_referentiels_ids = array_merge($all_referentiels_ids, $promotion['referentiels']);
            }
        }
        
        // Compter les référentiels uniques
        $total_referentiels = count(array_unique($all_referentiels_ids));
        
        // Compter les promotions actives
        $active_promotions = count(array_filter($all_promotions, function($p) {
            return isset($p['status']) && $p['status'] === 'active';
        }));
    }

    $page_stats = [
        'total_apprenants' => $total_apprenants,
        'total_referentiels' => $total_referentiels,
        'active_promotions' => $active_promotions,
        'total_promotions' => count($all_promotions),
        'total_items' => $total_items
    ];
    
    // Préparer un tableau de correspondance entre ID et nom de référentiel
    $referentiel_names = [];
    foreach ($referentiels as $ref) {
        $referentiel_names[$ref['id']] = $ref['name'];
    }

    // Pour chaque promotion, trier les référentiels par ordre alphabétique
    foreach ($promotions_to_display as &$promotion) {
        if (isset($promotion['referentiels']) && is_array($promotion['referentiels'])) {
            // Créer un tableau associatif id => nom pour pouvoir trier
            $refs_with_names = [];
            foreach ($promotion['referentiels'] as $ref_id) {
                if (isset($referentiel_names[$ref_id])) {
                    $refs_with_names[$ref_id] = $referentiel_names[$ref_id];
                }
            }
            
            // Trier par nom
            asort($refs_with_names);
            
            // Reconstruire le tableau des référentiels triés
            $promotion['referentiels'] = array_keys($refs_with_names);
            $promotion['referentiels_names'] = array_values($refs_with_names);
        }
    }
    unset($promotion); // Détruire la référence

    render('admin.layout.php', 'promotion/list.html.php', [
        'promotions' => $promotions_to_display,
        'current_page' => $current_page,
        'total_pages' => $total_pages,
        'view_mode' => $view_mode,
        'search' => $search,
        'status_filter' => $status_filter,
        'referentiel_filter' => $referentiel_filter,
        'referentiels' => $referentiels,
        'page_stats' => $page_stats
    ]);
}

// Modification du statut d'une promotion


// Affichage du formulaire d'ajout de promotion
 function add_promotion_form() {
    global $model, $validator_services,  $session_services;
    
    // Vérification des droits d'accès (Admin uniquement)
    $user = check_profile(\App\Enums\ADMIN);
    
    // Gestion de la suppression d'image temporaire
    if (isset($_GET['remove_image']) && $_GET['remove_image'] == 1) {
        if (isset($_SESSION['uploaded_promotion_image'])) {
            @unlink($_SESSION['uploaded_promotion_image']);
            unset($_SESSION['uploaded_promotion_image']);
            unset($_SESSION['uploaded_promotion_image_name']);
        }
        redirect('?page=add_promotion_form');
        return;
    }
    
    // Gestion de l'upload d'image temporaire via AJAX
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        // Valider l'image
        if ($validator_services['is_valid_image']($_FILES['image'])) {
            // Supprimer l'ancienne image temporaire si elle existe
            if (isset($_SESSION['uploaded_promotion_image'])) {
                @unlink($_SESSION['uploaded_promotion_image']);
            }
            
            // Générer un nom de fichier unique
            $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $filename = uniqid() . '.' . $extension;
            $upload_dir = __DIR__ . '/../uploads/temp/';
            
            // Créer le répertoire s'il n'existe pas
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $upload_path = $upload_dir . $filename;
            
            // Déplacer le fichier
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                $_SESSION['uploaded_promotion_image'] = $upload_path;
                $_SESSION['uploaded_promotion_image_name'] = $_FILES['image']['name'];
                
                // Si c'est une requête AJAX, renvoyer une réponse JSON
                if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => true,
                        'filename' => $_FILES['image']['name']
                    ]);
                    exit;
                }
                
                // Sinon, rediriger pour éviter la résoumission du formulaire
                redirect('?page=add_promotion_form');
                return;
            }
        }
    }
    
    // Récupération de tous les référentiels
    $referentiels = $model['get_all_referentiels']();
    
    // Préparer les variables pour la vue
    $uploaded_image = isset($_SESSION['uploaded_promotion_image']) ? $_SESSION['uploaded_promotion_image'] : null;
    $uploaded_image_name = isset($_SESSION['uploaded_promotion_image_name']) ? $_SESSION['uploaded_promotion_image_name'] : null;
    
    // Affichage de la vue
    render('admin.layout.php', 'promotion/add.html.php', [
        'user' => $user,
        'referentiels' => $referentiels,
        'uploaded_image' => $uploaded_image,
        'uploaded_image_name' => $uploaded_image_name,

    ]);
}function add_promotion_process() {
    global $model, $validator_services, $session_services;
    
    // Vérification des droits d'accès (Admin uniquement)

    $user = check_profile(\App\Enums\ADMIN);
    
    // Récupération des données du formulaire
    $name = $_POST['name'] ?? '';
    $date_debut = $_POST['date_debut'] ?? '';
    $date_fin = $_POST['date_fin'] ?? '';
    $referentiels = $_POST['referentiels'] ?? [];
    
    // Validation des données
    $errors = [];
    
    // Validation du nom
    if (empty(trim($name))) {
        $errors['name'] = 'Le nom de la promotion est obligatoire';
    } elseif ($model['promotion_name_exists']($name)) {
        $errors['name'] = 'Ce nom de promotion existe déjà';
    }
    
    // Validation des dates
    if (empty(trim($date_debut))) {
        $errors['date_debut'] = 'La date de début est obligatoire';
    } elseif (!$validator_services['is_valid_date']($date_debut)) {
        $errors['date_debut'] = 'La date de début doit être au format JJ/MM/AAAA';
    }
    
    if (empty(trim($date_fin))) {
        $errors['date_fin'] = 'La date de fin est obligatoire';
    } elseif (!$validator_services['is_valid_date']($date_fin)) {
        $errors['date_fin'] = 'La date de fin doit être au format JJ/MM/AAAA';
    }
    
    // Comparaison des dates
    if (!isset($errors['date_debut']) && !isset($errors['date_fin'])) {
        if (!$validator_services['is_date_after']($date_fin, $date_debut)) {
            $errors['date_fin'] = 'La date de fin doit être supérieure à la date de début';
        }
    }
    
    // Validation de l'image
    if (!isset($_SESSION['uploaded_promotion_image'])) {
        $errors['image'] = 'L\'image est obligatoire';
    }
    
    // Validation des référentiels
    if (empty($referentiels)) {
        $errors['referentiels'] = 'Au moins un référentiel doit être sélectionné';
    }
    
    // S'il y a des erreurs, affichage du formulaire avec les erreurs
    if (!empty($errors)) {
        $referentiels_data = $model['get_all_referentiels']();
        render('admin.layout.php', 'promotion/add.html.php', [
            'user' => $user,
            'errors' => $errors,
            'name' => $name,
            'date_debut' => $date_debut,
            'date_fin' => $date_fin,
            'referentiels_selected' => $referentiels,
            'referentiels' => $referentiels_data,
            'uploaded_image' => $_SESSION['uploaded_promotion_image'] ?? null,
            'uploaded_image_name' => $_SESSION['uploaded_promotion_image_name'] ?? null
        ]);
        return;
    }
    
    // Traitement de l'image
    $image_name = null;
    if (isset($_SESSION['uploaded_promotion_image'])) {
        // Déplacer l'image du dossier temporaire vers le dossier final
        $filename = basename($_SESSION['uploaded_promotion_image']);
        $final_dir = dirname(__DIR__, 2) . '/public/assets/images/uploads/promotions/';
        
        // Créer le répertoire s'il n'existe pas
        if (!is_dir($final_dir)) {
            mkdir($final_dir, 0777, true);
        }
        
        $final_path = $final_dir . $filename;
        
        if (copy($_SESSION['uploaded_promotion_image'], $final_path)) {
            $image_name = $filename;
            // Supprimer l'image temporaire
            @unlink($_SESSION['uploaded_promotion_image']);
        } else {
            $errors['image'] = 'Erreur lors de l\'enregistrement de l\'image';
            
            $referentiels_data = $model['get_all_referentiels']();
            render('admin.layout.php', 'promotion/add.html.php', [
                'user' => $user,
                'errors' => $errors,
                'name' => $name,
                'date_debut' => $date_debut,
                'date_fin' => $date_fin,
                'referentiels_selected' => $referentiels,
                'referentiels' => $referentiels_data,
                'uploaded_image' => $_SESSION['uploaded_promotion_image'] ?? null,
                'uploaded_image_name' => $_SESSION['uploaded_promotion_image_name'] ?? null
            ]);
            return;
        }
    }
    
    // Conversion des dates
    $date_debut_db = $validator_services['convert_date_to_db_format']($date_debut);
    $date_fin_db = $validator_services['convert_date_to_db_format']($date_fin);
    
    // Préparation des données de la promotion
    $promotion_data = [
        'name' => $name,
        'date_debut' => $date_debut_db,
        'date_fin' => $date_fin_db,
        'image' => $image_name,
        'status' => 'inactive',
        'apprenants' => [],
        'referentiels' => $referentiels
    ];
    
    // Création de la promotion
    $result = $model['create_promotion']($promotion_data);
    
    if (!$result) {
        $session_services['set_flash_message']('danger', 'Erreur lors de la création de la promotion');
        redirect('?page=add_promotion_form');
        return;
    }
    
    // Nettoyer les données de session
    unset($_SESSION['uploaded_promotion_image']);
    unset($_SESSION['uploaded_promotion_image_name']);
    
    // Redirection avec message de succès
    $session_services['set_flash_message']('success', 'Promotion créée avec succès');
    redirect('?page=promotions');
}// Affichage de l'image de la promotion
function display_promotion_image($promotion) {
    return '<img src="' . (isset($promotion['image']) ? 'assets/images/uploads/promotions/' . htmlspecialchars($promotion['image']) : 'assets/images/default-promotion.jpg') . '" 
                alt="' . htmlspecialchars($promotion['name']) . '" 
                class="promotion-avatar">';
}

// Fonction pour ajouter une promotion (simplifiée)
function add_promotion() {

    global $model, $session_services, $validator_services, $file_services;
    


    // Vérifier si l'utilisateur est connecté et a les droits d'administrateur
    $user = check_profile(\App\Enums\ADMIN);
    
    // Si la méthode est POST, traiter le formulaire
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Récupération des données du formulaire
        $name = $_POST['name'] ?? '';
        $date_debut = $_POST['date_debut'] ?? '';
        $date_fin = $_POST['date_fin'] ?? '';
        $referentiels = $_POST['referentiels'] ?? [];
        
        // Validation des données
        $errors = [];
        
        // Validation du nom
        if (empty(trim($name))) {
            $errors['name'] = 'Le nom de la promotion est obligatoire';
        } elseif ($model['promotion_name_exists']($name)) {
            $errors['name'] = 'Ce nom de promotion existe déjà';
        }
        
        // Validation des dates
        if (empty(trim($date_debut))) {
            $errors['date_debut'] = 'La date de début est obligatoire';
        } elseif (!$validator_services['is_valid_date']($date_debut)) {
            $errors['date_debut'] = 'La date de début doit être au format JJ/MM/AAAA';
        }
        
        if (empty(trim($date_fin))) {
            $errors['date_fin'] = 'La date de fin est obligatoire';
        } elseif (!$validator_services['is_valid_date']($date_fin)) {
            $errors['date_fin'] = 'La date de fin doit être au format JJ/MM/AAAA';
        }
        
        // Comparaison des dates
        if (!isset($errors['date_debut']) && !isset($errors['date_fin'])) {
            if (!$validator_services['is_date_after']($date_fin, $date_debut)) {
                $errors['date_fin'] = 'La date de fin doit être supérieure à la date de début';
            }
        }
        
        // Validation de l'image - SIMPLIFIÉE
        $image_name = 'assets/images/promotions/default.jpg'; // Valeur par défaut
        
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            // Vérifier le type et la taille de l'image
            if (!$validator_services['is_valid_image']($_FILES['image'])) {
                $errors['image'] = 'L\'image doit être au format JPG ou PNG et ne pas dépasser 2MB';
            } else {
                // Traiter l'image directement
                $image_result = $file_services['handle_promotion_image']($_FILES['image']);
                if ($image_result) {
                    $image_name = $image_result;
                } else {
                    $errors['image'] = 'Erreur lors du traitement de l\'image';
                }
            }
        }
        
        // S'il y a des erreurs, réafficher le formulaire
        if (!empty($errors)) {
            $referentiels_data = $model['get_all_referentiels']();
            render('admin.layout.php', 'promotion/add.html.php', [
                'user' => $user,
                'errors' => $errors,
                'name' => $name,
                'date_debut' => $date_debut,
                'date_fin' => $date_fin,
                'referentiels_selected' => $referentiels,
                'referentiels' => $referentiels_data
            ]);
            return;
        }
        
        // Conversion des dates
        $date_debut_db = $validator_services['convert_date_to_db_format']($date_debut);
        $date_fin_db = $validator_services['convert_date_to_db_format']($date_fin);
        
        // Préparation des données de la promotion
        $promotion_data = [
            'name' => $name,
            'date_debut' => $date_debut_db,
            'date_fin' => $date_fin_db,

            'image' => $image_name,
            'status' => 'inactive',
            'apprenants' => [],
            'referentiels' => $referentiels
        ];
        
        // Création de la promotion
        $result = $model['create_promotion']($promotion_data);
        
        if (!$result) {
            $session_services['set_flash_message']('danger', 'Erreur lors de la création de la promotion');
            redirect('?page=add_promotion_form');
            return;
        }
        
        // Redirection avec message de succès
        $session_services['set_flash_message']('success', 'Promotion créée avec succès');
        redirect('?page=promotions');
        return;
    }
    
    // Affichage du formulaire (GET request)
    $referentiels = $model['get_all_referentiels']();
    
    render('admin.layout.php', 'promotion/add.html.php', [
        'user' => $user,
        'referentiels' => $referentiels
    ]);
}

// Fonction de débogage pour les problèmes d'upload
function debug_file_upload($file) {
    $upload_max_filesize = ini_get('upload_max_filesize');
    $post_max_size = ini_get('post_max_size');
    
    $debug_info = [
        'file' => $file,
        'upload_max_filesize' => $upload_max_filesize,
        'post_max_size' => $post_max_size,
        'file_exists' => file_exists($file['tmp_name'] ?? ''),
        'is_uploaded_file' => is_uploaded_file($file['tmp_name'] ?? ''),
        'upload_dir_exists' => is_dir(dirname(__DIR__, 2) . '/public/assets/images/uploads/promotions'),
        'upload_dir_writable' => is_writable(dirname(__DIR__, 2) . '/public/assets/images/uploads/promotions')
    ];
    
    error_log('DEBUG FILE UPLOAD: ' . json_encode($debug_info));
    return $debug_info;
}

// Utilisez cette fonction dans votre traitement d'image
if (isset($_FILES['image'])) {
    debug_file_upload($_FILES['image']);
}

function upload_promotion_image() {
    global $model, $validator_services, $file_services, $session_services;
    
    // Vérifier l'authentification
    $user = check_auth();
    
    // Récupérer les données du formulaire pour les conserver
    $name = $_POST['name'] ?? '';
    $date_debut = $_POST['date_debut'] ?? '';
    $date_fin = $_POST['date_fin'] ?? '';
    $referentiels_selected = $_POST['referentiels'] ?? [];
    
    // Variables pour l'image
    $uploaded_image = null;
    $uploaded_image_name = null;
    $errors = [];
    
    // Traitement de l'image
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        if (!$validator_services['is_valid_image']($_FILES['image'])) {
            $errors['image'] = 'L\'image doit être au format JPG ou PNG et ne pas dépasser 2MB';
        } else {
            // Stocker le nom original
            $uploaded_image_name = $_FILES['image']['name'];
            
            // Traiter l'image
            $image_result = $file_services['handle_promotion_image']($_FILES['image']);
            if ($image_result) {
                $uploaded_image = $image_result;
                // Stocker l'image dans la session pour la récupérer plus tard
                $session_services['set_session']('uploaded_promotion_image', $uploaded_image);
                $session_services['set_session']('uploaded_promotion_image_name', $uploaded_image_name);
            } else {
                $errors['image'] = 'Erreur lors du traitement de l\'image';
            }
        }
    } else {
        $errors['image'] = 'Aucune image sélectionnée ou erreur lors du téléchargement';
    }
    
    // Récupérer les référentiels pour le formulaire
    $referentiels = $model['get_all_referentiels']();
    
    // Afficher le formulaire avec le résultat de l'upload
    render('admin.layout.php', 'promotion/add.html.php', [
        'user' => $user,
        'errors' => $errors,
        'name' => $name,
        'date_debut' => $date_debut,
        'date_fin' => $date_fin,
        'referentiels_selected' => $referentiels_selected,
        'referentiels' => $referentiels,
        'uploaded_image' => $uploaded_image,
        'uploaded_image_name' => $uploaded_image_name
    ]);
}

function manage_promotion($promotion_id) {
    global $model, $session_services;

    // Récupérer la promotion
    $promotion = $model['get_promotion_by_id']($promotion_id);

    if (!$promotion || $promotion['etat'] !== 'en cours') {
        $session_services['set_flash_message']('error', 'Vous ne pouvez gérer que les promotions en cours.');
        redirect('?page=promotions');
        return;
    }

    // Logique pour gérer la promotion en cours
    render('admin.layout.php', 'promotion/manage.html.php', [
        'promotion' => $promotion
    ]);
}

// Ajoutez cette fonction à votre contrôleur


/* function test_upload_process() {
    echo '<h1>Résultat du test</h1>';
    echo '<pre>';
    print_r($_FILES);
    echo '</pre>';
    
    if (isset($_FILES['test_image']) && $_FILES['test_image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/../../public/assets/images/uploads/test/';
        
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $filename = uniqid() . '.' . pathinfo($_FILES['test_image']['name'], PATHINFO_EXTENSION);
        $upload_path = $upload_dir . $filename;
        
        if (move_uploaded_file($_FILES['test_image']['tmp_name'], $upload_path)) {
            echo '<p style="color:green">Succès! Fichier uploadé: ' . $upload_path . '</p>';
            echo '<img src="/assets/images/uploads/test/' . $filename . '" style="max-width:300px">';
        } else {
            echo '<p style="color:red">Échec de l\'upload</p>';
        }
    } else {
        echo '<p style="color:red">Aucun fichier ou erreur d\'upload</p>';
    }
    
    echo '<p><a href="?page=test_upload">Retour au test</a></p>';
}
*/
function promotion_details() {
    global $model;

    // Récupérer l'ID de la promotion depuis l'URL
    $id = isset($_GET['id']) ? $_GET['id'] : null;

    if (!$id) {
        die('ID de la promotion manquant.');
    }

    // Charger les données des promotions depuis le modèle
    $promotions = $model['get_all_promotions']();

    // Trouver la promotion correspondante
    $promotion = array_filter($promotions, function ($promo) use ($id) {
        return $promo['id'] == $id;
    });

    if (empty($promotion)) {
        die('Promotion introuvable.');
    }

    $promotion = reset($promotion);

    // Rendre la vue des détails avec le layout admin
    render('admin.layout.php', 'promotion/details.html.php', [
        'promotion' => $promotion
    ]);
    // Afficher les détails de la promotion
    // echo '<h1>Détails de la promotion</h1>';
    // echo '<pre>';
    // print_r($promotion);
    // echo '</pre>';


}          
/* 'promotion' => $promotion    render('admin.layout.php', 'promotion/manage.html.php', [    // Logique pour gérer la promotion en cours    }        return;        redirect('?page=promotions');        $session_services['set_flash_message']('error', 'Vous ne pouvez gérer que les promotions en cours.');    if (!$promotion || $promotion['etat'] !== 'en cours') {    $promotion = $model['get_promotion_by_id']($promotion_id);    // Récupérer la promotion    global $model, $session_services;function manage_promotion($promotion_id) {}    ]);       
 */