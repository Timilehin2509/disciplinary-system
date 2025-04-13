<?php
require_once __DIR__ . '/../utils/Response.php';

class Auth {
    public static function checkAuth() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Check session timeout (30 minutes)
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
            session_unset();
            session_destroy();
            Response::unauthorized('Session expired');
        }
        $_SESSION['last_activity'] = time();

        // Check authentication
        if (!isset($_SESSION['user_id']) && !isset($_SESSION['student_id'])) {
            Response::unauthorized();
        }
    }
    
    public static function requireRole($allowedRoles) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        error_log("SESSION DATA IN AUTH: " . print_r($_SESSION, true));
        error_log("ALLOWED ROLES: " . print_r($allowedRoles, true));
        
        // Check if user is logged in
        if (!isset($_SESSION['user_id']) && !isset($_SESSION['student_id'])) {
            Response::unauthorized('No user found');
        }

        // Check if role exists
        if (!isset($_SESSION['role'])) {
            Response::unauthorized('No role found');
        }

        // Convert to array if string
        $allowedRoles = (array)$allowedRoles;
        
        // Check if current role is allowed
        if (!in_array($_SESSION['role'], $allowedRoles)) {
            Response::forbidden('You do not have permission');
        }
    }
}