<?php

namespace App\Services;

use App\Services\PrestaShopWebservice;
use App\Services\PrestaShopWebserviceException;

class PrestaShopService
{
    /**
     * Fetch available API endpoints from the PrestaShop /api endpoint using the PrestaShopWebservice library.
     *
     * @param string $baseUrl The base URL of the PrestaShop site.
     * @param string $apiKey  The PrestaShop API key.
     * @param array  $options Additional options for the API request.
     * @return string Comma‐separated list of available endpoints or error text.
     */
    public function fetchAvailableEndpoints (string $baseUrl, string $apiKey, array $options = [])
    {
        // Always use the API URL based on baseUrl.
        $url = rtrim($baseUrl, '/') . '/api';
        $requestOptions = array_merge(['url' => $url], $options);

        try {
            $ps = new PrestaShopWebservice($baseUrl, $apiKey, false);
            $xml = $ps->get($requestOptions);
            $json = json_encode($xml);
            $data = json_decode($json, true);

            if (!empty($data) && isset($data['api']) && is_array($data['api'])) {
                // Filter out the "@attributes" entry.
                $endpoints = array_filter($data['api'], function ($key) {
                    return $key !== '@attributes';
                }, ARRAY_FILTER_USE_KEY);
                return implode(', ', array_keys($endpoints));
            }
            return 'None';
        } catch (PrestaShopWebserviceException $e) {
            return 'Error: ' . $e->getMessage();
        }
    }
}
