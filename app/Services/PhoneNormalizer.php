<?php

namespace App\Services;

class PhoneNormalizer
{
    public function normalize(string $phone, string $countryCode = null): string
    {
        $digits = preg_replace('/\D+/', '', trim($phone));
        $country = $countryCode ?? config('polla.otp_default_country_code', '51');

        // Strip leading country code if present (e.g. 51992042725 → 992042725)
        if (strlen($digits) === strlen($country) + 9 && str_starts_with($digits, $country)) {
            $digits = substr($digits, strlen($country));
        }

        return $digits;
    }
}
