<?php
require_once __DIR__ . '/../../models/Incident.php';
require_once __DIR__ . '/../../middleware/Auth.php';
require_once __DIR__ . '/../../utils/Response.php';

Auth::requireRole('student');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    Response::error('Method not allowed', 405);
}

$student_id = $_SESSION['student_id'];

// Add getStudentIncidents method to Incident model
$incident = new Incident();
$records = $incident->getStudentIncidents($student_id);

Response::json($records);