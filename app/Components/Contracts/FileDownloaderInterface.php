<?php

namespace App\Components\Contracts;

interface FileDownloaderInterface
{
    public function downloadFiles(string $path): array;
}
