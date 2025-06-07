<?php
require_once('includes/authorization.php');

if (!Authorization::baseUser()) {
    exit(json_encode(['type' => 'error', 'content' => 'Failed to authorize']));
}

require_once('includes/headers.php');
require_once('includes/functions.php');

header("Content-type: application/json");

$filepath = '..' . str_replace('/', DIRECTORY_SEPARATOR, $data['filepath']);

if (!validatePath($filepath)) {
    exit(json_encode(['type' => 'error', 'content' => 'Invalid path']));
}

if (is_file($filepath)) {
    if (editableFile($filepath)) {
        exit (json_encode(['type' => 'file', 'content' => file_get_contents($filepath)]));
    } else {
        exit (json_encode(['type' => 'not_viewable']));
    }
}

if ($dir = scandir($filepath)) {
    $files = [];
    $subdirs = [];
    $count = 0;
    foreach ($dir as $file) {
        if ($filepath == '..' . DIRECTORY_SEPARATOR && $file == 'remote') {
            continue;
        }
        if ($file == '.' || $file == '..') {
            continue;
        }
        if (is_dir($filepath . DIRECTORY_SEPARATOR . $file)) {
            $subdirs[$count] = ['type' => 'dir', 'name' => $file];
        } else {
            $filesize  = ceil(filesize($filepath . DIRECTORY_SEPARATOR . $file) / 1024) . ' Kb';
            if (editableFile($filepath . DIRECTORY_SEPARATOR . $file)) {
                $files[$count] = ['type' => 'file', 'name' => $file, 'size' => $filesize];
            } else {
                $files[$count] = ['type' => 'not_viewable', 'name' => $file, 'size' => $filesize];
            }
        }
        $count++;
    }
    $allfiles = array_values($subdirs + $files);
    exit(json_encode(['type' => 'dir', 'content' => $allfiles]));
}
json_encode(['type' => 'error', 'content' => 'File or directory not found.']);
