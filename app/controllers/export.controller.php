<?php

namespace App\Controllers;

require_once __DIR__ . '/controller.php';
require_once __DIR__ . '/../models/model.php';
require_once __DIR__ . '/../services/session.service.php';
require_once __DIR__ . '/../enums/profile.enum.php';

use App\Models;
use App\Services;
use App\Enums;

// Fonction pour exporter la liste des apprenants en HTML
function export_apprenants_pdf() {
    global $model, $session_services;
    
    // Vérifier les droits d'accès
    $user = check_profile(\App\Enums\ADMIN);
    
    // Récupérer la promotion active
    $current_promotion = $model['get_current_promotion']();
    
    // Récupérer les apprenants de la promotion active
    $apprenants = $model['get_apprenants_by_promotion']($current_promotion['id']);
    
    // Filtrer les apprenants (code de filtrage comme précédemment)
    $filtered_apprenants = $apprenants; // Simplifié pour cet exemple
    
    // Générer le HTML
    $html = '<!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Liste des Apprenants</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            h1 { text-align: center; color: #333; }
            .date { text-align: center; font-style: italic; margin-bottom: 20px; }
            table { width: 100%; border-collapse: collapse; }
            th { background-color: #f5a623; color: white; padding: 10px; text-align: left; }
            td { padding: 8px; border-bottom: 1px solid #ddd; }
            tr:nth-child(even) { background-color: #f9f9f9; }
            @media print {
                body { margin: 0; }
                h1 { color: #000; }
                th { background-color: #eee; color: #000; }
            }
        </style>
    </head>
    <body>
        <h1>Liste des Apprenants</h1>
        <div class="date">Généré le ' . date('d/m/Y') . '</div>
        
        <table>
            <thead>
                <tr>
                    <th>Matricule</th>
                    <th>Nom Complet</th>
                    <th>Email</th>
                    <th>Téléphone</th>
                    <th>Référentiel</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>';
    
    foreach ($filtered_apprenants as $apprenant) {
        $html .= '
                <tr>
                    <td>' . htmlspecialchars($apprenant['matricule']) . '</td>
                    <td>' . htmlspecialchars($apprenant['prenom'] . ' ' . $apprenant['nom']) . '</td>
                    <td>' . htmlspecialchars($apprenant['email']) . '</td>
                    <td>' . htmlspecialchars($apprenant['telephone']) . '</td>
                    <td>' . htmlspecialchars($apprenant['referentiel']) . '</td>
                    <td>' . htmlspecialchars($apprenant['statut']) . '</td>
                </tr>';
    }
    
    $html .= '
            </tbody>
        </table>
        
        <script>
            // Ouvrir automatiquement la boîte de dialogue d\'impression
            window.onload = function() {
                window.print();
            }
        </script>
    </body>
    </html>';
    
    // Forcer le téléchargement du fichier HTML
    header('Content-Type: text/html; charset=utf-8');
    header('Content-Disposition: attachment; filename="Liste_Apprenants_' . date('d/m/Y') . '.html"');
    echo $html;
    exit;
}

// Fonction pour exporter la liste des apprenants en Excel
function export_apprenants_excel() {
    global $model, $session_services;
    
    // Vérifier les droits d'accès (Admin uniquement)
    $user = check_profile(\App\Enums\ADMIN);
    
    // Récupérer la promotion active
    $current_promotion = $model['get_current_promotion']();
    
    // Récupérer les apprenants de la promotion active
    $apprenants = $model['get_apprenants_by_promotion']($current_promotion['id']);
    
    // Filtrer les apprenants selon les critères (si nécessaire)
    $search = $_GET['search'] ?? '';
    $referentiel_filter = $_GET['referentiel'] ?? 'all';
    $status_filter = $_GET['status'] ?? 'all';
    $waiting_list = isset($_GET['waiting_list']) && $_GET['waiting_list'] === 'true';
    
    // Filtrer les apprenants
    $filtered_apprenants = array_filter($apprenants, function($apprenant) use ($search, $referentiel_filter, $status_filter, $waiting_list) {
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
            // Si vous filtrez par ID de référentiel, vous devrez adapter cette logique
            if (!isset($apprenant['referentiel']) || 
                strtolower($apprenant['referentiel']) !== strtolower($referentiel_filter)) {
                return false;
            }
        }
        
        // Filtre par statut
        if ($status_filter !== 'all' && $apprenant['statut'] !== $status_filter) {
            return false;
        }
        
        // Filtre liste d'attente
        if ($waiting_list) {
            // Adaptez cette condition selon votre logique de liste d'attente
            return isset($apprenant['liste_attente']) && $apprenant['liste_attente'] === true;
        }
        
        return true;
    });
    
    // Générer le fichier Excel (CSV)
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=Liste_Apprenants_' . date('Y-m-d') . '.csv');
    
    // Créer un fichier de sortie
    $output = fopen('php://output', 'w');
    
    // Ajouter l'en-tête UTF-8 BOM pour Excel
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    // Ajouter les en-têtes
    fputcsv($output, ['Matricule', 'Prénom', 'Nom', 'Email', 'Téléphone', 'Date de naissance', 'Adresse', 'Référentiel', 'Statut']);
    
    // Ajouter les données
    foreach ($filtered_apprenants as $apprenant) {
        fputcsv($output, [
            $apprenant['matricule'],
            $apprenant['prenom'],
            $apprenant['nom'],
            $apprenant['email'],
            $apprenant['telephone'],
            $apprenant['date_naissance'] ?? '',
            $apprenant['adresse'] ?? '',
            $apprenant['referentiel'],
            $apprenant['statut']
        ]);
    }
    
    fclose($output);
    exit;
}