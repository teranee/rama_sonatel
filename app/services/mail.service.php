<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Chargement de PHPMailer
require_once __DIR__ . '/../../vendor/autoload.php';

$mail_services = [
    'send_reset_password_email' => function ($email, $token) {
        $mail = new PHPMailer(true);
        
        try {
            // Configuration du serveur
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com'; // Remplacez par votre serveur SMTP
            $mail->SMTPAuth   = true;
            $mail->Username   = 'votre-email@gmail.com'; // Remplacez par votre email
            $mail->Password   = 'votre-mot-de-passe'; // Remplacez par votre mot de passe
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
            
            // Destinataires
            $mail->setFrom('votre-email@gmail.com', 'Système de Gestion des Apprenants');
            $mail->addAddress($email);
            
            // Contenu
            $mail->isHTML(true);
            $mail->Subject = 'Réinitialisation de votre mot de passe';
            
            // URL de réinitialisation
            $reset_url = 'http://' . $_SERVER['HTTP_HOST'] . '/?page=reset-password&token=' . $token;
            
            $mail->Body = '
                <h1>Réinitialisation de votre mot de passe</h1>
                <p>Vous avez demandé à réinitialiser votre mot de passe.</p>
                <p>Cliquez sur le lien ci-dessous pour définir un nouveau mot de passe :</p>
                <p><a href="' . $reset_url . '">Réinitialiser mon mot de passe</a></p>
                <p>Ce lien est valable pendant 1 heure.</p>
                <p>Si vous n\'avez pas demandé cette réinitialisation, ignorez cet email.</p>
            ';
            
            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Erreur d'envoi d'email: " . $mail->ErrorInfo);
            return false;
        }
    }
];