<?php
require_once('includes/authorization.php');

if (!Authorization::baseUser()) {
exit(json_encode(['type' => 'error', 'content' => 'Failed to authorize']));
}

require_once('includes/headers.php');

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
        $filepath = '..' . str_replace('/', DIRECTORY_SEPARATOR, $filepath);
        if (is_file($filepath)) {
            $zip->addFile($filepath, basename($filepath));
        }
        if (is_dir($filepath)) {
            zipFolder($zip, $filepath);
        }
    }
    $zip->close();
    header('Content-Type: application/zip');
    header('File-Name: download.zip');
    readfile('download.zip');
    unlink('download.zip');
    exit;
}

function zipFolder(ZipArchive $zip, String $filepath, String $zippath = ''): void {
    $files = scandir($filepath);

    //keep the root of directory in the file structure of the zip
    if ($zippath === '') {
        $dirlist = explode(DIRECTORY_SEPARATOR, $filepath);
        $zippath = $dirlist[count($dirlist) - 2] . DIRECTORY_SEPARATOR;
    }

    foreach ($files as $file) {
        if ($file === '.' || $file === '..') {
            continue;
        }
        if (is_dir ($filepath . $file)) {
            zipFolder($zip, $filepath . basename($file) . DIRECTORY_SEPARATOR, $zippath . $file . DIRECTORY_SEPARATOR);
        } else {
            $zip->addFile($filepath . $file, $zippath . basename($file));
        }
    }
}