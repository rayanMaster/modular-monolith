<?php

namespace App\DTO;

use Illuminate\Http\Testing\File as TestingFile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\App;

class FileDTO
{
    public function __construct(
        public \Illuminate\Http\File|\Illuminate\Http\UploadedFile|TestingFile|null $file // Adjust namespace according to your application
    ) {
    }

    public static function fromRequest(array $request): FileDTO
    {
        if (App::isProduction()) {
            // Validate if it's an UploadedFile
            if (! ($request['image'] instanceof UploadedFile)) {
                throw new \InvalidArgumentException('Invalid file format');
            }
        }

        // Validation for file can be added here if needed
        return new self(
            $request['image'] ?? null
        );
    }
}
