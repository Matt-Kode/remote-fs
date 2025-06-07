<?php
function zipFolder(ZipArchive $zip, String $filepath, String $zippath = ''): void {

    if ($zippath === '') {
        //keep the root of directory in the file structure of the zip
        $dirlist = explode(DIRECTORY_SEPARATOR, $filepath);
        $zippath = $dirlist[count($dirlist) - 1];
    }

    $files = scandir($filepath);

    foreach ($files as $file) {
        if ($file === '.' || $file === '..') {
            continue;
        }
        if (is_dir ($filepath . DIRECTORY_SEPARATOR . $file)) {
            zipFolder($zip, $filepath . DIRECTORY_SEPARATOR . basename($file), $zippath . DIRECTORY_SEPARATOR . $file);
        } else {
            $zip->addFile($filepath . DIRECTORY_SEPARATOR . $file, $zippath . DIRECTORY_SEPARATOR . basename($file));
        }
    }
}

function validateFilename($filename) {
    if (trim($filename) === '') {
        return false;
    }

    if (preg_match('/[<>:"\/\\\|\?\*]/', $filename)) {
        return false;
    }

    if (strpos($filename, '..') !== false) {
        return false;
    }

    $reserved = ['CON', 'PRN', 'AUX', 'NUL',
        'COM1','COM2','COM3','COM4','COM5','COM6','COM7','COM8','COM9',
        'LPT1','LPT2','LPT3','LPT4','LPT5','LPT6','LPT7','LPT8','LPT9'];

    $nameWithoutExtension = strtoupper(pathinfo($filename, PATHINFO_FILENAME));
    if (in_array($nameWithoutExtension, $reserved)) {
        return false;
    }

    if (strlen($filename) > 255) {
        return false;
    }

    return true;
}

function getLastFolder(String $currentpath) : String {
    $currentpatharray = array_filter(explode(DIRECTORY_SEPARATOR, $currentpath));
    $counter = 1;
    $newfilepath = '..';
    while ($counter < count($currentpatharray) - 2) {
        $newfilepath .= DIRECTORY_SEPARATOR . $currentpatharray[$counter];
        $counter++;
    }
    return $newfilepath;
}

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

function copyDirectory(String $source, String $destination, bool $start = true) : bool {
    //keep the root of the moved file in the structure
    if ($start) {
        $dirinsource = explode(DIRECTORY_SEPARATOR, $source);
        $rootindest = $dirinsource[count($dirinsource) - 1];
        $destination .= DIRECTORY_SEPARATOR . $rootindest;
    }

    if (!is_dir($destination)) {
        mkdir($destination, 0777, true);
    }

    foreach (scandir($source) as $file) {
        if ($file == '.' || $file == '..') {
            continue;
        }

        $sourcePath = $source . DIRECTORY_SEPARATOR . $file;
        $destinationPath = $destination . DIRECTORY_SEPARATOR . $file;

        if (is_dir($sourcePath)) {
            copyDirectory($sourcePath, $destinationPath, false);
        } else {
            copy($sourcePath, $destinationPath);
        }
    }
    return true;
}

function moveFile(String $source, String $destination) : bool {
    if (!file_exists($source) || !file_exists($destination)) {
        return false;
    }
    if (is_file($source)) {
        copy($source, $destination . DIRECTORY_SEPARATOR . basename($source));
        unlink($source);
        return true;
    }
    if (copyDirectory($source, $destination)) {
        if (recursiveDelete($source)) {
            return true;
        }
    }
    return false;
}

function validatePath($filepath) : bool {
    if (str_starts_with($filepath, '..' . DIRECTORY_SEPARATOR  . 'remote')) {
        return false;
    }
    return true;
}

function editableFile($filepath) {
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimetype = finfo_file($finfo, $filepath);
    finfo_close($finfo);
    if (strpos($mimetype, 'text') !== false || $mimetype === 'application/x-empty') {
        return true;
    }
    return false;
}