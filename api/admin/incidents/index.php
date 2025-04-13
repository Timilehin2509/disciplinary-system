<?php
require_once __DIR__ . '/../../../models/Incident.php';
require_once __DIR__ . '/../../../middleware/Auth.php';
require_once __DIR__ . '/../../../utils/Response.php';

// Add debug logging
ini_set('display_errors', 1);
error_reporting(E_ALL);
error_log("Request received at staff creation endpoint");
error_log("Session data: " . print_r($_SESSION, true));

Auth::requireRole('admin');

$incident = new Incident();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Get status filter if provided
    $filters = [];
    if (isset($_GET['status'])) {
        $filters['status'] = $_GET['status'];
    }
    
    $incidents = $incident->getAll($filters);
    Response::json($incidents);
} else {
    Response::error('Method not allowed', 405);
}