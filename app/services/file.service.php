<?php

namespace App\Services;

use App\Enums;

$file_services = [
    'handle_promotion_image' => function(array $file): ?string {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }
        
        $upload_dir = Enums\UPLOAD_DIR . '/promotions/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $file_name = uniqid() . '.' . $file_extension;
        $target_path = $upload_dir . '/' . $file_name;
        
        return move_uploaded_file($file['tmp_name'], $target_path) ? $file_name : null;
    }
];