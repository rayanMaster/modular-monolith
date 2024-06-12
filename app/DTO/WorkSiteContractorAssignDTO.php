<?php

namespace App\DTO;

use App\Enums\ConfirmEnum;
use Spatie\LaravelData\Data;

class WorkSiteContractorAssignDTO extends Data
{
    public function __construct(
        public ?int $contractorId,
        public ?string $shouldRemove
    ) {
    }

    /**
     * @param array{
     *     contractor_id : int|null,
     *     should_remove : string|null
     * } $request
     */
    public static function fromRequest(array $request): WorkSiteContractorAssignDTO
    {
        return new self(
            contractorId: $request['contractor_id'] ?? null,
            shouldRemove: $request['should_remove'] ?? ConfirmEnum::NO->value
        );
    }
}
