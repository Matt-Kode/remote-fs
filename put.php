<?php
require_once('includes/authorization.php');

if (!Authorization::baseUser()) {
    exit(json_encode(['type' => 'error', 'content' => 'Failed to authorize']));
}

require_once ('includes/headers.php');

header("Content-type: application/json");

$filepath = '..' . str_replace('/', DIRECTORY_SEPARATOR, $data['filepath']);;
$content  = $data['content'];

if (!file_exists($filepath)) {
    exit(json_encode(['type' => 'error', 'content' => 'File no longer exists']));
}

if (file_put_contents($filepath, $content)) {
    exit(json_encode(['type' => 'success', 'content' => 'Successfully saved file']));
}

exit(json_encode(['type' => 'error', 'content' => 'Failed to save file']));


