<?php

namespace App\DTO;

use Spatie\LaravelData\Data;

/**
 *
 */
class AddressCreateDTO extends Data
{
    /**
     * @param int $city_id
     * @param string $street
     * @param string $state
     */
    public function __construct(
        public int    $city_id,
        public string $street,
        public string $state,
    )
    {
    }

    /**
     * @param array{
     *    city_id:int,
     *    street:string,
     *    state:string
     * } $request
     * @return self
     */
    public static function fromRequest(array $request): AddressCreateDTO
    {
        return new self(
            city_id: $request['city_id'],
            street: $request['street'],
            state: $request['state']
        );
    }

}
