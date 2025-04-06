<?php
require_once __DIR__ . '/../../../models/Student.php';
require_once __DIR__ . '/../../../middleware/Auth.php';
require_once __DIR__ . '/../../../utils/Response.php';

// Check authentication and admin role
Auth::requireRole('admin');

$student = new Student();

switch($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        // List all students
        $students = $student->getAll();
        Response::json($students);
        break;

    case 'POST':
        // Create new student
        $data = json_decode(file_get_contents("php://input"));
        
        // Validate required fields
        if(!isset($data->student_number) || !isset($data->name) || 
           !isset($data->email) || !isset($data->class) || !isset($data->password)) {
            Response::error('Missing required fields');
        }
        
        $id = $student->create($data);
        if($id) {
            Response::json(['id' => $id, 'message' => 'Student created successfully'], 201);
        } else {
            Response::error('Failed to create student');
        }
        break;

    default:
        Response::error('Method not allowed', 405);
}