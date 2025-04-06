<?php
require_once __DIR__ . '/../utils/Response.php';

class Auth {
    public static function checkAuth() {
        session_start();
        
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
        self::checkAuth();
        
        // Get current user's role
        $currentRole = isset($_SESSION['role']) ? $_SESSION['role'] : 'student';
        
        // Check if current role is allowed
        if (!in_array($currentRole, (array)$allowedRoles)) {
            Response::forbidden('You do not have permission to access this resource');
        }
    }
}