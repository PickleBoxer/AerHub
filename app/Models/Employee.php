<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        'employee_id',
        'id_lang',
        'last_passwd_gen',
        'stats_date_from',
        'stats_date_to',
        'stats_compare_from',
        'stats_compare_to',
        'passwd',
        'lastname',
        'firstname',
        'email',
        'active',
        'id_profile',
        'bo_color',
        'default_tab',
        'bo_theme',
        'bo_css',
        'bo_width',
        'bo_menu',
        'stats_compare_option',
        'preselect_date_range',
        'id_last_order',
        'id_last_customer_message',
        'id_last_customer',
        'reset_password_token',
        'reset_password_validity',
        'has_enabled_gravatar',
    ];
}
