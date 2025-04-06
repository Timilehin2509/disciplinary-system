<?php
require_once __DIR__ . '/../../../models/Incident.php';
require_once __DIR__ . '/../../../middleware/Auth.php';
require_once __DIR__ . '/../../../utils/Response.php';

Auth::requireRole('admin');

$incident = new Incident();
$id = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

switch($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        $result = $incident->getById($id);
        if($result) {
            Response::json($result);
        } else {
            Response::error('Incident not found', 404);
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));
        
        if(!isset($data->status)) {
            Response::error('Status is required');
        }
        
        if($incident->updateStatus($id, $data->status, $_SESSION['user_id'])) {
            Response::json(['message' => 'Incident status updated successfully']);
        } else {
            Response::error('Failed to update incident status');
        }
        break;

    default:
        Response::error('Method not allowed', 405);
}