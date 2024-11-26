<?php

namespace App\Models;

use Database\Factories\MediaFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\URL;

/**
 * @property int $id
 * @property string $model_type
 * @property int $model_id
 * @property string $name
 * @property string $file_name
 * @property string $fullName
 */
class Media extends Model
{
    use HasFactory;

    protected $guarded = [];

    public static function factory(): MediaFactory
    {
        return MediaFactory::new();
    }

    /**
     * @return MorphTo<Model,Media>
     */
    public function model(): MorphTo
    {
        return $this->morphTo();

    }

    public function fullPath(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, mixed $attributes) {
                if (is_array($attributes) && isset($attributes['file_name'])) {
                    // TODO manage url according to driver ex: local -> storage, S3 -> get url from storage
                    return Url::to('storage/'.$attributes['file_name']);
                }

                return '';
            }
        );
    }
}
