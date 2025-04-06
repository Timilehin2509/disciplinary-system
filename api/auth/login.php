<?php
session_start();
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../utils/Response.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Response::error('Method not allowed', 405);
}

// Get POST data
$data = json_decode(file_get_contents("php://input"));

if (!isset($data->username) || !isset($data->password)) {
    Response::error('Missing username or password');
}

$user = new User();
if ($user->login($data->username, $data->password)) {
    $_SESSION['user_id'] = $user->id;
    $_SESSION['role'] = $user->role;
    
    Response::json([
        'message' => 'Login successful',
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'role' => $user->role,
            'email' => $user->email
        ]
    ]);
} else {
    Response::error('Invalid credentials', 401);
}