<?php
require_once('includes/authorization.php');

if (!Authorization::baseUser()) {
    exit(json_encode(['type' => 'error', 'content' => 'Failed to authorize']));
}

require_once('includes/headers.php');

header('Content-type: application/json');

$filepath = '..' . str_replace('/', DIRECTORY_SEPARATOR, $data['filepath']);

function recursiveDelete(String $filepathparam) : bool {
    if (is_file($filepathparam)) {
        return unlink($filepathparam);
    }
    if (is_dir($filepathparam)) {
        foreach(scandir($filepathparam) as $file) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            recursiveDelete($filepathparam . DIRECTORY_SEPARATOR . $file);
            }
        return rmdir($filepathparam);
        }
    return false;
    }

if (recursiveDelete($filepath)) {
    exit(json_encode(['type' => 'success', 'content' => 'File successfully deleted']));
} else {
    exit(json_encode(['type' => 'error', 'content' => 'Failed to delete file']));
}