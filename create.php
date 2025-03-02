<?php
require_once('includes/authorization.php');

if (!Authorization::baseUser()) {
exit(json_encode(['type' => 'error', 'content' => 'Failed to authorize']));
}

require_once('includes/headers.php');

header('Content-Type: application/json');

$filepath= '..' . str_replace('/', DIRECTORY_SEPARATOR, $data['filepath']);
$filetype = $data['filetype'];
$filename = $data['filename'];

if ($filetype === 'dir') {
    if (is_dir($filepath . $filename)) {
        exit(json_encode(['type' => 'error', 'content' => 'Directory already exists']));
    }
    mkdir($filepath . $filename, 0777, true);
    exit(json_encode(['type' => 'success', 'content' => 'Directory created successfully']));
}

if ($filetype === 'file') {
    if (is_file($filepath . $filename)) {
        exit(json_encode(['type' => 'error', 'content' => 'File already exists']));
    }
    file_put_contents($filepath . $filename, "");
    exit(json_encode(['type' => 'success', 'content' => 'File created successfully']));
}

exit(json_encode(['type' => 'error', 'content' => 'Something went wrong']));