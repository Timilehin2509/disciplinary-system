<?php
session_start();
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../models/Student.php';
require_once __DIR__ . '/../../utils/Response.php';

// Only allow GET requests
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    Response::error('Method not allowed', 405);
}

// Check if user is logged in
if (!isset($_SESSION['user_id']) && !isset($_SESSION['student_id'])) {
    Response::unauthorized();
}

// Get user details based on role
if (isset($_SESSION['user_id'])) {
    $user = new User();
    $details = $user->getById($_SESSION['user_id']);
    
    if (!$details) {
        Response::error('User not found', 404);
    }
    
    // Remove sensitive data
    unset($details['password']);
    
    Response::json([
        'id' => $details['id'],
        'username' => $details['username'],
        'role' => $details['role'],
        'name' => $details['name'],
        'email' => $details['email']
    ]);
} else {
    $student = new Student();
    $details = $student->getById($_SESSION['student_id']);
    
    if (!$details) {
        Response::error('Student not found', 404);
    }
    
    Response::json([
        'id' => $details['id'],
        'student_number' => $details['student_number'],
        'role' => 'student',
        'name' => $details['name'],
        'email' => $details['email'],
        'class' => $details['class']
    ]);
}