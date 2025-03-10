<?php
require_once('includes/authorization.php');

if (!Authorization::baseUser()) {
    exit(json_encode(['type' => 'error', 'content' => 'Failed to authorize']));
}

require_once('includes/headers.php');
require_once('includes/functions.php');

header("Content-type: application/json");

$filepath = '..' . str_replace('/', DIRECTORY_SEPARATOR, $data['filepath']);;
$content  = $data['content'];

if (!file_exists($filepath)) {
    exit(json_encode(['type' => 'error', 'content' => 'File no longer exists']));
}
$currentcontent = file_get_contents($filepath);

if ($currentcontent === $content) {
    exit(json_encode(['type' => 'error', 'content' => 'No changes to save']));
}

if (empty($content) || file_put_contents($filepath, $content)) {
    exit(json_encode(['type' => 'success', 'content' => 'Successfully saved file', 'old_file_content' => $currentcontent]));
}

exit(json_encode(['type' => 'error', 'content' => 'Failed to save file']));


