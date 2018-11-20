<?php

require_once 'Utils/Mimetype.class.php';
require_once 'Utils/FileStream.class.php';

$filename = isset($_GET['filename']) ?? null;
$defaultDirectories = ['tmp', 'pdf'];

$mimetype = new Mimetype();
$fileStream = new FileStream($mimetype, $defaultDirectories);

try {
    $fileStream->setFilepath($filename);
    $fileStream->streamFile();
} catch (Exception $e) {
    print $e->getMessage();
    exit();
}

unlink($filename);