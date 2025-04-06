<?php
require_once __DIR__ . '/../../../../models/Incident.php';
require_once __DIR__ . '/../../../../middleware/Auth.php';
require_once __DIR__ . '/../../../../utils/Response.php';

Auth::requireRole('admin');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    Response::error('Method not allowed', 405);
}

$incident = new Incident();
$period = isset($_GET['period']) ? $_GET['period'] : '30'; // Default to 30 days
$trend = $incident->getTrend($period);

Response::json($trend);