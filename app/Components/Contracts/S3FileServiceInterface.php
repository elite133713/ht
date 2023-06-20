<?php

namespace App\Components\Contracts;

use Aws\Result;

interface S3FileServiceInterface
{
    public function getFiles(string $path): array;

    public function getFileSize(string $path): int;

    public function getPartFile(string $file, int $partNumber): string;

    public function getObject(string $path): Result;
}
