<?php
require_once('includes/authorization.php');

if (!Authorization::baseUser()) {
    exit(json_encode(['type' => 'error', 'content' => 'Failed to authorize']));
}

require_once('includes/headers.php');
require_once('includes/functions.php');

header('Content-type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['file'])  && $_FILES['file']['error'] === UPLOAD_ERR_OK) {

        $fileTmpPath = $_FILES['file']['tmp_name'];
        $fileName = basename($_FILES['file']['name']);
        $fileSize = $_FILES['file']['size'];
        $fileType = $_FILES['file']['type'];

        $destdir = '..' . str_replace('/', DIRECTORY_SEPARATOR, $_POST['filepath']);

        if (file_exists($destdir . DIRECTORY_SEPARATOR . $fileName)) {
            exit(json_encode(['type' => 'error', 'content' => 'File already exists']));
        }
        if (!file_exists($destdir)) {
            mkdir($destdir, 0777, true);
        }
        if (move_uploaded_file($fileTmpPath, $destdir . DIRECTORY_SEPARATOR . $fileName)) {
            exit(json_encode(['type' => 'success', 'content' => 'File uploaded successfully']));
        } else {
            exit(json_encode(['type' => 'error', 'content' => 'Failed to upload file']));
        }
    } else {
        exit(json_encode(['type' => 'error', 'content' => 'Could not read file']));
    }
}