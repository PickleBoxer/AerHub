<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PrestaShopService;
use App\Models\Employee;
use App\Models\Site;

class SyncEmployees extends Command
{
    protected $signature = 'employees:sync';
    protected $description = 'Sync employees from the PrestaShop API';

    public function handle()
    {
        // filepath: config/services.php
        // return [
            // ...existing code...
            // 'prestashop' => [
                // 'base_url' => env('PRESTASHOP_BASE_URL', 'https://default-url.com'),
                // 'api_key' => env('PRESTASHOP_API_KEY', ''),
            // ],
            // ...existing code...
        // ];
        // $baseUrl = config('services.prestashop.base_url'); // e.g., https://mystore.com/
        // $apiKey = config('services.prestashop.api_key');

        $site = Site::first();
        if (!$site) {
            $this->error('No site configuration found.');
            return;
        }

        $baseUrl = $site->prestashop_url;
        $apiKey = $site->prestashop_api_key;

        $prestaShopService = new PrestaShopService();

        try {
            $employees = $prestaShopService->fetchEmployees($baseUrl, $apiKey);
            foreach ($employees as $empData) {
                Employee::updateOrCreate(
                    ['employee_id' => $empData['id'] ?? null],
                    [
                        'id_lang' => $empData['id_lang'] ?? null,
                        'last_passwd_gen' => $empData['last_passwd_gen'] ?? null,
                        //'stats_date_from' => $empData['stats_date_from'] ?? null,
                        //'stats_date_to' => $empData['stats_date_to'] ?? null,
                        //'stats_compare_from' => $empData['stats_compare_from'] ?? null,
                        //'stats_compare_to' => $empData['stats_compare_to'] ?? null,
                        'passwd' => $empData['passwd'] ?? null,
                        'lastname' => $empData['lastname'] ?? null,
                        'firstname' => $empData['firstname'] ?? null,
                        'email' => $empData['email'] ?? null,
                        'active' => filter_var($empData['active'] ?? false, FILTER_VALIDATE_BOOLEAN),
                        'id_profile' => $empData['id_profile'] ?? null,
                        //'bo_color' => $empData['bo_color'] ?? null,
                        //'default_tab' => $empData['default_tab'] ?? null,
                        //'bo_theme' => $empData['bo_theme'] ?? null,
                        //'bo_css' => $empData['bo_css'] ?? null,
                        //'bo_width' => $empData['bo_width'] ?? null,
                        //'bo_menu' => filter_var($empData['bo_menu'] ?? false, FILTER_VALIDATE_BOOLEAN),
                        //'stats_compare_option' => $empData['stats_compare_option'] ?? null,
                        //'preselect_date_range' => $empData['preselect_date_range'] ?? null,
                        'id_last_order' => $empData['id_last_order'] ?? null,
                        'id_last_customer_message' => $empData['id_last_customer_message'] ?? null,
                        'id_last_customer' => $empData['id_last_customer'] ?? null,
                        //'reset_password_token' => $empData['reset_password_token'] ?? null,
                        //'reset_password_validity' => $empData['reset_password_validity'] ?? null,
                        //'has_enabled_gravatar' => filter_var($empData['has_enabled_gravatar'] ?? false, FILTER_VALIDATE_BOOLEAN),
                    ]
                );
            }
            $this->info('Employees have been synchronized successfully.');
        } catch (\Exception $e) {
            $this->error('Error syncing employees: ' . $e->getMessage());
        }
    }
}
