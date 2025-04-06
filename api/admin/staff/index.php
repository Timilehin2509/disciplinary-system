<?php
require_once __DIR__ . '/../../../models/User.php';
require_once __DIR__ . '/../../../middleware/Auth.php';
require_once __DIR__ . '/../../../utils/Response.php';

Auth::requireRole('admin');

$user = new User();

switch($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        // List all staff accounts
        $staff = $user->getAllStaff();
        Response::json($staff);
        break;

    case 'POST':
        // Add new staff account
        $data = json_decode(file_get_contents("php://input"));
        
        if(!isset($data->username) || !isset($data->password) || 
           !isset($data->name) || !isset($data->email)) {
            Response::error('Missing required fields');
        }
        
        $data->role = 'staff';  // Force role to be staff
        $id = $user->create($data);
        
        if($id) {
            Response::json(['id' => $id, 'message' => 'Staff account created successfully'], 201);
        } else {
            Response::error('Failed to create staff account');
        }
        break;

    default:
        Response::error('Method not allowed', 405);
}