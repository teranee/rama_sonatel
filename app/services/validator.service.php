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
        
        $file_info = getimagesize($file['tmp_name']);
        $file_type = $file_info ? $file_info['mime'] : '';
        
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
        
        // Validation de l'image
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
        }
        
        // Validation des référentiels (au moins un requis)
        if (empty($post_data['referentiels'])) {
            $errors[] = 'Au moins un référentiel doit être sélectionné';
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
];