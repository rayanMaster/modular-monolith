<?php

namespace App\Contracts;

interface IFileManager
{
    public function upload(mixed $files): void;
}
