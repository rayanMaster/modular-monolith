<?php

namespace App\Models;

use Database\Factories\ResourceCategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResourceCategory extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function newFactory(): ResourceCategoryFactory
    {
        return ResourceCategoryFactory::new();
    }

}
