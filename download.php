<?php
require_once('includes/authorization.php');

if (!Authorization::baseUser()) {
    exit(json_encode(['type' => 'error', 'content' => 'Failed to authorize']));
}

require_once('includes/headers.php');
require_once('includes/functions.php');

if (count($data['filepaths']) == 1) {
    $filepath = '..' . str_replace('/', DIRECTORY_SEPARATOR, $data['filepaths'][0]);

    if (is_file($filepath)) {
        header('Content-Type: application/octet-stream');
        header('File-Name: ' . basename($filepath));
        readfile($filepath);
        exit;
    }
    if (is_dir($filepath)) {
        $zip = new ZipArchive();
        if (!$zip->open('download.zip', ZipArchive::CREATE)) {
            exit(json_encode(['type' => 'error', 'content' => 'Failed to download']));
        }
        zipFolder($zip, $filepath);
        $zip->close();
        header('Content-Type: application/zip');
        header('File-Name: download.zip');
        readfile('download.zip');
        unlink('download.zip');
        exit;
    }
    exit(json_encode(['type' => 'error', 'content' => 'File no longer exists']));
} else {
    $zip = new ZipArchive();
    if (!$zip->open('download.zip', ZipArchive::CREATE)) {
        exit(json_encode(['type' => 'error', 'content' => 'Failed to download']));
    }
    foreach ($data['filepaths'] as $filepath) {
        $path =  '..' . str_replace('/', DIRECTORY_SEPARATOR, $filepath);
        if (is_file($path)) {
            $zip->addFile($path, basename($path));
        }
        if (is_dir($path)) {
            zipFolder($zip, $path);
        }
    }
    $zip->close();
    header('Content-Type: application/zip');
    header('File-Name: download.zip');
    readfile('download.zip');
    unlink('download.zip');
    exit;
}