<?php
session_start();
require_once __DIR__ . '/../../utils/Response.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Response::error('Method not allowed', 405);
}

// Clear session
session_destroy();
Response::json(['message' => 'Logged out successfully']);