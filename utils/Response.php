<?php
class Response {
    public static function json($data, $status = 200) {
        header('Content-Type: application/json');
        http_response_code($status);
        
        // Add session check before logging
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Only log session data if it exists
        if (isset($_SESSION)) {
            error_log("Session ID: " . session_id());
            error_log("Session Data: " . print_r($_SESSION, true));
        }
        
        echo json_encode($data);
        exit;
    }

    public static function error($message, $status = 400) {
        header('Content-Type: application/json');
        http_response_code($status);
        
        $response = is_array($message) 
            ? $message 
            : ['message' => $message];
            
        echo json_encode($response);
        exit;
    }

    public static function unauthorized($message = 'Unauthorized') {
        self::error($message, 401);
    }

    public static function forbidden($message = 'Forbidden') {
        self::error($message, 403);
    }
}