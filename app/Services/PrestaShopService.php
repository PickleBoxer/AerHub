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
     * @return string Comma-separated list of available endpoints or error text.
     */
    public function fetchAvailableEndpoints(string $baseUrl, string $apiKey)
    {
        // Always use the API URL based on baseUrl.
        $url = rtrim($baseUrl, '/') . '/api';
        $requestOptions = ['url' => $url];

        try {
            $ps = new PrestaShopWebservice($baseUrl, $apiKey, false);
            $xml = $ps->get($requestOptions);

            // Directly iterate over the <api> children.
            $endpoints = [];
            foreach ($xml->api->children() as $endpoint => $details) {
                $endpoints[] = $endpoint;
            }
            return !empty($endpoints) ? implode(', ', $endpoints) : 'None';
        } catch (PrestaShopWebserviceException $e) {
            return 'Error: ' . $e->getMessage();
        }
    }

    /**
     * Fetch employees from the PrestaShop API.
     *
     * The API is assumed to return an XML document with the structure:
     * <prestashop xmlns:xlink="http://www.w3.org/1999/xlink">
     *   <employees>
     *     <employee>
     *       <id><![CDATA[]]></id>
     *       <id_lang><![CDATA[]]></id_lang>
     *       <last_passwd_gen><![CDATA[]]></last_passwd_gen>
     *       <stats_date_from><![CDATA[]]></stats_date_from>
     *       <stats_date_to><![CDATA[]]></stats_date_to>
     *       <stats_compare_from><![CDATA[]]></stats_compare_from>
     *       <stats_compare_to><![CDATA[]]></stats_compare_to>
     *       <passwd><![CDATA[]]></passwd>
     *       <lastname><![CDATA[]]></lastname>
     *       <firstname><![CDATA[]]></firstname>
     *       <email><![CDATA[]]></email>
     *       <active><![CDATA[]]></active>
     *       <id_profile><![CDATA[]]></id_profile>
     *       <bo_color><![CDATA[]]></bo_color>
     *       <default_tab><![CDATA[]]></default_tab>
     *       <bo_theme><![CDATA[]]></bo_theme>
     *       <bo_css><![CDATA[]]></bo_css>
     *       <bo_width><![CDATA[]]></bo_width>
     *       <bo_menu><![CDATA[]]></bo_menu>
     *       <stats_compare_option><![CDATA[]]></stats_compare_option>
     *       <preselect_date_range><![CDATA[]]></preselect_date_range>
     *       <id_last_order><![CDATA[]]></id_last_order>
     *       <id_last_customer_message><![CDATA[]]></id_last_customer_message>
     *       <id_last_customer><![CDATA[]]></id_last_customer>
     *       <reset_password_token><![CDATA[]]></reset_password_token>
     *       <reset_password_validity><![CDATA[]]></reset_password_validity>
     *       <has_enabled_gravatar><![CDATA[]]></has_enabled_gravatar>
     *     </employee>
     *   </employees>
     * </prestashop>
     *
     * @param string $baseUrl The base URL of the PrestaShop site.
     * @param string $apiKey  The PrestaShop API key.
     * @param array  $options Additional options for the API request.
     * @return array List of employees as associative arrays.
     *
     * @throws PrestaShopWebserviceException
     */
    public function fetchEmployees(string $baseUrl, string $apiKey, array $options = []): array
    {
        // Build the API URL for employees.
        $requestOptions = array_merge(['resource' => 'employees', 'display' => 'full'], $options);

        try {
            $ps = new PrestaShopWebservice($baseUrl, $apiKey, false);
            $xml = $ps->get($requestOptions);
            $json = json_encode($xml);
            $data = json_decode($json, true);

            // The returned XML always wraps employee in the 'employees' key.
            $employees = (array) ($data['employees']['employee'] ?? []);
            return $employees;
        } catch (PrestaShopWebserviceException $e) {
            throw $e;
        }
    }
}
