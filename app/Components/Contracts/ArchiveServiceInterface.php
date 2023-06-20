<?php

namespace App\Components\Contracts;

interface ArchiveServiceInterface
{
    public function createArchive(string $chunk): string;

    public function archive(string $path, string $name, string $content): void;
}
