<?php
if (!is_dir(__DIR__ . '/../uploads/')) {
    die("Upload directory does not exist");
}

return [
    'db_host' => 'localhost',
    'db_port' => '3307',
    'db_name' => 'disciplinary_system',
    'db_user' => 'root',
    'db_pass' => '',
    
    // JWT settings
    'jwt_secret' => 'your_jwt_secret_key_here',
    'jwt_expiration' => 3600, // 1 hour
    
    // Upload settings
    'upload_dir' => __DIR__ . '/../uploads/',
    'max_file_size' => 5 * 1024 * 1024, // 5MB
    'allowed_extensions' => ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png']
];