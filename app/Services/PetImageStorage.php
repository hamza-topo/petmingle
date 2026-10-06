<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

final class PetImageStorage
{
    public const DISK = 'public';

    public const DIRECTORY = 'pets';

    public function store(UploadedFile $file): string
    {
        $extension = match ($file->getMimeType()) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            default => throw new RuntimeException(
                'Unsupported pet image MIME type.'
            ),
        };

        $filename = Str::uuid()->toString().'.'.$extension;

        $path = $file->storePubliclyAs(
            self::DIRECTORY,
            $filename,
            self::DISK
        );

        if ($path === false) {
            throw new RuntimeException('Pet image upload failed.');
        }

        return $path;
    }

    public function delete(array $paths): void
    {
        $managedPaths = array_values(array_filter(
            array_unique($paths),
            fn ($path) => is_string($path) && $this->isManagedPath($path)
        ));

        if ($managedPaths === []) {
            return;
        }

        Storage::disk(self::DISK)->delete($managedPaths);
    }

    private function isManagedPath(string $path): bool
    {
        return str_starts_with($path, self::DIRECTORY.'/')
            || str_starts_with($path, 'uploads/');
    }
}
