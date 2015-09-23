<?php

$config = $GLOBALS['coreExt']['Config']->app;

$bucket = $config->aws->bucketname;
$directory = $config->database->dbname.'/';
$key = $config->aws->awsacesskey;
$secretKey = $config->aws->awssecretkey;

if (empty($bucket) || empty($key) || empty($secretKey)) {
  throw new Exception("Defina as configs AWS.");
}

if (!class_exists('S3')) {
  require_once 'S3.php';
}

$s3 = new S3($key, $secretKey);
$s3->putBucket($bucket, S3::ACL_PUBLIC_READ);

?>