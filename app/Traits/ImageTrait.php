<?php

namespace App\Traits;

use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait ImageTrait
{
    public const DISK = 'public';

    public const DIRECTORY = '/public';

    public UploadedFile $file;

    public string $name = '';

    public function setFile(UploadedFile $file): self
    {
        $this->file = $file;

        return $this;
    }

    public function setName(string $name = '')
    {
        $this->name = !empty($name) ? $name : Str::random(25);

        return $this;
    }

    public function upload(string $folder = self::DIRECTORY, string $disk = self::DISK, string $filename = null): mixed
    {
        $filePath = 'uploads/' . $this->file->getClientOriginalName();
        if( Storage::disk($disk)->put($filePath, file_get_contents($this->file->getRealPath()))){
            return $filePath;
        }

        return false;
    }

    public function uploadAll(array $uploadedFiles = [])
    {
        if (!empty($uploadedFiles))
            return array_map(function ($uploadedFile) {
                $this->setFile($uploadedFile);
                return $this->upload($uploadedFile, self::DISK, self::DIRECTORY);
            }, $uploadedFiles);
    }
}
