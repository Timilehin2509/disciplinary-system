<?php
require_once __DIR__ . '/../../../models/Incident.php';
require_once __DIR__ . '/../../../middleware/Auth.php';
require_once __DIR__ . '/../../../utils/Response.php';

Auth::requireRole('staff');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    Response::error('Method not allowed', 405);
}

$incident = new Incident();
$incidents = $incident->getByReporterId($_SESSION['user_id']);

Response::json($incidents);