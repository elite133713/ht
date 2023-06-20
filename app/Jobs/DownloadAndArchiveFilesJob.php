<?php
namespace App\Jobs;

use App\Components\Contracts\FileDownloaderInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;

class DownloadAndArchiveFilesJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;

    public function __construct(private string $path)
    {

    }

    public function handle(FileDownloaderInterface $fileDownloader): void
    {
        $fileDownloader->downloadFiles($this->path);
    }
}

