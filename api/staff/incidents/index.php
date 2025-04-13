<?php
require_once __DIR__ . '/../../../models/Incident.php';
require_once __DIR__ . '/../../../middleware/Auth.php';
require_once __DIR__ . '/../../../utils/Response.php';

Auth::requireRole('staff');

$incident = new Incident();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"));
    
    // Validate required fields
    if (!isset($data->type) || !isset($data->description) || 
        !isset($data->date_of_incidence) || !isset($data->students_involved)) {
        Response::error('Missing required fields');
    }
    
    // Set default values
    $data->date_reported = date('Y-m-d');
    $data->status = 'Open';
    $data->reporter_id = $_SESSION['user_id'];
    $data->students = $data->students_involved; // Map to model property name
    
    // Handle file uploads if present
    if (isset($_FILES['supporting_documents'])) {
        $config = require __DIR__ . '/../../../config/config.php';
        $uploadedFiles = [];
        
        foreach ($_FILES['supporting_documents']['tmp_name'] as $key => $tmp_name) {
            $file_name = $_FILES['supporting_documents']['name'][$key];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            
            if (!in_array($file_ext, $config['allowed_extensions'])) {
                Response::error("File type not allowed: $file_name");
            }
            
            // Generate unique filename
            $new_file_name = uniqid() . '.' . $file_ext;
            $upload_path = $config['upload_dir'] . $new_file_name;
            
            if (move_uploaded_file($tmp_name, $upload_path)) {
                $uploadedFiles[] = $new_file_name;
            }
        }
        
        $data->supporting_documents = json_encode($uploadedFiles);
    }
    
    $id = $incident->create($data);
    if ($id) {
        Response::json(['id' => $id, 'message' => 'Incident reported successfully'], 201);
    } else {
        Response::error('Failed to report incident');
    }
} else {
    Response::error('Method not allowed', 405);
}