<?php
require_once __DIR__ . '/../../../models/Incident.php';
require_once __DIR__ . '/../../../middleware/Auth.php';
require_once __DIR__ . '/../../../utils/Response.php';

Auth::requireRole('admin');

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    Response::error('Method not allowed', 405);
}

$incident = new Incident();
$id = basename(dirname(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)));

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->judgments) || !is_array($data->judgments)) {
    Response::error('Invalid judgment data');
}

// Validate each judgment
foreach ($data->judgments as $judgment) {
    if (!isset($judgment->student_id) || !isset($judgment->punishment) || !isset($judgment->details)) {
        Response::error('Missing required judgment fields');
    }
}

if ($incident->updateJudgments($id, $data->judgments)) {
    Response::json(['message' => 'Judgments updated successfully']);
} else {
    Response::error('Failed to update judgments');
}