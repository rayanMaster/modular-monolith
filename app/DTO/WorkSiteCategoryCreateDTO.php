<?php

namespace App\DTO;

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
     * status:int,
     * } $request
     */
    public static function fromRequest(array $request): WorkSiteCategoryCreateDTO
    {
        return new self(
            title: $request['title'],
            status: $request['status'] ?? 1,
        );
    }
}
