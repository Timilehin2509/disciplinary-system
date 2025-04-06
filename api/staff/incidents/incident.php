<?php
require_once __DIR__ . '/../../../models/Incident.php';
require_once __DIR__ . '/../../../middleware/Auth.php';
require_once __DIR__ . '/../../../utils/Response.php';

Auth::requireRole('staff');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    Response::error('Method not allowed', 405);
}

$incident = new Incident();
$id = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Get incident details
$result = $incident->getById($id);

// Check if incident exists and belongs to the staff member
if (!$result || $result['reporter_id'] != $_SESSION['user_id']) {
    Response::error('Incident not found or access denied', 404);
}

Response::json($result);