<?php

namespace App\Controllers;

require_once __DIR__ . '/../enums/path.enum.php';
require_once __DIR__ . '/../services/session.service.php';
require_once __DIR__ . '/../models/model.php'; // Ajout du model

use App\Enums;
use App\Services;

// Fonctions communes à tous les contrôleurs
function render($layout, $template, $data = []) {
    global $model;
    
    // Vérifier si $model est défini
    if (isset($model) && is_array($model) && isset($model['get_current_promotion'])) {
        $current_promotion = $model['get_current_promotion']();
        $data['current_promotion'] = $current_promotion;
    }
    
    // Extraction des données pour les rendre disponibles dans la vue
    extract($data);
    
    // Utiliser un chemin absolu vers les vues
    $base_path = dirname(__DIR__); // Remonte d'un niveau depuis le dossier controllers
    
    // Capturer le contenu du template
    ob_start();
    require_once $base_path . '/views/' . $template;
    $content = ob_get_clean();
    
    // Rendre le layout avec le contenu capturé
    require_once $base_path . '/views/layout/' . $layout;
}
// Redirection vers une autre page
function redirect($url) {
    if (!headers_sent()) {
        header("Location: $url");
        exit();
    } else {
        echo '<script>window.location.href="'.$url.'";</script>';
        echo '<noscript><meta http-equiv="refresh" content="0;url='.$url.'" /></noscript>';
        exit();
    }
}

// Vérification si l'utilisateur est connecté
function check_auth() {
    global $session_services;
    
    $session_services['start_session']();
    
    if (!$session_services['is_logged_in']()) {
        redirect('?page=login');
    }
    
    return $session_services['get_current_user']();
}

// Vérification si l'utilisateur a le profil requis
function check_profile($required_profiles) {
    $user = check_auth();
    
    // Si un seul profil est passé (string), le convertir en tableau
    if (!is_array($required_profiles)) {
        $required_profiles = [$required_profiles];
    }
    
    // Vérifier si le profil de l'utilisateur est dans la liste des profils autorisés
    if (!in_array($user['profile'], $required_profiles)) {
        redirect('?page=forbidden');
    }
    
    return $user;
}

// Téléchargement d'un fichier image
function upload_image($file, $directory = 'uploads') {
    // Vérifier si le répertoire existe, sinon le créer
    $upload_dir = Enums\UPLOAD_PATH . $directory . '/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    // Générer un nom de fichier unique
    $filename = uniqid() . '_' . basename($file['name']);
    $target_path = $upload_dir . $filename;
    
    // Déplacer le fichier téléchargé vers le répertoire cible
    if (move_uploaded_file($file['tmp_name'], $target_path)) {
        return $directory . '/' . $filename;
    }
    
    return false;
}