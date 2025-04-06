<?php
require_once __DIR__ . '/../../../../models/Incident.php';
require_once __DIR__ . '/../../../../middleware/Auth.php';
require_once __DIR__ . '/../../../../utils/Response.php';

Auth::requireRole('admin');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    Response::error('Method not allowed', 405);
}

$incident = new Incident();
$total = $incident->getTotalCount();

Response::json(['total' => $total]);