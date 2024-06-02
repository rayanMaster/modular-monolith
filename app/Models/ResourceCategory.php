<?php

namespace App\Models;

use Database\Factories\ResourceCategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ResourceCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected static function newFactory(): ResourceCategoryFactory
    {
        return ResourceCategoryFactory::new();
    }
}
