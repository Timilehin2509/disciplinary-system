<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../utils/Response.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Response::error('Method not allowed', 405);
}

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->username) || !isset($data->password) || 
    !isset($data->name) || !isset($data->email)) {
    Response::error('Missing required fields');
}

// Create admin user
$user = new User();
$data->role = 'admin';  // Force role to be admin
$id = $user->create($data);

if ($id) {
    Response::json(['id' => $id, 'message' => 'Admin created successfully'], 201);
} else {
    Response::error('Failed to create admin');
}