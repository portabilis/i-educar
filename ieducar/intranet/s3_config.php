<?php
// Bucket Name
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/bootstrap.php';

$bucket= $GLOBALS['coreExt']['Config']->app->aws->bucketname;
$directory=$GLOBALS['coreExt']['Config']->app->database->dbname.'/';
$key = $GLOBALS['coreExt']['Config']->app->aws->awsacesskey;
$secretKey = $GLOBALS['coreExt']['Config']->app->aws->awssecretkey;

if (!class_exists('S3'))
    require_once 'S3.php';

//instantiate the class
$s3 = new S3($key, $secretKey);

$s3->putBucket($bucket, S3::ACL_PUBLIC_READ);
?>