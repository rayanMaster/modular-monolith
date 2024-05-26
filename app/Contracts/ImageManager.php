<?php

namespace App\Contracts;

use Illuminate\Support\Facades\File;
use Intervention\Image\Drivers\Gd\Driver;

class ImageManager implements IFileManager
{
    public function upload($files)
    {
        $path = lcfirst('WorkSite');
        $name = $name ?? now()->format('YmdHisu');

        if (! File::exists(public_path('storage/'.$path))) {
            File::makeDirectory(public_path('storage/'.$path));
        }
        $fullPath = public_path('storage/'.$path).'/'.$name.'.webp';
        $relativePath = $path.'/'.$name.'.webp';

        // create new manager instance with desired driver
        $manager = new \Intervention\Image\ImageManager(new Driver());

        // read image from filesystem
        $image = $manager->read($files)->save($fullPath);
    }
}
