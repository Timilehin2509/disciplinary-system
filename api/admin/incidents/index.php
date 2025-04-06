<?php
require_once __DIR__ . '/../../../models/Incident.php';
require_once __DIR__ . '/../../../middleware/Auth.php';
require_once __DIR__ . '/../../../utils/Response.php';

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