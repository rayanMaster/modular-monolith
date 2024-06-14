<?php

namespace App\Models;

use Database\Factories\WorkSiteResourceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkSiteResource extends Model
{
    use HasFactory;

    protected $table = 'work_site_resources';

    protected $guarded = [];

    protected static function newFactory(): WorkSiteResourceFactory
    {
        return WorkSiteResourceFactory::new();
    }
}
