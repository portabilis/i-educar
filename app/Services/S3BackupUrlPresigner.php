<?php

namespace App\Services;

use Aws\Credentials\Credentials;
use Aws\S3\S3Client;
use Exception;
use Storage;

class S3BackupUrlPresigner
{
    private $url;

    public function __construct(string $url)
    {
        $this->url = $url;
    }

    public function getPresignedUrl() : string
    {
        $s3Client = $this->getClient();

        $command = $s3Client->getCommand('GetObject', [
            'Bucket' => $this->getBucketFromUrl(),
            'Key' => $this->getKeyFromUrl(),
        ]);

        $request = $s3Client->createPresignedRequest($command, '+10 minutes');

        return (string) $request->getURI();
    }

    private function getClient() : S3Client
    {
        $clientParameters = [
            'version' => 'latest',
            'region'  => $this->getRegionFromUrl(),
            'http'    => [
                'connect_timeout' => 5
            ],
        ];

        if (config('filesystems.disks.s3.key') && config('filesystems.disks.s3.secret')) {
            $clientParameters['credentials'] = new Credentials(config('filesystems.disks.s3.key'), config('filesystems.disks.s3.secret'));
        }

        return new S3Client($clientParameters);
    }

    private function getBucketFromUrl() : string
    {
        return explode('/', $this->url)[3];
    }

    private function getKeyFromUrl() : string
    {
        return implode('/', array_slice(explode('/', $this->url), 4));
    }

    private function getRegionFromUrl() : string
    {
        return substr(explode('.', explode('//', $this->url)[1])[0], 3);
    }
}
