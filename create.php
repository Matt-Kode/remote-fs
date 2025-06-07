<?php
require_once('includes/authorization.php');

if (!Authorization::baseUser()) {
exit(json_encode(['type' => 'error', 'content' => 'Failed to authorize']));
}

require_once('includes/headers.php');
require_once('includes/functions.php');

header('Content-Type: application/json');

$filepath= '..' . str_replace('/', DIRECTORY_SEPARATOR, $data['filepath']);
$filetype = $data['filetype'];
$filename = $data['filename'];

if (!validatePath($filepath)) {
    exit(json_encode(['type' => 'error', 'content' => 'Invalid path']));
}

if (!validateFilename($filename)) {
    exit(json_encode(['type' => 'error', 'content' => 'Invalid filename']));
}

if ($filetype === 'dir') {
    if (is_dir($filepath . DIRECTORY_SEPARATOR . $filename)) {
        exit(json_encode(['type' => 'error', 'content' => 'Directory already exists']));
    }
    mkdir($filepath .DIRECTORY_SEPARATOR . $filename, 0777, true);
    exit(json_encode(['type' => 'success', 'content' => 'Directory created successfully']));
}

if ($filetype === 'file') {
    if (is_file($filepath .DIRECTORY_SEPARATOR . $filename)) {
        exit(json_encode(['type' => 'error', 'content' => 'File already exists']));
    }
    file_put_contents($filepath . DIRECTORY_SEPARATOR .$filename, "");
    exit (json_encode(['type' => 'success', 'content' => 'File created successfully']));

}

exit(json_encode(['type' => 'error', 'content' => 'Something went wrong']));
