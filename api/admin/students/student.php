<?php
require_once __DIR__ . '/../../../models/Student.php';
require_once __DIR__ . '/../../../middleware/Auth.php';
require_once __DIR__ . '/../../../utils/Response.php';

Auth::requireRole('admin');

$student = new Student();
$id = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

switch($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        $result = $student->getById($id);
        if($result) {
            Response::json($result);
        } else {
            Response::error('Student not found', 404);
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));
        $data->id = $id;
        
        if($student->update($data)) {
            Response::json(['message' => 'Student updated successfully']);
        } else {
            Response::error('Failed to update student');
        }
        break;

    case 'DELETE':
        if($student->delete($id)) {
            Response::json(['message' => 'Student deleted successfully']);
        } else {
            Response::error('Failed to delete student');
        }
        break;

    default:
        Response::error('Method not allowed', 405);
}