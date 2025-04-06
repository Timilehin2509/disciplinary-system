<?php
require_once __DIR__ . '/../../../models/User.php';
require_once __DIR__ . '/../../../middleware/Auth.php';
require_once __DIR__ . '/../../../utils/Response.php';

Auth::requireRole('admin');

$user = new User();
$id = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

switch($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        $result = $user->getById($id);
        if($result && $result['role'] === 'staff') {
            unset($result['password']); // Remove sensitive data
            Response::json($result);
        } else {
            Response::error('Staff member not found', 404);
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));
        $data->id = $id;
        $data->role = 'staff'; // Ensure role remains staff
        
        if($user->update($data)) {
            Response::json(['message' => 'Staff record updated successfully']);
        } else {
            Response::error('Failed to update staff record');
        }
        break;

    case 'DELETE':
        // Verify it's a staff account before deletion
        $staff = $user->getById($id);
        if(!$staff || $staff['role'] !== 'staff') {
            Response::error('Staff member not found', 404);
        }
        
        if($user->delete($id)) {
            Response::json(['message' => 'Staff account deleted successfully']);
        } else {
            Response::error('Failed to delete staff account');
        }
        break;

    default:
        Response::error('Method not allowed', 405);
}