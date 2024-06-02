<?php

namespace App\Models;

use Database\Factories\WorkerFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Worker extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected static function newFactory(): WorkerFactory
    {
        return new WorkerFactory();
    }
}
