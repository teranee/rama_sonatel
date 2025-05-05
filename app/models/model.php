<?php

namespace App\Models;

require_once __DIR__ . '/../enums/path.enum.php';
require_once __DIR__ . '/../enums/status.enum.php';
require_once __DIR__ . '/../enums/profile.enum.php';

use App\Enums;
use App\Enums\Status; // Ajout de cette ligne

// Collection de toutes les fonctions modèles pour l'application
$model = [
    // Fonctions de base pour manipuler les données
    'read_data' => function() {
        $json_file = __DIR__ . '/../data/data.json';
        if (!file_exists($json_file)) {
            return [
                'apprenants' => [],
                'promotions' => [],
                'referentiels' => []
            ];
        }
        return json_decode(file_get_contents($json_file), true);
    },
    
    'write_data' => function ($data) {
        // Vérifier si le dossier data existe, sinon le créer
        $data_dir = dirname(Enums\DATA_PATH);
        if (!is_dir($data_dir)) {
            if (!@mkdir($data_dir, 0775, true)) {
                error_log("Impossible de créer le dossier : $data_dir");
                return false;
            }
        }
        
        // Vérifier les permissions
        if (!is_writable($data_dir)) {
            error_log("Le dossier n'est pas accessible en écriture : $data_dir");
            return false;
        }
        
        $json_data = json_encode($data, JSON_PRETTY_PRINT);
        
        // Utiliser @ pour supprimer l'avertissement et vérifier le retour
        if (@file_put_contents(Enums\DATA_PATH, $json_data) === false) {
            error_log("Impossible d'écrire dans le fichier : " . Enums\DATA_PATH);
            return false;
        }
        
        return true;
    },
    'add_promotion' => function ($new_promo) {
        $read_data = $GLOBALS['model']['read_data']();
        $promotions = $read_data['promotions'] ?? [];

        // Générer un ID unique (ex: timestamp)
        $new_promo['id'] = time();

        $promotions[] = $new_promo;
        $read_data['promotions'] = $promotions;

        $GLOBALS['model']['write_data']($read_data);
    },

    
    'generate_id' => function () {
        return uniqid();
    },
    
    // Fonctions d'authentification
    'authenticate' => function ($email, $password) use (&$model) {
        $data = $model['read_data']();
        
        // Utiliser array_filter au lieu de foreach
        $filtered_users = array_filter($data['users'], function ($user) use ($email, $password) {
            return $user['email'] === $email && $user['password'] === $password;
        });
        
        // Si aucun utilisateur ne correspond
        if (empty($filtered_users)) {
            return null;
        }
        
        // Récupérer le premier utilisateur qui correspond
        return reset($filtered_users);
    },
    
    'get_user_by_email' => function ($email) use (&$model) {
        $data = $model['read_data']();
        
        // Utiliser array_filter au lieu de foreach
        $filtered_users = array_filter($data['users'], function ($user) use ($email) {
            return $user['email'] === $email;
        });
        
        // Si aucun utilisateur ne correspond
        if (empty($filtered_users)) {
            return null;
        }
        
        // Récupérer le premier utilisateur qui correspond
        return reset($filtered_users);
    },
    
    'get_user_by_id' => function ($user_id) use (&$model) {
        $data = $model['read_data']();
        
        // Utiliser array_filter au lieu de foreach
        $filtered_users = array_filter($data['users'], function ($user) use ($user_id) {
            return $user['id'] === $user_id;
        });
        
        // Si aucun utilisateur ne correspond
        if (empty($filtered_users)) {
            return null;
        }
        
        // Récupérer le premier utilisateur qui correspond
        return reset($filtered_users);
    },
    
    'change_password' => function ($user_id, $new_password) use (&$model) {
        $data = $model['read_data']();
        
        $user_indices = array_keys(array_filter($data['users'], function($user) use ($user_id) {
            return $user['id'] === $user_id;
        }));
        
        if (empty($user_indices)) {
            return false;
        }
        
        $user_index = reset($user_indices);
        
        // Mettre à jour le mot de passe (sans cryptage)
        $data['users'][$user_index]['password'] = $new_password;
        
        // Sauvegarder les modifications
        return $model['write_data']($data);
    },
    
    // Fonctions pour les promotions
    'get_all_promotions' => function () use (&$model) {
        $data = $model['read_data']();
        return $data['promotions'] ?? [];
    },
    
    'get_promotion_by_id' => function ($id) use (&$model) {
        $data = $model['read_data']();
        
        // Utiliser array_filter au lieu de foreach
        $filtered_promotions = array_filter($data['promotions'] ?? [], function ($promotion) use ($id) {
            return $promotion['id'] === $id;
        });
        
        return !empty($filtered_promotions) ? reset($filtered_promotions) : null;
    },
    
    'promotion_name_exists' => function(string $name) use (&$model): bool {
        $data = $model['read_data']();
        
        foreach ($data['promotions'] as $promotion) {
            if (strtolower($promotion['name']) === strtolower($name)) {
                return true;
            }
        }
        
        return false;
    },
    
    'create_promotion' => function(array $promotion_data) use (&$model) {
        if (empty($promotion_data['name']) || empty($promotion_data['date_debut']) || empty($promotion_data['date_fin'])) {
            return false; // Données invalides
        }

        $data = $model['read_data']();
        $promotion_data['id'] = uniqid();
        $promotion_data['status'] = 'inactive';
        $data['promotions'][] = $promotion_data;

        return $model['write_data']($data);
    },
    $model['update_promotion'] = function ($updated_promotion) use (&$model) {
        $data = $model['read_data']();
        foreach ($data['promotions'] as &$promotion) {
            if ($promotion['id'] == $updated_promotion['id']) {
                $promotion = $updated_promotion;
                break;
            }
        }
        $model['write_data']($data);
    },
    
    'toggle_promotion_status' => function(int $promotion_id) use (&$model) {
        $data = $model['read_data']();
        
        // Trouver la promotion à modifier
        $target_index = null;
        foreach ($data['promotions'] as $index => $promotion) {
            if ((int)$promotion['id'] === $promotion_id) {
                $target_index = $index;
                break;
            }
        }
        
        if ($target_index === null) {
            return false;
        }
        
        // Si la promotion est inactive
        if ($data['promotions'][$target_index]['status'] === Status::INACTIVE->value) {
            // Désactiver d'abord toutes les autres promotions
            foreach ($data['promotions'] as &$promotion) {
                if ($promotion['id'] !== $promotion_id) {
                    $promotion['status'] = Status::INACTIVE->value;
                }
            }
            
            // Puis activer la promotion ciblée
            $data['promotions'][$target_index]['status'] = Status::ACTIVE->value;
        } else {
            // Si la promotion est active, la désactiver
            $data['promotions'][$target_index]['status'] = Status::INACTIVE->value;
        }
        
        // Sauvegarder les modifications
        return $model['write_data']($data);
    },
    
    'search_promotions' => function($search_term) use (&$model) {
        $promotions = $model['get_all_promotions']();
        
        if (empty($search_term)) {
            return $promotions;
        }
        
        return array_values(array_filter($promotions, function($promotion) use ($search_term) {
            return stripos($promotion['name'], $search_term) !== false;
        }));
    },
    
    // Fonctions pour les référentiels
    'get_all_referentiels' => function () use (&$model) {
        $data = $model['read_data']();
        return $data['referentiels'] ?? [];
    },
    
    'get_referentiel_by_id' => function ($id) use (&$model) {
        $data = $model['read_data']();
        
        // Utiliser array_filter au lieu de foreach
        $filtered_referentiels = array_filter($data['referentiels'] ?? [], function ($referentiel) use ($id) {
            return $referentiel['id'] === $id;
        });
        
        return !empty($filtered_referentiels) ? reset($filtered_referentiels) : null;
    },
    
    'referentiel_name_exists' => function($name) use (&$model) {
        $data = $model['read_data']();
        
        foreach ($data['referentiels'] as $referentiel) {
            if (strtolower($referentiel['name']) === strtolower($name)) {
                return true;
            }
        }
        
        return false;
    },
    
    'create_referentiel' => function(array $referentiel_data) use (&$model) {
        $data = $model['read_data']();

        // Générer un nouvel ID
        $max_id = 0;
        foreach ($data['referentiels'] as $referentiel) {
            $max_id = max($max_id, (int)$referentiel['id']);
        }

        $new_id = $max_id + 1;

        // Créer le nouveau référentiel avec l'état "en attente"
        $new_referentiel = [
            'id' => (string)$new_id,
            'name' => $referentiel_data['name'],
            'description' => $referentiel_data['description'],
            'capacity' => $referentiel_data['capacity'],
            'sessions' => $referentiel_data['sessions'],
            'etat' => 'en attente', // Par défaut, les nouveaux référentiels sont en attente
            'image' => $referentiel_data['image']
        ];

        // Ajouter le référentiel aux données
        $data['referentiels'][] = $new_referentiel;

        // Sauvegarder les données
        return $model['write_data']($data);
    },
    
    'get_referentiels_by_promotion' => function($promotion_id) use (&$model) {
        $data = $model['read_data']();
        
        // Trouver la promotion
        $promotion = null;
        foreach ($data['promotions'] as $p) {
            if ($p['id'] == $promotion_id) {
                $promotion = $p;
                break;
            }
        }
        
        if (!$promotion || empty($promotion['referentiels'])) {
            return [];
        }
        
        // Récupérer les référentiels associés
        return array_filter($data['referentiels'], function($ref) use ($promotion) {
            return in_array($ref['id'], $promotion['referentiels']);
        });
    },
    
    'assign_referentiels_to_promotion' => function ($promotion_id, $referentiel_ids) use (&$model) {
        $data = $model['read_data']();
        
        // Trouver l'index de la promotion
        $promotion_indices = array_keys(array_filter($data['promotions'], function($promotion) use ($promotion_id) {
            return $promotion['id'] === $promotion_id;
        }));
        
        if (empty($promotion_indices)) {
            return false;
        }
        
        $promotion_index = reset($promotion_indices);
        
        // Ajouter les référentiels à la promotion
        if (!isset($data['promotions'][$promotion_index]['referentiels'])) {
            $data['promotions'][$promotion_index]['referentiels'] = [];
        }
        
        $data['promotions'][$promotion_index]['referentiels'] = array_unique(
            array_merge($data['promotions'][$promotion_index]['referentiels'], $referentiel_ids)
        );
        
        return $model['write_data']($data);
    },
    
    'search_referentiels' => function(string $query) use (&$model) {
        $referentiels = $model['get_all_referentiels']();
        if (empty($query)) {
            return $referentiels;
        }
        
        return array_filter($referentiels, function($ref) use ($query) {
            return stripos($ref['name'], $query) !== false || 
                   stripos($ref['description'], $query) !== false;
        });
    },
    
    // Fonction pour récupérer la promotion active courante
    'get_current_promotion' => function () use (&$model) {
        $data = $model['read_data']();
        
        if (!isset($data['promotions'])) {
            return null;
        }

        // Filtrer les promotions actives
        $active_promotions = array_filter($data['promotions'], function ($promotion) {
            return $promotion['status'] === 'active';
        });

        if (empty($active_promotions)) {
            return null;
        }

        // Retourner la première promotion active
        return reset($active_promotions);
    },
    
    // Statistiques diverses pour le tableau de bord
    'get_promotions_stats' => function () use (&$model) {
        $data = $model['read_data']();
        
        // Nombre total de promotions
        $total_promotions = count($data['promotions'] ?? []);
        
        // Nombre de promotions actives
        $active_promotions = count(array_filter($data['promotions'] ?? [], function ($promotion) {
            return $promotion['status'] === Status::ACTIVE->value; // Correction ici
        }));
        
        // Récupérer la promotion courante
        $current_promotion = $model['get_current_promotion']();
        
        // Nombre d'apprenants dans la promotion courante
        $current_promotion_apprenants = 0;
        if ($current_promotion) {
            $current_promotion_apprenants = count(array_filter($data['apprenants'] ?? [], function ($apprenant) use ($current_promotion) {
                return $apprenant['promotion_id'] === $current_promotion['id'];
            }));
        }
        
        // Nombre de référentiels dans la promotion courante
        $current_promotion_referentiels = 0;
        if ($current_promotion) {
            $current_promotion_referentiels = count($current_promotion['referentiels'] ?? []);
        }
        
        return [
            'total_promotions' => $total_promotions,
            'active_promotions' => $active_promotions,
            'current_promotion_apprenants' => $current_promotion_apprenants,
            'current_promotion_referentiels' => $current_promotion_referentiels
        ];
    },
    
    // Fonctions pour les apprenants
    'get_all_apprenants' => function () use (&$model) {
        $data = $model['read_data']();
        return $data['apprenants'] ?? [];
    },
    
    'get_apprenants_by_promotion' => function ($promotion_id) use (&$model) {
        $data = $model['read_data']();
        
        // Si aucun apprenant n'existe dans le JSON, créer quelques exemples
        if (empty($data['apprenants'])) {
            $data['apprenants'] = [
                [
                    'id' => '1',
                    'matricule' => 'ODC-2023-0001',
                    'prenom' => 'John',
                    'nom' => 'Doe',
                    'email' => 'john.doe@example.com',
                    'telephone' => '770000000',
                    'date_naissance' => '1995-05-15',
                    'adresse' => 'Dakar, Sénégal',
                    'photo' => 'assets/images/default-avatar.png',
                    'referentiel_id' => '1', // Assurez-vous que ce référentiel existe
                    'statut' => 'Actif',
                    'promotion_id' => $promotion_id
                ],
                [
                    'id' => '2',
                    'matricule' => 'ODC-2023-0002',
                    'prenom' => 'Jane',
                    'nom' => 'Smith',
                    'email' => 'jane.smith@example.com',
                    'telephone' => '770000001',
                    'date_naissance' => '1997-08-20',
                    'adresse' => 'Dakar, Sénégal',
                    'photo' => 'assets/images/default-avatar.png',
                    'referentiel_id' => '2', // Assurez-vous que ce référentiel existe
                    'statut' => 'Actif',
                    'promotion_id' => $promotion_id
                ]
            ];
            
            // Sauvegarder ces exemples dans le JSON
            $model['write_data']($data);
        }
        
        // Filtrer les apprenants par promotion
        $apprenants = array_filter($data['apprenants'] ?? [], function ($apprenant) use ($promotion_id) {
            return isset($apprenant['promotion_id']) && $apprenant['promotion_id'] === $promotion_id;
        });
        
        // Si aucun apprenant n'est trouvé pour cette promotion, retourner tous les apprenants
        if (empty($apprenants) && !empty($data['apprenants'])) {
            // Associer tous les apprenants à cette promotion
            foreach ($data['apprenants'] as &$apprenant) {
                $apprenant['promotion_id'] = $promotion_id;
            }
            
            // Sauvegarder les modifications
            $model['write_data']($data);
            
            return $data['apprenants'];
        }
        
        return array_values($apprenants); // Réindexer le tableau
    },
    
    'get_apprenant_by_id' => function ($apprenant_id) use (&$model) {
        $data = $model['read_data']();
        
        // Filtrer les apprenants par ID
        $apprenants = array_filter($data['apprenants'] ?? [], function ($apprenant) use ($apprenant_id) {
            return $apprenant['id'] === $apprenant_id;
        });
        
        return !empty($apprenants) ? reset($apprenants) : null;
    },
    
    // Ajouter un apprenant à la liste d'attente
    'add_to_waiting_list' => function ($apprenant_data) use (&$model) {
        $data = $model['read_data']();
        
        // Initialiser la liste d'attente si elle n'existe pas
        if (!isset($data['waiting_list'])) {
            $data['waiting_list'] = [];
        }
        
        // Ajouter l'apprenant à la liste d'attente
        $data['waiting_list'][] = $apprenant_data;
        
        // Sauvegarder les données
        return $model['write_data']($data);
    },

    // Récupérer les apprenants de la liste d'attente
    'get_waiting_list' => function ($promotion_id = null) use (&$model) {
        $data = $model['read_data']();
        
        if (!isset($data['waiting_list'])) {
            return [];
        }
        
        // Filtrer par promotion si nécessaire
        if ($promotion_id) {
            return array_filter($data['waiting_list'], function($apprenant) use ($promotion_id) {
                return isset($apprenant['promotion_id']) && $apprenant['promotion_id'] === $promotion_id;
            });
        }
        
        return $data['waiting_list'];
    },

    // Vérifier si un email existe déjà
    'email_exists' => function ($email) use (&$model) {
        $data = $model['read_data']();
        
        // Vérifier dans les apprenants
        foreach ($data['apprenants'] ?? [] as $apprenant) {
            if (isset($apprenant['email']) && strtolower($apprenant['email']) === strtolower($email)) {
                return true;
            }
        }
        
        // Vérifier dans la liste d'attente
        foreach ($data['waiting_list'] ?? [] as $apprenant) {
            if (isset($apprenant['email']) && strtolower($apprenant['email']) === strtolower($email)) {
                return true;
            }
        }
        
        return false;
    },
    
    'create_apprenant' => function ($apprenant_data) use (&$model) {
        $data = $model['read_data']();
        
        // Ajouter l'apprenant aux données
        if (!isset($data['apprenants'])) {
            $data['apprenants'] = [];
        }
        
        $data['apprenants'][] = $apprenant_data;
        
        // Mettre à jour la promotion pour ajouter l'apprenant
        foreach ($data['promotions'] as &$promotion) {
            if ($promotion['id'] === $apprenant_data['promotion_id']) {
                if (!isset($promotion['apprenants'])) {
                    $promotion['apprenants'] = [];
                }
                $promotion['apprenants'][] = $apprenant_data['id'];
                break;
            }
        }
        
        // Sauvegarder les données
        return $model['write_data']($data);
    },
    
    'update_apprenant' => function ($apprenant_data) use (&$model) {
        $data = $model['read_data']();
        
        // Trouver l'index de l'apprenant à mettre à jour
        $apprenant_index = -1;
        foreach ($data['apprenants'] ?? [] as $index => $apprenant) {
            if ($apprenant['id'] === $apprenant_data['id']) {
                $apprenant_index = $index;
                break;
            }
        }
        
        if ($apprenant_index === -1) {
            return false;
        }
        
        // Mettre à jour l'apprenant
        $data['apprenants'][$apprenant_index] = $apprenant_data;
        
        // Sauvegarder les données
        return $model['write_data']($data);
    },
    
    'delete_apprenant' => function ($apprenant_id) use (&$model) {
        $data = $model['read_data']();
        
        // Récupérer l'apprenant avant de le supprimer
        $apprenant = null;
        foreach ($data['apprenants'] ?? [] as $a) {
            if ($a['id'] === $apprenant_id) {
                $apprenant = $a;
                break;
            }
        }
        
        if (!$apprenant) {
            return false;
        }
        
        // Supprimer l'apprenant de la promotion
        foreach ($data['promotions'] as &$promotion) {
            if ($promotion['id'] === $apprenant['promotion_id']) {
                if (isset($promotion['apprenants'])) {
                    $promotion['apprenants'] = array_filter($promotion['apprenants'], function ($id) use ($apprenant_id) {
                        return $id !== $apprenant_id;
                    });
                    // Réindexer le tableau
                    $promotion['apprenants'] = array_values($promotion['apprenants']);
                }
                break;
            }
        }
        
        // Filtrer les apprenants pour exclure celui à supprimer
        $data['apprenants'] = array_filter($data['apprenants'] ?? [], function ($a) use ($apprenant_id) {
            return $a['id'] !== $apprenant_id;
        });
        
        // Réindexer le tableau
        $data['apprenants'] = array_values($data['apprenants']);
        
        // Sauvegarder les données
        return $model['write_data']($data);
    },
    
    'get_apprenant_by_matricule' => function ($matricule) use (&$model) {
        $data = $model['read_data']();
        
        // Filtrer les apprenants par matricule
        $filtered_apprenants = array_filter($data['apprenants'] ?? [], function ($apprenant) use ($matricule) {
            return $apprenant['matricule'] === $matricule;
        });
        
        return !empty($filtered_apprenants) ? reset($filtered_apprenants) : null;
    },
    
    'generate_matricule' => function () use (&$model) {
        $data = $model['read_data']();
        $year = date('Y');
        $count = count($data['apprenants'] ?? []) + 1;
        
        return 'ODC-' . $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    },
    
    'get_statistics' => function() use (&$model) {
        $data = $model['read_data']();
        
        // Trouver la promotion active
        $active_promotion = $model['get_current_promotion']();
        
        // Compter les référentiels de la promotion active
        $active_promotion_referentiels = 0;
        if ($active_promotion && isset($active_promotion['referentiels'])) {
            $active_promotion_referentiels = count($active_promotion['referentiels']);
        }
        
        // Compter les apprenants de la promotion active
        $active_learners = 0;
        if ($active_promotion && isset($active_promotion['apprenants'])) {
            $active_learners = count($active_promotion['apprenants']);
        }
        
        // Calculer les autres statistiques
        $active_promotions = array_filter($data['promotions'] ?? [], function($promotion) {
            return $promotion['status'] === 'active';
        });
        
        return [
            'active_learners' => $active_learners,
            'total_referentials' => $active_promotion_referentiels,
            'active_promotions' => count($active_promotions),
            'total_promotions' => count($data['promotions'] ?? [])
        ];
    },
    'remove_referentiel_from_promo' => function ($promotion_id, $referentiel_id) use (&$model) {
        $data = $model['read_data']();

        // Trouver la promotion
        foreach ($data['promotions'] as &$promo) {
            if ($promo['id'] == $promotion_id) {
                // Supprimer le référentiel de la liste
                $promo['referentiels'] = array_values(array_filter($promo['referentiels'], function ($id) use ($referentiel_id) {
                    return $id != $referentiel_id;
                }));

                // Sauvegarder les modifications
                return $model['write_data']($data);
            }
        }

        return false;
    },
    'update_promotion_states' => function () use (&$model) {
        $data = $model['read_data']();
        $today = date('Y-m-d');

        foreach ($data['promotions'] as &$promotion) {
            // Si la date de fin est dépassée, l'état est "terminé"
            if ($promotion['date_fin'] < $today) {
                $promotion['etat'] = 'terminé';
            }
            // Si la promotion est active et la date de fin n'est pas dépassée, l'état est "en cours"
            elseif ($promotion['status'] === 'active') {
                $promotion['etat'] = 'en cours';
            }
            // Toutes les autres promotions sont "en attente"
            else {
                $promotion['etat'] = 'en attente';
            }
        }

        $model['write_data']($data);
    },
    'get_referentiel_by_id' => function ($referentiel_id) use (&$model) {
        $data = $model['read_data']();
        
        // Filtrer les référentiels par ID
        $referentiels = array_filter($data['referentiels'] ?? [], function ($referentiel) use ($referentiel_id) {
            return $referentiel['id'] === $referentiel_id;
        });
        
        return !empty($referentiels) ? reset($referentiels) : null;
    },
    'update_referentiel_states' => function () use (&$model) {
        $data = $model['read_data']();

        foreach ($data['referentiels'] as &$referentiel) {
            // Si le référentiel est déjà "terminé" ou "en cours", on ne change pas son état
            if (in_array($referentiel['etat'], ['terminé', 'en cours'])) {
                continue;
            }

            // Tous les autres référentiels sont mis en "en attente"
            $referentiel['etat'] = 'en attente';
        }

        $model['write_data']($data);
    },
    // Mettre à jour un apprenant dans la liste d'attente
    'update_waiting_apprenant' => function ($apprenant_data) use (&$model) {
        $data = $model['read_data']();
        
        if (!isset($data['waiting_list'])) {
            return false;
        }
        
        // Trouver l'index de l'apprenant dans la liste d'attente
        $apprenant_index = -1;
        foreach ($data['waiting_list'] as $index => $apprenant) {
            if ($apprenant['id'] === $apprenant_data['id']) {
                $apprenant_index = $index;
                break;
            }
        }
        
        if ($apprenant_index === -1) {
            return false;
        }
        
        // Mettre à jour l'apprenant
        $data['waiting_list'][$apprenant_index] = $apprenant_data;
        
        // Sauvegarder les données
        return $model['write_data']($data);
    },

    // Supprimer un apprenant de la liste d'attente
    'remove_from_waiting_list' => function ($apprenant_id) use (&$model) {
        $data = $model['read_data']();
        
        if (!isset($data['waiting_list'])) {
            return false;
        }
        
        // Filtrer la liste d'attente pour supprimer l'apprenant
        $data['waiting_list'] = array_filter($data['waiting_list'], function($apprenant) use ($apprenant_id) {
            return $apprenant['id'] !== $apprenant_id;
        });
        
        // Réindexer le tableau
        $data['waiting_list'] = array_values($data['waiting_list']);
        
        // Sauvegarder les données
        return $model['write_data']($data);
    },
];