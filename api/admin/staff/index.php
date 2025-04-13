<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('X-XSS-Protection: 1; mode=block');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SERVER['CONTENT_TYPE']) || $_SERVER['CONTENT_TYPE'] !== 'application/json') {
        Response::error('Content-Type must be application/json', 415);
    }
}

require_once __DIR__ . '/../../../models/User.php';
require_once __DIR__ . '/../../../middleware/Auth.php';
require_once __DIR__ . '/../../../utils/Response.php';

error_log("POST DATA: " . file_get_contents("php://input"));
error_log("SESSION: " . print_r($_SESSION, true));

Auth::requireRole('admin');

$user = new User();

switch($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        // List all staff
        $staff = $user->getAllStaff();
        
        // Sanitize sensitive data from response
        foreach ($staff as &$member) {
            unset($member['password']);
            unset($member['created_at']);
            unset($member['updated_at']);
        }
        
        Response::json($staff);
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        
        // Enhanced JSON validation
        if ($data === null) {
            $jsonError = json_last_error_msg();
            error_log("JSON decode error: " . $jsonError);
            Response::error('Invalid JSON payload: ' . $jsonError);
        }
        
        // Type validation
        if (!is_object($data)) {
            Response::error('Invalid payload format - object expected');
        }
        
        // Advanced field validation
        $required_fields = ['username', 'password', 'name', 'email'];
        $validation_errors = [];
        
        foreach ($required_fields as $field) {
            if (!isset($data->$field)) {
                $validation_errors[] = "Missing field: {$field}";
            } elseif (empty(trim($data->$field))) {
                $validation_errors[] = "Empty field: {$field}";
            }
        }
        
        if (!empty($validation_errors)) {
            Response::error(['message' => 'Validation failed', 'errors' => $validation_errors]);
        }
        
        // Enhanced password validation
        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/', $data->password)) {
            Response::error('Password must be at least 8 characters and contain uppercase, lowercase, and numbers');
        }
        
        // Sanitize and validate email with DNS check
        $email = filter_var(trim($data->email), FILTER_SANITIZE_EMAIL);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !checkdnsrr(substr(strrchr($email, "@"), 1))) {
            Response::error('Invalid or unreachable email domain');
        }
        
        // Username uniqueness check
        if ($user->usernameExists($data->username)) {
            Response::error('Username already exists');
        }
        
        // Force role to be staff
        $data->role = 'staff';
        
        error_log("Creating staff with data: " . print_r($data, true));
        
        // Sanitize data before saving
        $data->username = trim($data->username);
        $data->name = trim($data->name);
        $data->email = trim($data->email);
        
        $id = $user->create($data);
        if($id) {
            Response::json(['id' => $id, 'message' => 'Staff account created successfully'], 201);
        } else {
            Response::error('Failed to create staff account');
        }
        break;

    default:
        Response::error('Method not allowed', 405);
}