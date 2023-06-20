<?php

namespace App\Jobs;

use App\Components\Contracts\ArchiveServiceInterface;
use App\Components\Contracts\S3FileServiceInterface;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\File;

class DownloadFileJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(private string $file, private string $archivePath, private int $partNumber)
    {
    }

    /**
     * Execute the job.
     * @throws Exception
     */
    public function handle(S3FileServiceInterface $s3FileService, ArchiveServiceInterface $archiveService): void
    {
        // Load it from S3
        $partFilePath = $s3FileService->getPartFile($this->file, $this->partNumber);
        $fileContent = file_get_contents($partFilePath);

        // Add the content to the archive
        $archiveService->archive($this->archivePath, "{$this->file}_part_{$this->partNumber}", $fileContent);

        // Delete the part file if it exists in the /tmp directory
        if (file_exists($partFilePath)) {
            File::delete($partFilePath);
        }
    }

    /**
     * @throws Exception
     */
    public function failed(Exception $e): void
    {
        // Remove failed file
        File::delete($this->archivePath);

        throw $e;
    }
}
