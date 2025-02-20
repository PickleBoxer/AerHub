<?php

namespace App\Services;

use App\Services\PrestaShopWebservice;
use App\Services\PrestaShopWebserviceException;

class PrestaShopService
{
    /**
     * Fetch API data from the PrestaShop /api endpoint using the PrestaShopWebservice library.
     *
     * @param string $baseUrl The base URL of the PrestaShop site.
     * @param string $apiKey  The PrestaShop API key.
     * @return array The API response data as an associative array, or an empty array on error.
     */
    public function fetchApiData(string $baseUrl, string $apiKey): array
    {
        try {
            // Instantiate the PrestaShopWebservice with debug set to false.
            $ps = new PrestaShopWebservice($baseUrl, $apiKey, false);

            // Use the 'url' parameter to request the base /api endpoint.
            $xml = $ps->get(['url' => rtrim($baseUrl, '/') . '/api']);

            // Convert the SimpleXMLElement to JSON and then to an array.
            $json = json_encode($xml);
            $data = json_decode($json, true);
            return $data;
        } catch (PrestaShopWebserviceException $e) {
            // Optionally log error: \Log::error($e->getMessage());
            return [];
        }
    }
}
