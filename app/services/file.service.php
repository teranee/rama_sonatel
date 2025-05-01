<?php

namespace App\Services;

require_once __DIR__ . '/../enums/path.enum.php';

use App\Enums;

$file_services = [
    'handle_promotion_image' => function ($file) {
        // Définir le chemin absolu du répertoire de destination
        $upload_dir = dirname(__DIR__, 2) . '/public/assets/images/uploads/promotions';
        
        // Vérifier si le répertoire existe, sinon le créer
        if (!is_dir($upload_dir)) {
            if (!mkdir($upload_dir, 0775, true)) {
                error_log("Impossible de créer le répertoire : $upload_dir");
                return false;
            }
        }
        
        // Vérifier les permissions
        if (!is_writable($upload_dir)) {
            error_log("Le répertoire n'est pas accessible en écriture : $upload_dir");
            chmod($upload_dir, 0775); // Tentative de correction des permissions
            
            if (!is_writable($upload_dir)) {
                return false; // Si toujours pas accessible en écriture
            }
        }
        
        // Générer un nom de fichier unique
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $extension;
        $target_path = $upload_dir . '/' . $filename;
        
        // Déplacer le fichier téléchargé
        if (move_uploaded_file($file['tmp_name'], $target_path)) {
            return $filename; // Retourner seulement le nom du fichier, pas le chemin complet
        } else {
            $error = error_get_last();
            error_log("Erreur lors du déplacement du fichier : " . ($error ? $error['message'] : 'Raison inconnue'));
            return false;
        }
    },
    
    'get_image_url' => function ($filename, $type = 'promotions') {
        // Construire l'URL relative pour l'affichage
        return "/assets/images/uploads/{$type}/{$filename}";
    },
    
    'handle_referentiel_image' => function($file) {
        $upload_dir = __DIR__ . '/../../public/assets/images/referentiels/';
        
        // Créer le répertoire s'il n'existe pas
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        // Générer un nom de fichier unique
        $filename = uniqid() . '_' . basename($file['name']);
        $upload_path = $upload_dir . $filename;
        
        // Déplacer le fichier téléchargé
        if (move_uploaded_file($file['tmp_name'], $upload_path)) {
            return 'assets/images/referentiels/' . $filename;
        }
        
        return false;
    }
];