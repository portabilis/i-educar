<?php
// Bucket Name
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/bootstrap.php';
$bucket="apps.ieducar.images.".$GLOBALS['coreExt']['Config']->app->database->dbname;
if (!class_exists('S3'))
	require_once 'S3.php';
/*
//AWS access info
if (!defined('awsAccessKey')) define('awsAccessKey', '***REMOVED***');
if (!defined('awsSecretKey')) define('awsSecretKey', '***REMOVED***');
*/
	
//instantiate the class
$s3 = new S3('***REMOVED***', '***REMOVED***');

$s3->putBucket($bucket, S3::ACL_PUBLIC_READ);
?>