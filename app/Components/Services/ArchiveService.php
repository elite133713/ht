<?php

namespace App\Components\Services;

use App\Components\Contracts\ArchiveServiceInterface;
use App\Components\Contracts\S3FileServiceInterface;
use ZipArchive;
use function basename;

class ArchiveService implements ArchiveServiceInterface
{
    public function createArchive(string $chunk): string
    {
        $archivePath = storage_path('app/' . uniqid("archive_{$chunk}_", true) . '.zip');
        touch($archivePath);

        return $archivePath;
    }

    public function archive(string $path, string $name, string $content): void
    {
        $zip = new \ZipArchive;

        // Use ZipArchive::CREATE | ZipArchive::OVERWRITE if you want to create a new archive or overwrite an existing one.
        // Use ZipArchive::CREATE | ZipArchive::CHECKCONS if you want to create a new archive or append to an existing one.
        $openMode = file_exists($path) ? \ZipArchive::CHECKCONS : \ZipArchive::CREATE | \ZipArchive::OVERWRITE;

        if ($zip->open($path, $openMode) === true) {
            $zip->addFromString(basename($name), $content);
            $zip->close();
        } else {
            throw new \Exception("Could not open archive at path: $path");
        }
    }
}
