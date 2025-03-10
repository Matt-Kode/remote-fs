<?php
require_once('includes/authorization.php');

if (!Authorization::baseUser()) {
    exit(json_encode(['type' => 'error', 'content' => 'Failed to authorize']));
}

require_once('includes/headers.php');
require_once('includes/functions.php');

header('Content-type: application/json');

$filepath = '..' . str_replace('/', DIRECTORY_SEPARATOR, $data['filepath']);

if (recursiveDelete($filepath)) {
    exit(json_encode(['type' => 'success', 'content' => 'File successfully deleted']));
} else {
    exit(json_encode(['type' => 'error', 'content' => 'Failed to delete file']));
}