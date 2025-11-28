<?php
session_start();
require_once __DIR__ . "/functions.php";

ini_set('display_errors', 1);
error_reporting(E_ALL);

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid JSON"]);
    exit;
}

if (!isset($_SESSION['username'])) {
    http_response_code(401);
    echo json_encode(["error" => "Not logged in"]);
    exit;
}

updateStatus(e($data['id']), e($data['status']), e($data['date']), e($data['note']));