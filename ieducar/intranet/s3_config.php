<?php

$bucket = config('legacy.app.aws.bucketname');
$directory = config('legacy.app.database.dbname') . '/';
$key = config('legacy.app.aws.awsacesskey');
$secretKey = config('legacy.app.aws.awssecretkey');

$s3 = new S3($key, $secretKey);

$s3->putBucket($bucket, S3::ACL_PUBLIC_READ);
