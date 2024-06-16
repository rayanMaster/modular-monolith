<?php

namespace App\DTO;

use App\Enums\StatusEnum;

class WorkSiteCategoryCreateDTO extends \Spatie\LaravelData\Data
{
    public function __construct(
        public string $title,
        public ?int $status
    ) {
    }

    /**
     * @param array{
     * title:string,
     * status:int|null,
     * } $request
     */
    public static function fromRequest(array $request): WorkSiteCategoryCreateDTO
    {
        return new self(
            title: $request['title'],
            status: $request['status'] ?? StatusEnum::Active->value,
        );
    }
}
