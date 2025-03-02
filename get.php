<?php
require_once('includes/authorization.php');

if (!Authorization::baseUser()) {
    exit(json_encode(['type' => 'error', 'content' => 'Failed to authorize']));
}

require_once('includes/headers.php');

header("Content-type: application/json");

$filepath = '..' . str_replace('/', DIRECTORY_SEPARATOR, $data['filepath']);;

if (is_file($filepath)) {
    $fileextension = strtolower(pathinfo($filepath, PATHINFO_EXTENSION));
    switch ($fileextension) {
        case 'yml':
            exit (json_encode(['type' => 'file', 'extension' => 'yaml', 'content' => file_get_contents($filepath)]));
        case 'json':
            exit (json_encode(['type' => 'file', 'extension' => 'json', 'content' => file_get_contents($filepath)]));
        case 'js':
            exit (json_encode(['type' => 'file', 'extension' => 'javascript', 'content' => file_get_contents($filepath)]));
        case 'php':
            exit (json_encode(['type' => 'file', 'extension' => 'php', 'content' => file_get_contents($filepath)]));
        case 'py':
            exit (json_encode(['type' => 'file', 'extension' => 'python', 'content' => file_get_contents($filepath)]));
        case 'java':
            exit (json_encode(['type' => 'file', 'extension' => 'java', 'content' => file_get_contents($filepath)]));
        case 'css':
            exit(json_encode(['type' => 'file', 'extension' => 'css', 'content' => file_get_contents($filepath)]));
        case 'html':
            exit(json_encode(['type' => 'file', 'extension' => 'html', 'content' => file_get_contents($filepath)]));
        case 'txt' || 'toml' || 'pem' || 'key':
            exit (json_encode(['type' => 'file', 'extension' => 'text', 'content' => file_get_contents($filepath)]));
    }
    exit (json_encode(['type' => 'not_editable', 'extension' => $fileextension]));
}

if ($dir = scandir($filepath)) {
    $files = [];
    $subdirs = [];
    $count = 0;
    foreach ($dir as $file) {
        if ($file == '.' || $file == '..') {
            continue;
        }
        if (is_dir($filepath . DIRECTORY_SEPARATOR . $file)) {
            $subdirs[$count] = ['type' => 'dir', 'name' => $file];
        } else {
            $filesize  = ceil(filesize($filepath . DIRECTORY_SEPARATOR . $file) / 1024) . ' Kb';
            $files[$count] = ['type' => 'file', 'name' => $file, 'size' => $filesize];
        }
        $count++;
    }
    $allfiles = array_values($subdirs + $files);
    exit(json_encode(['type' => 'dir', 'content' => $allfiles]));
}
json_encode(['type' => 'error', 'content' => 'File or directory not found.']);

