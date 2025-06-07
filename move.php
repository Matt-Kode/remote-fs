<?php
require_once "includes/authorization.php";
if (!Authorization::baseUser()) {
    exit(json_encode(['type' => 'error', 'content' => 'Failed to authorize']));
}
require_once('includes/headers.php');
require_once('includes/functions.php');

header("Content-Type: application/json");

$filepath = (str_starts_with($data['filepath'], 'deleted_files') ? '' : '..') . str_replace('/', DIRECTORY_SEPARATOR, $data['filepath']);
$destination = $data['destination'] === 'deleted_files' ? $data['destination'] : '..' . str_replace('/', DIRECTORY_SEPARATOR, $data['destination']);

if (!validatePath($filepath)) {
    exit(json_encode(['type' => 'error', 'content' => 'Invalid path']));
}

if (moveFile($filepath, $destination)) {
    exit(json_encode(['type' => 'success', 'content' => 'Successfully completed operation']));
}
exit(json_encode(['type' => 'error', 'content' => 'Operation failed']));