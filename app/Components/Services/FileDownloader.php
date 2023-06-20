<?php

namespace App\Components\Services;

use App\Components\Contracts\ArchiveServiceInterface;
use App\Components\Contracts\FileDownloaderInterface;
use App\Components\Contracts\S3FileServiceInterface;
use App\Jobs\DownloadFileJob;
use Illuminate\Support\Facades\Queue;
use RuntimeException;
use function config;
use function throw_unless;

class FileDownloader implements FileDownloaderInterface
{
    public function __construct(private S3FileServiceInterface $s3Service, private ArchiveServiceInterface $archiveService)
    {
    }

    public function downloadFiles(string $path): array
    {
        $files = $this->s3Service->getFiles($path);

        throw_unless($files, new RuntimeException("No files in $path"));

        $chunks = $this->divideIntoChunks($files);

        $archives = [];

        foreach ($chunks as $i => $chunk) {
            $archivePath = $this->archiveService->createArchive($i);

            foreach ($chunk as $fileData) {
                Queue::push(new DownloadFileJob($fileData['file'], $archivePath, $fileData['part']));
            }

            $archives[] = $archivePath;
        }

        return $archives;
    }

    private function divideIntoChunks(array $files): array
    {
        $chunks = [];
        $chunk = [];
        $totalSize = 0;
        $maxChunkSize = config('filesystems.max_archive_size') * 1024 * 1024;

        foreach ($files as $file) {
            $fileSize = $this->s3Service->getFileSize($file);
            $numParts = ceil($fileSize / $maxChunkSize);

            for ($i = 0; $i < $numParts; $i++) {
                $partSize = min($maxChunkSize, $fileSize - ($i * $maxChunkSize));

                if (($partSize + $totalSize) > $maxChunkSize) {
                    // This part will not fit in the current chunk, create a new chunk
                    $chunks[] = $chunk;
                    $chunk = [];
                    $totalSize = 0;
                }

                $chunk[] = ['file' => $file, 'part' => $i];
                $totalSize += $partSize;
            }

            // If after adding this file, the total size has reached the maximum, add the chunk and reset
            if ($totalSize >= $maxChunkSize) {
                $chunks[] = $chunk;
                $chunk = [];
                $totalSize = 0;
            }
        }

        if (!empty($chunk)) {
            $chunks[] = $chunk;
        }

        return $chunks;
    }

}
