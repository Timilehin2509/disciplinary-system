<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../utils/Response.php';
require_once __DIR__ . '/../../models/Student.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Response::error('Method not allowed', 405);
}

// Get POST data
$data = json_decode(file_get_contents("php://input"));

if (!isset($data->username) || !isset($data->password)) {
    Response::error('Missing username or password');
}

error_log("Login attempt with username: " . $data->username);

// Try staff/admin login first
$user = new User();
if ($user->login($data->username, $data->password)) {
    $_SESSION['user_id'] = $user->id;
    $_SESSION['role'] = $user->role;
    $_SESSION['last_activity'] = time();
    
    error_log("Login successful. Session data: " . print_r($_SESSION, true));
    
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
    error_log("Login failed for username: " . $data->username);
    
    // Try student login if staff/admin login fails
    $student = new Student();
    if ($student->login($data->username, $data->password)) {
        $_SESSION['student_id'] = $student->id;
        $_SESSION['role'] = 'student';
        $_SESSION['last_activity'] = time();
        
        Response::json([
            'message' => 'Login successful',
            'user' => [
                'id' => $student->id,
                'name' => $student->name,
                'role' => 'student',
                'email' => $student->email,
                'class' => $student->class
            ]
        ]);
    } else {
        Response::error('Invalid credentials', 401);
    }
}