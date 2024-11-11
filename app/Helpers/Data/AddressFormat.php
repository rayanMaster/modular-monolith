<?php

namespace App\Helpers\Data;

use App\Models\Address;

class AddressFormat
{
    /**
     * @param  Address|array  $address
     */
    public static function format(array $address): string
    {

        // Initialize an array to hold the address components
        $addressParts = [];

        // Check and add each component if it exists
        if (! empty($address['title'])) {
            $addressParts[] = $address['title'];
        }

        if (! empty($address['state'])) {
            $addressParts[] = $address['state'];
        }

        if (! empty($address['city'])) {
            $addressParts[] = $address['city'];
        }

        if (! empty($address['street'])) {
            $addressParts[] = $address['street'];
        }
        if (! empty($address['zipcode'])) {
            $addressParts[] = $address['zipcode'];
        }

        // Join the non-empty parts with a comma and a space
        return ! empty($addressParts) ? implode(', ', $addressParts) : 'No Address Available';
    }
}
