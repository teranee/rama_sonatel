<?php

namespace App\Controllers;

require_once __DIR__ . '/controller.php';
require_once __DIR__ . '/../models/model.php';
require_once __DIR__ . '/../services/session.service.php';
require_once __DIR__ . '/../enums/profile.enum.php';

use App\Models;
use App\Services;
use App\Enums;

// Fonction pour exporter la liste des apprenants en PDF
function export_apprenants_pdf() {
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
    $statut_filter = $_GET['status'] ?? 'all';
    
    if (!empty($search) || $referentiel_filter !== 'all' || $statut_filter !== 'all') {
        $apprenants = array_filter($apprenants, function($apprenant) use ($search, $referentiel_filter, $statut_filter) {
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
                $referentiels = $model['get_all_referentiels']();
                $ref_name = '';
                foreach ($referentiels as $ref) {
                    if ($ref['id'] === $referentiel_filter) {
                        $ref_name = strtoupper($ref['name']);
                        break;
                    }
                }
                
                if (!isset($apprenant['referentiel']) || 
                    strtoupper($apprenant['referentiel']) !== $ref_name) {
                    return false;
                }
            }
            
            // Filtre par statut
            if ($statut_filter !== 'all' && $apprenant['statut'] !== $statut_filter) {
                return false;
            }
            
            return true;
        });
    }
    
    // Générer le PDF avec FPDF
    require_once __DIR__ . '/../libs/fpdf/fpdf.php';
    
    class PDF extends \FPDF {
        function Header() {
            // Logo
            $this->Image(__DIR__ . '/../../public/assets/images/logo.png', 10, 6, 30);
            // Police Arial gras 15
            $this->SetFont('Arial', 'B', 15);
            // Décalage à droite
            $this->Cell(80);
            // Titre
            $this->Cell(30, 10, 'Liste des Apprenants', 0, 0, 'C');
            // Saut de ligne
            $this->Ln(20);
            
            // En-têtes de colonnes
            $this->SetFont('Arial', 'B', 10);
            $this->Cell(30, 7, 'Matricule', 1, 0, 'C');
            $this->Cell(40, 7, 'Nom Complet', 1, 0, 'C');
            $this->Cell(40, 7, 'Email', 1, 0, 'C');
            $this->Cell(30, 7, 'Téléphone', 1, 0, 'C');
            $this->Cell(30, 7, 'Référentiel', 1, 0, 'C');
            $this->Cell(20, 7, 'Statut', 1, 0, 'C');
            $this->Ln();
        }
        
        function Footer() {
            // Positionnement à 1,5 cm du bas
            $this->SetY(-15);
            // Police Arial italique 8
            $this->SetFont('Arial', 'I', 8);
            // Numéro de page
            $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
        }
    }
    
    // Instanciation de la classe dérivée
    $pdf = new PDF();
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->SetFont('Arial', '', 10);
    
    // Ajout des données
    foreach ($apprenants as $apprenant) {
        $pdf->Cell(30, 6, $apprenant['matricule'], 1, 0, 'L');
        $pdf->Cell(40, 6, $apprenant['prenom'] . ' ' . $apprenant['nom'], 1, 0, 'L');
        $pdf->Cell(40, 6, $apprenant['email'], 1, 0, 'L');
        $pdf->Cell(30, 6, $apprenant['telephone'], 1, 0, 'L');
        $pdf->Cell(30, 6, $apprenant['referentiel'], 1, 0, 'L');
        $pdf->Cell(20, 6, $apprenant['statut'], 1, 0, 'L');
        $pdf->Ln();
    }
    
    // Sortie du PDF
    $pdf->Output('D', 'Liste_Apprenants_' . date('Y-m-d') . '.pdf');
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
    $statut_filter = $_GET['status'] ?? 'all';
    
    if (!empty($search) || $referentiel_filter !== 'all' || $statut_filter !== 'all') {
        $apprenants = array_filter($apprenants, function($apprenant) use ($search, $referentiel_filter, $statut_filter) {
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
                $referentiels = $model['get_all_referentiels']();
                $ref_name = '';
                foreach ($referentiels as $ref) {
                    if ($ref['id'] === $referentiel_filter) {
                        $ref_name = strtoupper($ref['name']);
                        break;
                    }
                }
                
                if (!isset($apprenant['referentiel']) || 
                    strtoupper($apprenant['referentiel']) !== $ref_name) {
                    return false;
                }
            }
            
            // Filtre par statut
            if ($statut_filter !== 'all' && $apprenant['statut'] !== $statut_filter) {
                return false;
            }
            
            return true;
        });
    }
    
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
    foreach ($apprenants as $apprenant) {
        fputcsv($output, [
            $apprenant['matricule'],
            $apprenant['prenom'],
            $apprenant['nom'],
            $apprenant['email'],
            $apprenant['telephone'],
            $apprenant['date_naissance'],
            $apprenant['adresse'],
            $apprenant['referentiel'],
            $apprenant['statut']
        ]);
    }
    
    fclose($output);
    exit;
}