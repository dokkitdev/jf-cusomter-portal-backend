<?php

namespace App\Repositories;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\File;

class LetterTemplateRepository
{
    protected Filesystem $storage;

    public function __construct()
    {
        $this->storage = Storage::disk('letter_templates');
    }

    public function exists(string $letterName): bool
    {
        return $this->storage->exists($letterName);
    }

    public function putTemplateContent(string $letterName, File $file): void
    {
        $this->storage->putStream($letterName, fopen($file->getPathname(), 'r'));
    }

    public function getTemplateContent(string $letterName): string
    {
        return $this->storage->get($letterName);
    }
}
