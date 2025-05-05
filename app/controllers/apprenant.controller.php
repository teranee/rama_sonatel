<?php

namespace App\Controllers;

require_once __DIR__ . '/controller.php';
require_once __DIR__ . '/../models/model.php';
require_once __DIR__ . '/../services/validator.service.php';
require_once __DIR__ . '/../services/session.service.php';
require_once __DIR__ . '/../services/file.service.php';

use App\Models;
use App\Services;

// Affichage de la liste des apprenants
function list_apprenants() {
    global $model, $session_services;
    
    // Vérifier si l'utilisateur est connecté et a les droits d'administrateur
    $user = check_profile(\App\Enums\ADMIN);
    
    // Récupérer les paramètres de pagination et de filtrage
    $current_page = isset($_GET['page_num']) ? (int)$_GET['page_num'] : 1;
    $items_per_page = isset($_GET['items_per_page']) ? (int)$_GET['items_per_page'] : 10;
    $search = $_GET['search'] ?? '';
    $referentiel_filter = $_GET['referentiel'] ?? 'all';
    $statut_filter = $_GET['status'] ?? 'all';
    $waiting_list = isset($_GET['waiting_list']);
    
    // Récupérer la promotion active
    $current_promotion = $model['get_current_promotion']();    
    if (!$current_promotion) {
        $session_services['set_flash_message']('warning', 'Aucune promotion active. Veuillez activer une promotion.');
        redirect('?page=promotions');
        return;
    }
    
    // Récupérer tous les référentiels pour les filtres
    $referentiels = $model['get_all_referentiels']();
    
    if ($waiting_list) {
        // Récupérer la liste d'attente
        $all_apprenants = $model['get_waiting_list']($current_promotion['id']);
    } else {
        // Récupérer les apprenants de la promotion active
        $all_apprenants = $model['get_apprenants_by_promotion']($current_promotion['id']);
    }
    
    // Filtrer les apprenants selon les critères
    $filtered_apprenants = array_filter($all_apprenants, function($apprenant) use ($search, $referentiel_filter, $statut_filter) {
        // Filtre de recherche
        if (!empty($search)) {
            $nom_complet = strtolower($apprenant['prenom'] . ' ' . $apprenant['nom']);
            $matricule = strtolower($apprenant['matricule']);
            if (strpos($nom_complet, strtolower($search)) === false && 
                strpos($matricule, strtolower($search)) === false) {
                return false;
            }
        }
        
        // Filtre par référentiel
        if ($referentiel_filter !== 'all') {
            // Vérifier si le nom du référentiel correspond au filtre
            // Vous pouvez adapter cette logique selon la façon dont vous stockez les référentiels
            if (!isset($apprenant['referentiel']) || 
                strtolower($apprenant['referentiel']) !== strtolower($referentiel_filter)) {
                return false;
            }
        }
        
        // Filtre par statut
        if ($statut_filter = $_GET['status'] ?? 'all'!== 'all' && $apprenant['statut'] !== $statut_filter) {
            return false;
        }
        
        return true;
    });
    
    // Calculer la pagination
    $total_items = count($filtered_apprenants);
    $total_pages = ceil($total_items / $items_per_page);
    $current_page = max(1, min($current_page, $total_pages > 0 ? $total_pages : 1));
    $offset = ($current_page - 1) * $items_per_page;
    
    // Extraire les apprenants pour la page actuelle
    $apprenants = array_slice($filtered_apprenants, $offset, $items_per_page);
    
    // Récupérer tous les référentiels pour le filtre
    $referentiels = $model['get_all_referentiels']();
    
    // Afficher la vue
    render('admin.layout.php', 'apprenant/list.html.php', [
        'user' => $user,
        'active_menu' => 'apprenants',
        'current_promotion' => $current_promotion,
        'apprenants' => $apprenants,
        'referentiels' => $referentiels,
        'search' => $search,
        'referentiel_filter' => $referentiel_filter,
        'statut_filter' => $statut_filter = $_GET['status'] ?? 'all',
        'current_page' => $current_page,
        'total_pages' => $total_pages,
        'items_per_page' => $items_per_page,
        'offset' => $offset,
        'total_items' => $total_items
    ]);
}

// Affichage du formulaire d'ajout d'un apprenant
function add_apprenant_form() {
    global $model, $session_services;
    
    // Vérifier l'authentification
    $user = check_auth();
    
    // Récupérer la promotion courante
    $current_promotion = $model['get_current_promotion']();
    
    // Si aucune promotion n'est active
    if (!$current_promotion) {
        $session_services['set_flash_message']('info', 'Aucune promotion active pour ajouter un apprenant');
        redirect('?page=promotions');
        return;
    }
    
    // Récupérer tous les référentiels
    $referentiels = $model['get_all_referentiels']();
    
    // Affichage de la vue
    render('admin.layout.php', 'apprenant/add.html.php', [
        'user' => $user,
        'active_menu' => 'apprenants',
        'current_promotion' => $current_promotion,
        'referentiels' => $referentiels
    ]);
}

// Traitement du formulaire d'ajout d'un apprenant
function add_apprenant() {
    global $model, $validator_services, $session_services, $file_services;
    
    // Vérifier l'authentification
    $user = check_auth();
    
    // Si la méthode n'est pas POST, rediriger vers le formulaire
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirect('?page=add-apprenant-form');
        return;
    }
    
    // Récupérer la promotion courante
    $current_promotion = $model['get_current_promotion']();
    
    // Si aucune promotion n'est active
    if (!$current_promotion) {
        $session_services['set_flash_message']('info', 'Aucune promotion active pour ajouter un apprenant');
        redirect('?page=promotions');
        return;
    }
    
    // Récupération des données du formulaire
    $prenom = $_POST['prenom'] ?? '';
    $nom = $_POST['nom'] ?? '';
    $email = $_POST['email'] ?? '';
    $telephone = $_POST['telephone'] ?? '';
    $date_naissance = $_POST['date_naissance'] ?? '';
    $adresse = $_POST['adresse'] ?? '';
    $referentiel_id = $_POST['referentiel_id'] ?? '';
    
    // Validation des données
    $errors = [];
    
    if (empty(trim($prenom))) {
        $errors['prenom'] = 'Le prénom est obligatoire';
    }
    
    if (empty(trim($nom))) {
        $errors['nom'] = 'Le nom est obligatoire';
    }
    
    if (empty(trim($email))) {
        $errors['email'] = 'L\'email est obligatoire';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'L\'email n\'est pas valide';
    } elseif ($model['email_exists']($email)) {
        $errors['email'] = 'Cet email est déjà utilisé';
    }
    
    if (empty(trim($telephone))) {
        $errors['telephone'] = 'Le téléphone est obligatoire';
    }
    
    if (empty(trim($date_naissance))) {
        $errors['date_naissance'] = 'La date de naissance est obligatoire';
    }
    
    if (empty(trim($referentiel_id))) {
        $errors['referentiel_id'] = 'Le référentiel est obligatoire';
    }
    
    // Traitement de la photo
    $photo_path = 'assets/images/default-avatar.png'; // Valeur par défaut
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png'];
        $max_size = 2 * 1024 * 1024; // 2MB
        
        if (!in_array($_FILES['photo']['type'], $allowed_types)) {
            $errors['photo'] = 'Le format de la photo doit être JPG ou PNG';
        } elseif ($_FILES['photo']['size'] > $max_size) {
            $errors['photo'] = 'La photo ne doit pas dépasser 2MB';
        } else {
            // Traiter la photo
            $upload_dir = __DIR__ . '/../../public/assets/images/uploads/apprenants/';
            
            // Créer le répertoire s'il n'existe pas
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            // Générer un nom de fichier unique
            $filename = uniqid() . '_' . basename($_FILES['photo']['name']);
            $upload_path = $upload_dir . $filename;
            
            // Déplacer le fichier téléchargé
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $upload_path)) {
                $photo_path = 'assets/images/uploads/apprenants/' . $filename;
            } else {
                $errors['photo'] = 'Erreur lors du téléchargement de la photo';
            }
        }
    }
    
    // S'il y a des erreurs, réafficher le formulaire avec les erreurs
    if (!empty($errors)) {
        $referentiels = $model['get_all_referentiels']();
        render('admin.layout.php', 'apprenant/add.html.php', [
            'user' => $user,
            'active_menu' => 'apprenants',
            'current_promotion' => $current_promotion,
            'referentiels' => $referentiels,
            'errors' => $errors,
            'prenom' => $prenom,
            'nom' => $nom,
            'email' => $email,
            'telephone' => $telephone,
            'date_naissance' => $date_naissance,
            'adresse' => $adresse,
            'referentiel_id' => $referentiel_id
        ]);
        return;
    }
    
    // Générer un matricule unique
    $matricule = $model['generate_matricule']();
    
    // Préparation des données de l'apprenant
    $apprenant_data = [
        'id' => uniqid(),
        'matricule' => $matricule,
        'prenom' => $prenom,
        'nom' => $nom,
        'email' => $email,
        'telephone' => $telephone,
        'date_naissance' => $date_naissance,
        'adresse' => $adresse,
        'photo' => $photo_path,
        'referentiel_id' => $referentiel_id,
        'statut' => 'Actif',
        'promotion_id' => $current_promotion['id']
    ];
    
    // Création de l'apprenant
    $result = $model['create_apprenant']($apprenant_data);
    
    if (!$result) {
        $session_services['set_flash_message']('danger', 'Erreur lors de la création de l\'apprenant');
        redirect('?page=add-apprenant-form');
        return;
    }
    
    // Redirection avec message de succès
    $session_services['set_flash_message']('success', 'Apprenant créé avec succès');
    redirect('?page=apprenants');
}

// Affichage des détails d'un apprenant
function view_apprenant() {
    global $model, $session_services;
    
    // Vérifier l'authentification
    $user = check_auth();
    
    // Récupérer l'ID de l'apprenant
    $apprenant_id = $_GET['id'] ?? null;
    if (!$apprenant_id) {
        $session_services['set_flash_message']('danger', 'Apprenant non trouvé');
        redirect('?page=apprenants');
        return;
    }
    
    // Récupérer l'apprenant
    $apprenant = $model['get_apprenant_by_id']($apprenant_id);
    if (!$apprenant) {
        $session_services['set_flash_message']('danger', 'Apprenant non trouvé');
        redirect('?page=apprenants');
        return;
    }
    
    // Récupérer la promotion
    $promotion = $model['get_promotion_by_id']($apprenant['promotion_id']);
    
    // Récupérer le référentiel
    $referentiel = $model['get_referentiel_by_id']($apprenant['referentiel_id']);
    
    // Affichage de la vue
    render('admin.layout.php', 'apprenant/view.html.php', [
        'user' => $user,
        'active_menu' => 'apprenants',
        'apprenant' => $apprenant,
        'promotion' => $promotion,
        'referentiel' => $referentiel
    ]);
}

// Affichage du formulaire de modification d'un apprenant
function edit_apprenant_form() {
    global $model, $session_services;
    
    // Vérifier l'authentification
    $user = check_auth();
    
    // Récupérer l'ID de l'apprenant
    $apprenant_id = $_GET['id'] ?? null;
    if (!$apprenant_id) {
        $session_services['set_flash_message']('danger', 'Apprenant non trouvé');
        redirect('?page=apprenants');
        return;
    }
    
    // Récupérer l'apprenant
    $apprenant = $model['get_apprenant_by_id']($apprenant_id);
    if (!$apprenant) {
        $session_services['set_flash_message']('danger', 'Apprenant non trouvé');
        redirect('?page=apprenants');
        return;
    }
    
    // Récupérer tous les référentiels
    $referentiels = $model['get_all_referentiels']();
    
    // Affichage de la vue
    render('admin.layout.php', 'apprenant/edit.html.php', [
        'user' => $user,
        'active_menu' => 'apprenants',
        'apprenant' => $apprenant,
        'referentiels' => $referentiels
    ]);
}

// Traitement de l'édition d'un apprenant
function edit_apprenant() {
    global $model, $validator_services, $session_services;
    
    // Vérifier l'authentification
    $user = check_auth();
    
    // Si la méthode n'est pas POST, rediriger vers la liste
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirect('?page=apprenants');
        return;
    }
    
    // Récupérer l'ID de l'apprenant
    $apprenant_id = $_POST['id'] ?? null;
    if (!$apprenant_id) {
        $session_services['set_flash_message']('danger', 'Apprenant non trouvé');
        redirect('?page=apprenants');
        return;
    }
    
    // Récupérer l'apprenant existant
    $apprenant = $model['get_apprenant_by_id']($apprenant_id);
    if (!$apprenant) {
        $session_services['set_flash_message']('danger', 'Apprenant non trouvé');
        redirect('?page=apprenants');
        return;
    }
    
    // Récupération des données du formulaire
    $prenom = $_POST['prenom'] ?? '';
    $nom = $_POST['nom'] ?? '';
    $email = $_POST['email'] ?? '';
    $telephone = $_POST['telephone'] ?? '';
    $date_naissance = $_POST['date_naissance'] ?? '';
    $adresse = $_POST['adresse'] ?? '';
    $referentiel_id = $_POST['referentiel_id'] ?? '';
    $statut = $_POST['statut'] ?? 'Actif';
    
    // Validation des données
    $errors = [];
    
    if (empty(trim($prenom))) {
        $errors['prenom'] = 'Le prénom est obligatoire';
    }
    
    if (empty(trim($nom))) {
        $errors['nom'] = 'Le nom est obligatoire';
    }
    
    if (empty(trim($email))) {
        $errors['email'] = 'L\'email est obligatoire';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'L\'email n\'est pas valide';
    } elseif ($email !== $apprenant['email'] && $model['email_exists']($email)) {
        $errors['email'] = 'Cet email est déjà utilisé';
    }
    
    if (empty(trim($telephone))) {
        $errors['telephone'] = 'Le téléphone est obligatoire';
    }
    
    if (empty(trim($date_naissance))) {
        $errors['date_naissance'] = 'La date de naissance est obligatoire';
    }
    
    if (empty(trim($referentiel_id))) {
        $errors['referentiel_id'] = 'Le référentiel est obligatoire';
    }
    
    // Traitement de la photo
    $photo_path = $apprenant['photo'];
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png'];
        $max_size = 2 * 1024 * 1024; // 2MB
        
        if (!in_array($_FILES['photo']['type'], $allowed_types)) {
            $errors['photo'] = 'Le format de la photo doit être JPG ou PNG';
        } elseif ($_FILES['photo']['size'] > $max_size) {
            $errors['photo'] = 'La photo ne doit pas dépasser 2MB';
        } else {
            // Traiter la photo
            $upload_dir = __DIR__ . '/../../public/assets/images/uploads/apprenants/';
            
            // Créer le répertoire s'il n'existe pas
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            // Générer un nom de fichier unique
            $filename = uniqid() . '_' . basename($_FILES['photo']['name']);
            $upload_path = $upload_dir . $filename;
            
            // Déplacer le fichier téléchargé
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $upload_path)) {
                // Supprimer l'ancienne photo si elle existe
                if ($photo_path && file_exists(__DIR__ . '/../../public/' . $photo_path)) {
                    unlink(__DIR__ . '/../../public/' . $photo_path);
                }
                $photo_path = 'assets/images/uploads/apprenants/' . $filename;
            } else {
                $errors['photo'] = 'Erreur lors du téléchargement de la photo';
            }
        }
    }
    
    // S'il y a des erreurs, réafficher le formulaire avec les erreurs
    if (!empty($errors)) {
        $referentiels = $model['get_all_referentiels']();
        render('admin.layout.php', 'apprenant/edit.html.php', [
            'user' => $user,
            'active_menu' => 'apprenants',
            'apprenant' => $apprenant,
            'referentiels' => $referentiels,
            'errors' => $errors,
            'prenom' => $prenom,
            'nom' => $nom,
            'email' => $email,
            'telephone' => $telephone,
            'date_naissance' => $date_naissance,
            'adresse' => $adresse,
            'referentiel_id' => $referentiel_id,
            'statut' => $statut
        ]);
        return;
    }
    
    // Préparation des données de l'apprenant
    $apprenant_data = [
        'id' => $apprenant_id,
        'matricule' => $apprenant['matricule'],
        'prenom' => $prenom,
        'nom' => $nom,
        'email' => $email,
        'telephone' => $telephone,
        'date_naissance' => $date_naissance,
        'adresse' => $adresse,
        'photo' => $photo_path,
        'referentiel_id' => $referentiel_id,
        'statut' => $statut,
        'promotion_id' => $apprenant['promotion_id']
    ];
    
    // Mise à jour de l'apprenant
    $result = $model['update_apprenant']($apprenant_data);
    
    if (!$result) {
        $session_services['set_flash_message']('danger', 'Erreur lors de la mise à jour de l\'apprenant');
        redirect('?page=edit-apprenant&id=' . $apprenant_id);
        return;
    }
    
    // Redirection avec message de succès
    $session_services['set_flash_message']('success', 'Apprenant mis à jour avec succès');
    redirect('?page=apprenants');
}

// Suppression d'un apprenant
function delete_apprenant() {
    global $model, $session_services;
    
    // Vérifier l'authentification
    $user = $session_services['get_current_user']();
    if (!$user) {
        redirect('?page=login');
        return;
    }
    
    // Récupérer l'ID de l'apprenant
    $apprenant_id = $_GET['id'] ?? null;
    if (!$apprenant_id) {
        $session_services['set_flash_message']('danger', 'Apprenant non trouvé');
        redirect('?page=apprenants');
        return;
    }
    
    // Récupérer l'apprenant
    $apprenant = $model['get_apprenant_by_id']($apprenant_id);
    if (!$apprenant) {
        $session_services['set_flash_message']('danger', 'Apprenant non trouvé');
        redirect('?page=apprenants');
        return;
    }
    
    // Supprimer la photo si elle existe
    if ($apprenant['photo'] && file_exists(__DIR__ . '/../../public/' . $apprenant['photo'])) {
        unlink(__DIR__ . '/../../public/' . $apprenant['photo']);
    }
    
    // Suppression de l'apprenant
    $result = $model['delete_apprenant']($apprenant_id);
    
    if (!$result) {
        $session_services['set_flash_message']('danger', 'Erreur lors de la suppression de l\'apprenant');
        redirect('?page=apprenants');
        return;
    }
    
    // Redirection avec message de succès
    $session_services['set_flash_message']('success', 'Apprenant supprimé avec succès');
    redirect('?page=apprenants');
}

// Export de la liste des apprenants
function export_apprenants() {
    global $model, $session_services;
    
    // Vérifier l'authentification
    $user = $session_services['get_current_user']();
    if (!$user) {
        redirect('?page=login');
        return;
    }
    
    // Récupérer la promotion courante
    $current_promotion = $model['get_current_promotion']();
    
    // Si aucune promotion n'est active
    if (!$current_promotion) {
        $session_services['set_flash_message']('info', 'Aucune promotion active');
        redirect('?page=promotions');
        return;
    }
    
    // Récupérer les apprenants de la promotion courante
    $apprenants = $model['get_apprenants_by_promotion']($current_promotion['id']);
    
    // Récupérer tous les référentiels
    $referentiels = $model['get_all_referentiels']();
    
    // Créer un tableau associatif des référentiels pour faciliter l'accès
    $referentiels_map = [];
    foreach ($referentiels as $ref) {
        $referentiels_map[$ref['id']] = $ref['name'];
    }
    
    // Définir les en-têtes HTTP pour le téléchargement
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="apprenants_' . date('Y-m-d') . '.csv"');
    
    // Créer un fichier CSV
    $output = fopen('php://output', 'w');
    
    // Ajouter l'en-tête UTF-8 BOM pour Excel
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    // Ajouter les en-têtes du CSV
    fputcsv($output, ['Matricule', 'Prénom', 'Nom', 'Email', 'Téléphone', 'Date de naissance', 'Adresse', 'Référentiel', 'Statut']);
    
    // Ajouter les données des apprenants
    foreach ($apprenants as $apprenant) {
        $referentiel_name = isset($referentiels_map[$apprenant['referentiel_id']]) ? $referentiels_map[$apprenant['referentiel_id']] : 'Non assigné';
        
        fputcsv($output, [
            $apprenant['matricule'],
            $apprenant['prenom'],
            $apprenant['nom'],
            $apprenant['email'],
            $apprenant['telephone'],
            $apprenant['date_naissance'],
            $apprenant['adresse'],
            $referentiel_name,
            $apprenant['statut']
        ]);
    }
    
    fclose($output);
    exit;
}

// Affichage du formulaire d'importation
function import_apprenants_form() {
    global $model, $session_services;
    
    // Vérifier si l'utilisateur est connecté et a les droits d'administrateur
    $user = check_profile(\App\Enums\ADMIN);
    
    // Afficher le formulaire
    render('admin.layout.php', 'apprenant/import.html.php', [
        'user' => $user,
        'active_menu' => 'apprenants'
    ]);
}

// Téléchargement du modèle CSV
function download_template() {
    // Définir les en-têtes pour le téléchargement
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="modele_import_apprenants.csv"');
    
    // Créer un fichier de sortie
    $output = fopen('php://output', 'w');
    
    // Ajouter l'en-tête UTF-8 BOM pour Excel
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    // Ajouter les en-têtes
    fputcsv($output, ['Prénom', 'Nom', 'Email', 'Téléphone', 'Date de naissance (JJ/MM/AAAA)', 'Adresse', 'Référentiel']);
    
    // Ajouter quelques exemples
    fputcsv($output, ['John', 'Doe', 'john.doe@example.com', '770000000', '15/05/1995', 'Dakar, Sénégal', 'Développeur Web']);
    fputcsv($output, ['Jane', 'Smith', 'jane.smith@example.com', '771111111', '20/10/1998', 'Thiès, Sénégal', 'Référent Digital']);
    
    fclose($output);
    exit;
}

// Traitement de l'importation
function import_apprenants_process() {
    global $model, $validator_services, $session_services;
    
    // Vérifier si l'utilisateur est connecté et a les droits d'administrateur
    $user = check_profile(\App\Enums\ADMIN);
    
    // Récupérer tous les référentiels
    $referentiels = $model['get_all_referentiels']();
    
    // Initialiser les résultats d'importation
    $import_results = [
        'success' => [],
        'errors' => [],
        'waiting_list' => []
    ];
    
    // Vérifier si un fichier a été téléchargé
    if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
        $session_services['set_flash_message']('danger', 'Aucun fichier téléchargé');
        render('admin.layout.php', 'apprenant/import.html.php', [
            'user' => $user,
            'active_menu' => 'apprenants',
            'error_message' => 'Veuillez sélectionner un fichier CSV valide.'
        ]);
        return;
    }
    
    // Ouvrir le fichier CSV
    $file = fopen($_FILES['csv_file']['tmp_name'], 'r');
    if (!$file) {
        $session_services['set_flash_message']('danger', 'Impossible d\'ouvrir le fichier');
        redirect('?page=apprenants');
        return;
    }
    
    // Options d'importation
    $skip_header = isset($_POST['skip_header']);
    $add_to_waiting_list = isset($_POST['add_to_waiting_list']);
    
    // Récupérer la promotion active
    $current_promotion = $model['get_current_promotion']();
    if (!$current_promotion) {
        render('admin.layout.php', 'apprenant/import.html.php', [
            'user' => $user,
            'active_menu' => 'apprenants',
            'error_message' => 'Aucune promotion active trouvée. Veuillez activer une promotion avant d\'importer des apprenants.'
        ]);
        return;
    }
    
    // Lire le fichier ligne par ligne
    $line_number = 0;
    while (($data = fgetcsv($file)) !== FALSE) {
        $line_number++;
        
        // Ignorer la première ligne si demandé
        if ($line_number === 1 && $skip_header) {
            continue;
        }
        
        // Vérifier si la ligne a suffisamment de colonnes
        if (count($data) < 4) {
            $import_results['errors'][] = [
                'line' => $line_number,
                'data' => implode(', ', $data),
                'message' => 'Nombre de colonnes insuffisant'
            ];
            continue;
        }
        
        // Extraire les données
        $prenom = trim($data[0]);
        $nom = trim($data[1]);
        $email = trim($data[2]);
        $telephone = trim($data[3]);
        $date_naissance = isset($data[4]) ? trim($data[4]) : '';
        $adresse = isset($data[5]) ? trim($data[5]) : '';
        $referentiel_name = isset($data[6]) ? trim($data[6]) : '';
        
        // Valider les données obligatoires
        $errors = [];
        
        if (empty($prenom)) {
            $errors[] = 'Prénom manquant';
        }
        
        if (empty($nom)) {
            $errors[] = 'Nom manquant';
        }
        
        if (empty($email)) {
            $errors[] = 'Email manquant';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Format d\'email invalide';
        }
        
        if (empty($telephone)) {
            $errors[] = 'Téléphone manquant';
        } elseif (!preg_match('/^[0-9]{9,10}$/', str_replace(' ', '', $telephone))) {
            $errors[] = 'Format de téléphone invalide';
        }
        
        if (empty($referentiel_name)) {
            $errors[] = 'Référentiel manquant';
        }
        
        // Trouver l'ID du référentiel
        $referentiel_id = null;
        foreach ($referentiels as $ref) {
            if (strtolower($ref['name']) === strtolower($referentiel_name)) {
                $referentiel_id = $ref['id'];
                break;
            }
        }
        
        if (!$referentiel_id) {
            $errors[] = 'Référentiel inconnu';
        }
        
        // Convertir la date de naissance si fournie
        $date_naissance_formatted = '';
        if (!empty($date_naissance)) {
            if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $date_naissance, $matches)) {
                $day = $matches[1];
                $month = $matches[2];
                $year = $matches[3];
                $date_naissance_formatted = "$year-$month-$day";
            } else {
                $errors[] = 'Format de date de naissance invalide (JJ/MM/AAAA attendu)';
            }
        }
        
        // Vérifier si l'email existe déjà
        if ($model['email_exists']($email)) {
            $errors[] = 'Un apprenant avec cet email existe déjà';
        }
        
        // Si des erreurs sont trouvées
        if (!empty($errors)) {
            if ($add_to_waiting_list) {
                // Ajouter à la liste d'attente
                $waiting_apprenant = [
                    'id' => uniqid(),
                    'prenom' => $prenom,
                    'nom' => $nom,
                    'email' => $email,
                    'telephone' => $telephone,
                    'date_naissance' => $date_naissance_formatted,
                    'adresse' => $adresse,
                    'referentiel_id' => $referentiel_id,
                    'referentiel' => $referentiel_name,
                    'raison' => implode(', ', $errors),
                    'promotion_id' => $current_promotion['id'],
                    'date_ajout' => date('Y-m-d H:i:s'),
                    'liste_attente' => true
                ];
                
                $model['add_to_waiting_list']($waiting_apprenant);
                $import_results['waiting_list'][] = $waiting_apprenant;
            } else {
                // Ajouter aux erreurs
                $import_results['errors'][] = [
                    'line' => $line_number,
                    'data' => implode(', ', $data),
                    'message' => implode(', ', $errors)
                ];
            }
            continue;
        }
        
        // Générer un matricule unique
        $matricule = $model['generate_matricule']();
        
        // Préparer les données de l'apprenant
        $apprenant_data = [
            'id' => uniqid(),
            'matricule' => $matricule,
            'prenom' => $prenom,
            'nom' => $nom,
            'email' => $email,
            'telephone' => $telephone,
            'date_naissance' => $date_naissance_formatted,
            'adresse' => $adresse,
            'photo' => 'assets/images/default-avatar.png',
            'referentiel_id' => $referentiel_id,
            'statut' => 'Actif',
            'promotion_id' => $current_promotion['id']
        ];
        
        // Ajouter l'apprenant
        $result = $model['create_apprenant']($apprenant_data);
        
        if ($result) {
            // Ajouter aux succès
            $apprenant_data['referentiel'] = $referentiel_name;
            $import_results['success'][] = $apprenant_data;
        } else {
            // Ajouter aux erreurs
            $import_results['errors'][] = [
                'line' => $line_number,
                'data' => implode(', ', $data),
                'message' => 'Erreur lors de l\'ajout de l\'apprenant dans le fichier JSON'
            ];
        }
    }
    
    fclose($file);
    
    // Afficher les résultats
    render('admin.layout.php', 'apprenant/import.html.php', [
        'user' => $user,
        'active_menu' => 'apprenants',
        'success_message' => count($import_results['success']) . ' apprenants importés avec succès',
        'import_results' => $import_results
    ]);
}

// Fonction pour générer un matricule unique
function generate_matricule($referentiel, $annee) {
    global $model;
    
    // Extraire les premières lettres du référentiel
    $prefix = '';
    $words = explode(' ', $referentiel);
    foreach ($words as $word) {
        if (!empty($word)) {
            $prefix .= strtoupper(substr($word, 0, 1));
        }
    }
    
    // Limiter à 3 caractères maximum
    $prefix = substr($prefix, 0, 3);
    
    // Ajouter l'année
    $year_suffix = substr($annee, 2, 2);
    
    // Récupérer le dernier numéro utilisé pour ce préfixe et cette année
    $last_number = $model['get_last_matricule_number']($prefix, $year_suffix);
    $next_number = $last_number + 1;
    
    // Formater le numéro avec des zéros devant
    $number_formatted = str_pad($next_number, 3, '0', STR_PAD_LEFT);
    
    // Construire le matricule final
    $matricule = $prefix . $year_suffix . $number_formatted;
    
    return $matricule;
}

// Approuver un apprenant de la liste d'attente
function approve_waiting_apprenant() {
    global $model, $session_services;
    
    // Vérifier si l'utilisateur est connecté et a les droits d'administrateur
    $user = check_profile(\App\Enums\ADMIN);
    
    // Récupérer l'ID de l'apprenant
    $apprenant_id = $_GET['id'] ?? null;
    if (!$apprenant_id) {
        $session_services['set_flash_message']('danger', 'ID d\'apprenant manquant');
        redirect('?page=apprenants&waiting_list=true');
        return;
    }
    
    // Récupérer l'apprenant de la liste d'attente
    $waiting_list = $model['get_waiting_list']();
    $apprenant = null;
    
    foreach ($waiting_list as $item) {
        if ($item['id'] === $apprenant_id) {
            $apprenant = $item;
            break;
        }
    }
    
    if (!$apprenant) {
        $session_services['set_flash_message']('danger', 'Apprenant non trouvé dans la liste d\'attente');
        redirect('?page=apprenants&waiting_list=true');
        return;
    }
    
    // Générer un matricule unique
    $matricule = $model['generate_matricule']();
    
    // Préparer les données de l'apprenant
    $apprenant_data = [
        'id' => uniqid(),
        'matricule' => $matricule,
        'prenom' => $apprenant['prenom'],
        'nom' => $apprenant['nom'],
        'email' => $apprenant['email'],
        'telephone' => $apprenant['telephone'],
        'date_naissance' => $apprenant['date_naissance'],
        'adresse' => $apprenant['adresse'],
        'photo' => 'assets/images/default-avatar.png',
        'referentiel_id' => $apprenant['referentiel_id'],
        'statut' => 'Actif',
        'promotion_id' => $apprenant['promotion_id']
    ];
    
    // Ajouter l'apprenant
    $result = $model['create_apprenant']($apprenant_data);
    
    if ($result) {
        // Supprimer l'apprenant de la liste d'attente
        $model['remove_from_waiting_list']($apprenant_id);
        
        $session_services['set_flash_message']('success', 'Apprenant approuvé et ajouté avec succès');
    } else {
        $session_services['set_flash_message']('danger', 'Erreur lors de l\'ajout de l\'apprenant');
    }
    
    redirect('?page=apprenants&waiting_list=true');
}

// Afficher le formulaire de modification d'un apprenant de la liste d'attente
function edit_waiting_apprenant_form() {
    global $model, $session_services;
    
    // Vérifier si l'utilisateur est connecté et a les droits d'administrateur
    $user = check_profile(\App\Enums\ADMIN);
    
    // Récupérer l'ID de l'apprenant
    $apprenant_id = $_GET['id'] ?? null;
    if (!$apprenant_id) {
        $session_services['set_flash_message']('danger', 'ID d\'apprenant manquant');
        redirect('?page=apprenants&waiting_list=true');
        return;
    }
    
    // Récupérer l'apprenant de la liste d'attente
    $waiting_list = $model['get_waiting_list']();
    $apprenant = null;
    
    foreach ($waiting_list as $item) {
        if ($item['id'] === $apprenant_id) {
            $apprenant = $item;
            break;
        }
    }
    
    if (!$apprenant) {
        $session_services['set_flash_message']('danger', 'Apprenant non trouvé dans la liste d\'attente');
        redirect('?page=apprenants&waiting_list=true');
        return;
    }
    
    // Récupérer tous les référentiels
    $referentiels = $model['get_all_referentiels']();
    
    // Afficher le formulaire
    render('admin.layout.php', 'apprenant/edit_waiting.html.php', [
        'user' => $user,
        'active_menu' => 'apprenants',
        'apprenant' => $apprenant,
        'referentiels' => $referentiels
    ]);
}

// Traiter la modification d'un apprenant de la liste d'attente
function edit_waiting_apprenant_process() {
    global $model, $validator_services, $session_services;
    
    // Vérifier si l'utilisateur est connecté et a les droits d'administrateur
    $user = check_profile(\App\Enums\ADMIN);
    
    // Récupérer les données du formulaire
    $id = $_POST['id'] ?? null;
    $prenom = $_POST['prenom'] ?? '';
    $nom = $_POST['nom'] ?? '';
    $email = $_POST['email'] ?? '';
    $telephone = $_POST['telephone'] ?? '';
    $date_naissance = $_POST['date_naissance'] ?? '';
    $adresse = $_POST['adresse'] ?? '';
    $referentiel_id = $_POST['referentiel_id'] ?? '';
    
    // Validation des données
    $errors = [];
    
    if (empty(trim($prenom))) {
        $errors['prenom'] = 'Le prénom est obligatoire';
    }
    
    if (empty(trim($nom))) {
        $errors['nom'] = 'Le nom est obligatoire';
    }
    
    if (empty(trim($email))) {
        $errors['email'] = 'L\'email est obligatoire';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Format d\'email invalide';
    }
    
    if (empty(trim($telephone))) {
        $errors['telephone'] = 'Le téléphone est obligatoire';
    }
    
    if (empty(trim($referentiel_id))) {
        $errors['referentiel_id'] = 'Le référentiel est obligatoire';
    }
    
    // Récupérer l'apprenant original de la liste d'attente
    $waiting_list = $model['get_waiting_list']();
    $original_apprenant = null;
    
    foreach ($waiting_list as $item) {
        if ($item['id'] === $id) {
            $original_apprenant = $item;
            break;
        }
    }
    
    if (!$original_apprenant) {
        $session_services['set_flash_message']('danger', 'Apprenant non trouvé dans la liste d\'attente');
        redirect('?page=apprenants&waiting_list=true');
        return;
    }
    
    // Vérifier si l'email a changé et s'il existe déjà (sauf pour cet apprenant)
    if ($email !== $original_apprenant['email'] && $model['email_exists']($email)) {
        $errors['email'] = 'Cet email est déjà utilisé par un autre apprenant';
    }
    
    // S'il y a des erreurs, réafficher le formulaire avec les erreurs
    if (!empty($errors)) {
        $referentiels = $model['get_all_referentiels']();
        render('admin.layout.php', 'apprenant/edit_waiting.html.php', [
            'user' => $user,
            'active_menu' => 'apprenants',
            'apprenant' => $original_apprenant,
            'errors' => $errors,
            'prenom' => $prenom,
            'nom' => $nom,
            'email' => $email,
            'telephone' => $telephone,
            'date_naissance' => $date_naissance,
            'adresse' => $adresse,
            'referentiel_id' => $referentiel_id,
            'referentiels' => $referentiels
        ]);
        return;
    }
    
    // Trouver le référentiel pour obtenir son nom
    $referentiels = $model['get_all_referentiels']();
    $referentiel_name = '';
    
    foreach ($referentiels as $ref) {
        if ($ref['id'] === $referentiel_id) {
            $referentiel_name = $ref['name'];
            break;
        }
    }
    
    // Mettre à jour les données de l'apprenant
    $updated_apprenant = [
        'id' => $id,
        'prenom' => $prenom,
        'nom' => $nom,
        'email' => $email,
        'telephone' => $telephone,
        'date_naissance' => $date_naissance,
        'adresse' => $adresse,
        'referentiel_id' => $referentiel_id,
        'referentiel' => $referentiel_name,
        'raison' => $original_apprenant['raison'],
        'promotion_id' => $original_apprenant['promotion_id'],
        'date_ajout' => $original_apprenant['date_ajout'],
        'liste_attente' => true
    ];
    
    // Mettre à jour l'apprenant dans la liste d'attente
    $result = $model['update_waiting_apprenant']($updated_apprenant);
    
    if ($result) {
        $session_services['set_flash_message']('success', 'Apprenant mis à jour avec succès');
        redirect('?page=apprenants&waiting_list=true');
    } else {
        $session_services['set_flash_message']('danger', 'Erreur lors de la mise à jour de l\'apprenant');
        redirect('?page=edit-waiting-apprenant&id=' . $id);
    }
}

// Supprimer un apprenant de la liste d'attente
function delete_waiting_apprenant() {
    global $model, $session_services;
    
    // Vérifier si l'utilisateur est connecté et a les droits d'administrateur
    $user = check_profile(\App\Enums\ADMIN);
    
    // Récupérer l'ID de l'apprenant
    $apprenant_id = $_POST['apprenant_id'] ?? null;
    
    if (!$apprenant_id) {
        $session_services['set_flash_message']('danger', 'ID d\'apprenant manquant');
        redirect('?page=apprenants&waiting_list=true');
        return;
    }
    
    // Supprimer l'apprenant de la liste d'attente
    $result = $model['remove_from_waiting_list']($apprenant_id);
    
    if ($result) {
        $session_services['set_flash_message']('success', 'Apprenant supprimé de la liste d\'attente avec succès');
    } else {
        $session_services['set_flash_message']('danger', 'Erreur lors de la suppression de l\'apprenant');
    }
    
    redirect('?page=apprenants&waiting_list=true');
}