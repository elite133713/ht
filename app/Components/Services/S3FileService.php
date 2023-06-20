<?php

namespace App\Components\Services;

use App\Components\Contracts\S3FileServiceInterface;
use Aws\Result;
use Aws\S3\S3Client;
use function config;

class S3FileService implements S3FileServiceInterface
{
    private S3Client $s3Client;

    public function __construct()
    {
        $this->s3Client = new S3Client(
            [
                'region' => config('services.aws.region'),
                'version' => config('services.aws.version'),
                'credentials' => config('services.aws.credentials'),
                'endpoint' => config('services.aws.endpoint'),
                'use_path_style_endpoint' => true,
            ]
        );
    }

    public function getFiles(string $path): array
    {
        $result = $this->s3Client->listObjectsV2([
            'Bucket' => config('filesystems.disks.s3.bucket'),
            'Prefix' => $path,
        ]);

        $files = [];
        if (isset($result['Contents'])) {
            foreach ($result['Contents'] as $object) {
                $files[] = $object['Key'];
            }
        }

        return $files;
    }

    public function getFileSize(string $path): int
    {
        $result = $this->s3Client->headObject([
            'Bucket' => config('filesystems.disks.s3.bucket'),
            'Key' => $path,
        ]);

        return $result['ContentLength'];
    }

    public function getObject(string $path): Result
    {
        return $this->s3Client->getObject([
            'Bucket' => config('filesystems.disks.s3.bucket'),
            'Key' => $path,
        ]);
    }

    public function getPartFile(string $file, int $partNumber): string
    {
        $size = $this->getFileSize($file);
        $partSize = config('filesystems.max_archive_size') * 1024 * 1024;

        $start = $partNumber * $partSize;
        $end = ($partNumber + 1) * $partSize - 1;

        if ($end >= $size) {
            $end = $size - 1;
        }

        // Using the AWS S3 client
        $result = $this->s3Client->getObject([
            'Bucket' => config('filesystems.disks.s3.bucket'),
            'Key'    => $file,
            'Range'  => "bytes=$start-$end",
        ]);

        // You may want to save the part as a temporary file and return its path
        $partFilePath = "/tmp/{$file}_part_{$partNumber}";

        // Extract the directory part from the file path
        $dir = dirname($partFilePath);

        // Check if the directory exists and if not, create it
        if (!is_dir($dir)) {
            if (!mkdir($dir, 0777, true) && !is_dir($dir)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $dir));
            }
        }

        file_put_contents($partFilePath, $result['Body']);

        return $partFilePath;
    }
}
