<?php

namespace App\DTO;

use Spatie\LaravelData\Data;

class LoginDTO extends Data
{
    /**
     * @param  string  $email
     */
    public function __construct(
        public string $phone,
        public string $password
    ) {
    }

    /**
     * @param array{
     *  user_name:string,
     *  password:string
     * } $request
     */
    public static function fromRequest(array $request): LoginDTO
    {
        return new self(
            phone: $request['user_name'],
            password: $request['password']
        );
    }
}
