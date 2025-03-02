<?php
require_once('includes/authorization.php');

if (!Authorization::baseUser()) {
    exit(json_encode(['type' => 'error', 'content' => 'Failed to authorize']));
}

require_once('includes/headers.php');

header('Content-type: application/json');

$filepath = '..' . str_replace('/', DIRECTORY_SEPARATOR, $data['filepath']);
$newfilename = $data['newfilename'];


if (rename($filepath, getLastFolder($filepath) . $newfilename)) {
    exit(json_encode(['type' => 'success', 'content' => 'File successfully renamed']));
} else {
    exit(json_encode(['type' => 'error', 'content' => 'Failed to rename file']));
}

function getLastFolder(String $currentpath) : String {
    $currentpatharray = array_filter(explode(DIRECTORY_SEPARATOR, $currentpath));
    $counter = 1;
    $newfilepath = '..' . DIRECTORY_SEPARATOR;
    while ($counter < count($currentpatharray) - 1) {
        $newfilepath .= $currentpatharray[$counter]. DIRECTORY_SEPARATOR;
        $counter++;
    }
    return $newfilepath;
}