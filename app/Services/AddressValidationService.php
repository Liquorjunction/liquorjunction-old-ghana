<?php

namespace App\Services;

use App\Models\Country;
use App\Models\Region;
use App\Models\Area;
use Illuminate\Support\Facades\Validator;

class AddressValidationService
{
    /**
     *      * Validate incoming form payload for saving/updating Shipping or Billing address.
     *      *
     *      * @param array $input
     *      * @param bool $isBilling
     *      * @return array ['valid' => bool, 'message' => string, 'data' => array]
     *      */
    public static function validateAddressData(array $input, bool $isBilling = false): array
    {
        $name = $isBilling ? ($input['bill_name'] ?? ($input['name'] ?? null)) : ($input['name'] ?? null);
        $phone = $isBilling ? ($input['bill_phone'] ?? ($input['phone'] ?? null)) : ($input['phone'] ?? null);
        $phonecode = $isBilling ? ($input['bill_phonecode'] ?? ($input['phonecode'] ?? null)) : ($input['phonecode'] ?? null);
        $country_id = $isBilling ? ($input['bill_country_id'] ?? ($input['country_id'] ?? null)) : ($input['country_id'] ?? null);
        $region_id = $isBilling ? ($input['bill_region_id'] ?? ($input['region_id'] ?? null)) : ($input['region_id'] ?? null);
        $area_id = $isBilling ? ($input['bill_area_id'] ?? ($input['area_id'] ?? null)) : ($input['area_id'] ?? null);
        $city = $isBilling ? ($input['bill_city'] ?? ($input['city'] ?? null)) : ($input['city'] ?? null);
        $address = $isBilling ? ($input['bill_address'] ?? ($input['address'] ?? null)) : ($input['address'] ?? null);
        $zip_code = $isBilling ? ($input['bill_zip_code'] ?? ($input['zip_code'] ?? null)) : ($input['zip_code'] ?? null);
        $latitude = $isBilling ? ($input['bill_latitude'] ?? ($input['latitude'] ?? null)) : ($input['latitude'] ?? null);
        $longitude = $isBilling ? ($input['bill_longitude'] ?? ($input['longitude'] ?? null)) : ($input['longitude'] ?? null);

        $name = $name !== null ? trim((string)$name) : null;
        $phone = $phone !== null ? trim((string)$phone) : null;
        $phonecode = $phonecode !== null ? trim((string)$phonecode) : null;
        $city = $city !== null ? trim((string)$city) : null;
        $address = $address !== null ? trim((string)$address) : null;
        $zip_code = $zip_code !== null ? trim((string)$zip_code) : '';
        $prefix = $isBilling ? 'Billing' : 'Delivery';
        $validator = Validator::make([
            'name' => $name,
            'phone' => $phone,
            'phonecode' => $phonecode,
            'country_id' => $country_id,
            'region_id' => $region_id,
            'area_id' => $area_id,
            'city' => $city,
            'address' => $address,
            'zip_code' => $zip_code,
            'latitude' => $latitude,
            'longitude' => $longitude,
        ], [
            'name' => 'required|string|max:100',
            'phone' => 'required|string|min:6|max:20',
            'phonecode' => 'required|max:10',
            'country_id' => 'required|integer',
            'region_id' => 'required|integer',
            'area_id' => 'required|integer',
            'city' => 'required|string|max:100',
            'address' => 'required|string|max:255',
            'zip_code' => 'nullable|string|max:20',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return [
                'valid' => false,
                'message' => $validator->errors()->first()
            ];
        }

        // 1. Verify Country
        $country = Country::where('id', $country_id)->where('status', 1)->first();
        if (!$country) {
            return [
                'valid' => false,
                'message' => "$prefix country is invalid or not allowed."
            ];
        }

        // 2. Verify Region belongs to Country
        $region = Region::where('id', $region_id)->where('country_id', $country_id)->where('status', 1)->first();
        if (!$region) {
            return [
                'valid' => false,
                'message' => "$prefix region is invalid or does not belong to the selected country."
            ];
        }

        // 3. Verify Area belongs to Region
        $area = Area::where('id', $area_id)->where('region_id', $region_id)->where('status', 1)->first();
        if (!$area) {
            return [
                'valid' => false,
                'message' => "$prefix area does not belong to the chosen region."
            ];
        }

        // 4. Verify Coordinates
        $lat = floatval($latitude);
        $lng = floatval($longitude);

        $geoCheck = self::checkCoordinatesInAllowedServiceArea($country, $region, $area, $lat, $lng, $prefix);
        if (!$geoCheck['valid']) {
            return $geoCheck;
        }

        return [
            'valid' => true,
            'data' => [
                'name' => $name,
                'phone' => $phone,
                'phonecode' => $phonecode,
                'country_id' => $country_id,
                'region_id' => $region_id,
                'area_id' => $area_id,
                'city' => $city,
                'address' => $address,
                'zip_code' => $zip_code ?: '',
                'latitude' => $lat,
                'longitude' => $lng,
            ]
        ];
    }

    /**
     *      * Verify an existing UserAddress or UserBillAddress model instance for order placement.
     *      *
     *      * @param mixed $addressModel
     *      * @param string $label
     *      * @return array ['valid' => bool, 'message' => string]
     *      */
    public static function verifyAddressModel($addressModel, string $label = 'Delivery address'): array
    {
        if (!$addressModel) {
            return [
                'valid' => false,
                'message' => "$label is missing or could not be found."
            ];
        }

        if (intval($addressModel->status) !== 1) {
            return [
                'valid' => false,
                'message' => "$label is no longer active. Please select another address."
            ];
        }

        $lat = ($addressModel->latitude !== null && $addressModel->latitude !== '') ? floatval($addressModel->latitude) : null;
        $lng = ($addressModel->longitude !== null && $addressModel->longitude !== '') ? floatval($addressModel->longitude) : null;

        if ($lat === null || $lng === null || ($lat == 0.0 && $lng == 0.0)) {
            return [
                'valid' => false,
                'message' => "$label does not have a map location set. Please edit the address and pin your location on the map before placing your order."
            ];
        }

        $countryId = $addressModel->country_id;
        $regionId = $addressModel->region_id;
        $areaId = $addressModel->area_id;

        // Verify Country
        $country = $addressModel->country ?: Country::where('id', $countryId)->where('status', 1)->first();
        if (!$country || intval($country->status) !== 1) {
            return [
                'valid' => false,
                'message' => "$label has an invalid or inactive country."
            ];
        }

        // Verify Region
        $region = $addressModel->region ?: Region::where('id', $regionId)->where('country_id', $countryId)->where('status', 1)->first();
        if (!$region || intval($region->status) !== 1 || intval($region->country_id) !== intval($countryId)) {
            return [
                'valid' => false,
                'message' => "$label region is invalid or does not belong to the selected country."
            ];
        }

        // Verify Area
        $area = $addressModel->area ?: Area::where('id', $areaId)->where('region_id', $regionId)->where('status', 1)->first();
        if (!$area || intval($area->status) !== 1 || intval($area->region_id) !== intval($regionId)) {
            return [
                'valid' => false,
                'message' => "$label area is invalid or does not belong to the selected region."
            ];
        }

        // Check Coordinates against Allowed Service Locations
        return self::checkCoordinatesInAllowedServiceArea($country, $region, $area, $lat, $lng, $label);
    }

    /**
     *      * Completely dynamic coordinate check against allowed service locations.
     *      * Uses live reverse geocoding to dynamically verify that the coordinates match
     *      * the selected Country, Region, and Area entities without any hardcoded coordinates or city names.
     *      */
    public static function checkCoordinatesInAllowedServiceArea($country, $region, $area, float $lat, float $lng, string $label = 'Location'): array
    {
        if ($lat < -90.0 || $lat > 90.0 || $lng < -180.0 || $lng > 180.0 || ($lat == 0.0 && $lng == 0.0)) {
            return [
                'valid' => false,
                'message' => "$label has invalid geographic coordinates."
            ];
        }

        // Live reverse geocoding verification (timeout 3 seconds)
        $url = "https://nominatim.openstreetmap.org/reverse?format=json&lat={$lat}&lon={$lng}&zoom=14&addressdetails=1";
        $ctx = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => "User-Agent: LiquorJunctionDeliveryApp/2.1 (tech@liquorjunction.com)\r\nAccept-Language: en\r\n",
                'timeout' => 5
            ]
        ]);

        $resp = @file_get_contents($url, false, $ctx);
        if (!$resp) {
            // If external geocoder is unreachable/offline, fallback to valid basic check
            return ['valid' => true];
        }

        $data = json_decode($resp, true);
        $addr = $data['address'] ?? [];
        if (empty($addr)) {
            return [
                'valid' => false,
                'message' => "$label could not be verified on the map."
            ];
        }

        // 1. Dynamic Country Match
        $geoCountryCode = strtolower($addr['country_code'] ?? '');
        $geoCountryName = strtolower($addr['country'] ?? '');
        $dbCountryCode = strtolower($country->shortname ?? '');
        $dbCountryName = strtolower($country->name ?? '');

        $countryMatched = ($geoCountryCode && $dbCountryCode && $geoCountryCode === $dbCountryCode)
            || ($geoCountryName && $dbCountryName && (stripos($geoCountryName, $dbCountryName) !== false || stripos($dbCountryName, $geoCountryName) !== false));

        if (!$countryMatched) {
            $cName = $addr['country'] ?? 'another country';
            return [
                'valid' => false,
                'message' => "$label map coordinates point to {$cName}, but {$country->name} was selected."
            ];
        }

        // 2. Dynamic Region Match
        $geoState = strtolower($addr['state'] ?? ($addr['region'] ?? ($addr['province'] ?? '')));
        $dbRegionTitle = strtolower(trim($region->title ?? ''));

        if ($geoState && $dbRegionTitle) {
            if (stripos($geoState, $dbRegionTitle) === false && stripos($dbRegionTitle, $geoState) === false) {
                $sName = $addr['state'] ?? 'another region';
                return [
                    'valid' => false,
                    'message' => "$label map coordinates point to {$sName}, but {$region->title} was selected."
                ];
            }
        }

        // 3. Dynamic Area Match (Strict administrative entity matching)
        $rawFields = [
            $addr['city'] ?? '',
            $addr['town'] ?? '',
            $addr['village'] ?? '',
            $addr['suburb'] ?? '',
            $addr['neighbourhood'] ?? '',
            $addr['municipality'] ?? '',
            $addr['district'] ?? '',
            $addr['state_district'] ?? '',
            $addr['county'] ?? '',
        ];
        $dbAreaTitle = strtolower(trim($area->title ?? ''));
        $pattern = '/\b' . preg_quote($dbAreaTitle, '/') . '\b/i';
        $matchedArea = false;
        foreach ($rawFields as $field) {
            $field = trim($field);
            if (!$field) continue;
            // Overarching administrative divisions (e.g. Ajmer Division) are NOT delivery areas
            if (preg_match('/\bdivision\b/i', $field)) continue;
            // Road/Highway names are NOT delivery areas
            if (preg_match('/\b(road|highway|expressway|bypass|marg|path)\b/i', $field)) continue;
            if (preg_match($pattern, $field)) {
                $matchedArea = true;
                break;
            }
        }
        if (!$matchedArea) {
            $detectedLoc = $addr['village'] ?? ($addr['suburb'] ?? ($addr['town'] ?? ($addr['city'] ?? ($addr['county'] ?? ($addr['state_district'] ?? 'this location')))));
            $isBill = (stripos($label, 'billing') !== false);
            $areaType = $isBill ? 'billing area.' : 'delivery area.';
            return [
                'valid' => false,
                'message' => "$label map coordinates point to {$detectedLoc}, which is outside the {$area->title} {$areaType}"
            ];
        }
        return ['valid' => true];
    }
}
