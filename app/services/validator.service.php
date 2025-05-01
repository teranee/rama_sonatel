<?php

namespace App\Services;

// Regroupement des fonctions de validation
$validator_services = [
    'is_empty' => function ($value) {
        return empty(trim($value));
    },
    
    'min_length' => function ($value, $min) {
        return strlen(trim($value)) >= $min;
    },
    
    'max_length' => function ($value, $max) {
        return strlen(trim($value)) <= $max;
    },
    
    'is_email' => function ($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    },
    
    'is_valid_image' => function ($file) {
        // Vérifier si le fichier est une image valide (JPG ou PNG) et sa taille
        if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
            return false;
        }
        
        $allowed_types = ['image/jpeg', 'image/png'];
        $max_size = 2 * 1024 * 1024; // 2MB en octets
        
        // Vérifier si le fichier existe
        if (!file_exists($file['tmp_name'])) {
            return false;
        }
        
        // Obtenir les informations sur l'image
        $file_info = @getimagesize($file['tmp_name']);
        if (!$file_info) {
            return false;
        }
        
        $file_type = $file_info['mime'];
        
        return in_array($file_type, $allowed_types) && $file['size'] <= $max_size;
    },
    
    'validate_form' => function ($data, $rules) {
        $errors = [];
        
        $validate_rule = function($field, $rule, $rule_value, $data, &$errors) {
            $result = match($rule) {
                'required' => $rule_value && empty(trim($data[$field])) 
                    ? ["Le champ est obligatoire"] : [],
                'min_length' => !empty($data[$field]) && strlen(trim($data[$field])) < $rule_value 
                    ? ["Le champ doit contenir au moins $rule_value caractères"] : [],
                'max_length' => !empty($data[$field]) && strlen(trim($data[$field])) > $rule_value 
                    ? ["Le champ ne doit pas dépasser $rule_value caractères"] : [],
                'email' => $rule_value && !empty($data[$field]) && !filter_var($data[$field], FILTER_VALIDATE_EMAIL) 
                    ? ["Email invalide"] : [],
                default => []
            };
            
            if (!empty($result)) {
                if (!isset($errors[$field])) {
                    $errors[$field] = [];
                }
                $errors[$field] = array_merge($errors[$field], $result);
            }
        };
        
        $process_field = function($field, $field_rules) use ($data, &$errors, $validate_rule) {
            $rule_keys = array_keys($field_rules);
            array_map(function($rule) use ($field, $field_rules, $data, &$errors, $validate_rule) {
                $validate_rule($field, $rule, $field_rules[$rule], $data, $errors);
            }, $rule_keys);
        };
        
        $fields = array_keys($rules);
        array_map(function($field) use ($rules, $process_field) {
            $process_field($field, $rules[$field]);
        }, $fields);
        
        return $errors;
    },
    
    'validate_promotion' => function(array $post_data, array $files): array {
        $errors = [];
        
        // Validation du nom (obligatoire et unique)
        if (empty($post_data['name'])) {
            $errors[] = 'Le nom de la promotion est requis';
        } else {
            global $model;
            if ($model['promotion_name_exists']($post_data['name'])) {
                $errors[] = 'Ce nom de promotion existe déjà';
            }
        }
        
        // Validation des dates (obligatoires)
        if (empty($post_data['date_debut'])) {
            $errors[] = 'La date de début est requise';
        }
        
        if (empty($post_data['date_fin'])) {
            $errors[] = 'La date de fin est requise';
        }
        
        if (!empty($post_data['date_debut']) && !empty($post_data['date_fin'])) {
            if (strtotime($post_data['date_fin']) <= strtotime($post_data['date_debut'])) {
                $errors[] = 'La date de fin doit être supérieure à la date de début';
            }
        }
        
       /*  // Validation de l'image
        if (empty($files['image']['name'])) {
            $errors[] = 'L\'image de la promotion est requise';
        } else {
            $allowed_types = ['image/jpeg', 'image/png'];
            if (!in_array($files['image']['type'], $allowed_types)) {
                $errors[] = 'Le format de l\'image doit être JPG ou PNG';
            }
            
            if ($files['image']['size'] > 2 * 1024 * 1024) { // 2MB
                $errors[] = 'La taille de l\'image ne doit pas dépasser 2MB';
            }
        } */
        
        // Validation des référentiels (au moins un requis)
        if (empty($post_data['referentiels'])) {
            $errors[] = 'Au moins un référentiel doit être sélectionné';
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    },
    
    'is_valid_date' => function($date_string) {
        // Vérifier le format JJ/MM/AAAA
        if (!preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $date_string, $matches)) {
            return false;
        }
        
        $day = (int)$matches[1];
        $month = (int)$matches[2];
        $year = (int)$matches[3];
        
        // Vérifier si la date est valide
        return checkdate($month, $day, $year);
    },

    'is_date_after' => function($date1, $date2) {
        // Convertir les dates au format JJ/MM/AAAA en timestamps
        if (!preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $date1, $matches1)) {
            return false;
        }
        
        if (!preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $date2, $matches2)) {
            return false;
        }
        
        $day1 = (int)$matches1[1];
        $month1 = (int)$matches1[2];
        $year1 = (int)$matches1[3];
        
        $day2 = (int)$matches2[1];
        $month2 = (int)$matches2[2];
        $year2 = (int)$matches2[3];
        
        $timestamp1 = mktime(0, 0, 0, $month1, $day1, $year1);
        $timestamp2 = mktime(0, 0, 0, $month2, $day2, $year2);
        
        // Vérifier si date1 est après date2
        return $timestamp1 > $timestamp2;
    },

    'convert_date_to_db_format' => function($date_string) {
        // Convertir JJ/MM/AAAA en AAAA-MM-JJ pour la base de données
        if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $date_string, $matches)) {
            $day = $matches[1];
            $month = $matches[2];
            $year = $matches[3];
            return "$year-$month-$day";
        }
        return $date_string;
    },

    'convert_date_from_db_format' => function($date_string) {
        // Convertir AAAA-MM-JJ en JJ/MM/AAAA pour l'affichage
        if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $date_string, $matches)) {
            $year = $matches[1];
            $month = $matches[2];
            $day = $matches[3];
            return "$day/$month/$year";
        }
        return $date_string;
    }
];