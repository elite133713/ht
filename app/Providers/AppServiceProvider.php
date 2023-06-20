<?php

namespace App\Providers;

use App\Components\Contracts\ArchiveServiceInterface;
use App\Components\Contracts\FileDownloaderInterface;
use App\Components\Contracts\S3FileServiceInterface;
use App\Components\Services\ArchiveService;
use App\Components\Services\FileDownloader;
use App\Components\Services\S3FileService;
use Aws\S3\S3Client;
use Illuminate\Support\ServiceProvider;
use function config;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ArchiveServiceInterface::class, ArchiveService::class);
        $this->app->bind(FileDownloaderInterface::class, FileDownloader::class);
        $this->app->bind(S3FileServiceInterface::class, S3FileService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
