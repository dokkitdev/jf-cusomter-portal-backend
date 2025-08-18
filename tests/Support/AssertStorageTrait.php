<?php

namespace App\Tests\Support;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use SplFileInfo;

trait AssertStorageTrait
{
    abstract public static function assertEmpty(mixed $actual, string $message = ''): void;

    protected function prepareInitialStorageState(string $fixturesDirPath, ?string $disk): void
    {
        $files = File::allFiles($this->getFixturePath($fixturesDirPath));

        foreach ($files as $file) {
            Storage::disk($disk)->put($file->getRelativePathname(), $file->getContents());
        }
    }

    protected function makeStorageFake(): void
    {
        foreach (array_keys(config('filesystems.disks')) as $disk) {
            Storage::fake($disk);
        }
    }

    protected function assertStorageState(string $expectedDir, ?string $disk = null): void
    {
        $fixturePath = $this->getFixturePath($expectedDir);
        $storagePath = Storage::disk($disk)->path('');

        $this->assertDirectoryEquals($fixturePath, $storagePath);
    }

    protected function assertStorageEmpty(?string $disk = null): void
    {
        $storageContent = [
            ...Storage::disk($disk)->allFiles(),
            ...Storage::disk($disk)->allDirectories(),
        ];

        $disk = $disk ?? 'default';

        $this->assertEmpty($storageContent, "The {$disk} storage disk is not empty");
    }

    protected function assertDirectoryEquals(string $expectedDir, string $actualDir): void
    {
        if ($this->forceExportMode) {
            if (!file_exists($expectedDir)) {
                mkdir_recursively($expectedDir);
            }

            shell_exec("cp -r {$actualDir}/* {$expectedDir}");
        }

        $expectedFiles = array_map(fn (SplFileInfo $file) => $file->getRelativePathname(), File::allFiles($expectedDir));
        $actualFiles = array_map(fn (SplFileInfo $file) => $file->getRelativePathname(), File::allFiles($actualDir));

        $this->assertSame($expectedFiles, $actualFiles, "{$expectedDir} not equal to {$actualDir}");

        foreach ($expectedFiles as $file) {
            $expectedFullPath = "{$expectedDir}/{$file}";
            $actualFullPath = "{$actualDir}/{$file}";

            $this->assertSame(file_get_contents($expectedFullPath), file_get_contents($actualFullPath));
        }
    }
}
