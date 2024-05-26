<?php

namespace App\Models;

use Database\Factories\WorkSiteCategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkSiteCategory extends Model
{
    use HasFactory,SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */

    protected $guarded = [];

    protected static function newFactory(): WorkSiteCategoryFactory
    {
        return WorkSiteCategoryFactory::new();
    }
}
