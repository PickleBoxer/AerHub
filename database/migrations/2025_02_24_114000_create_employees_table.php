<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('employee_id')->nullable()->unique(); // PrestaShop employee id
            $table->unsignedInteger('id_lang')->nullable();
            $table->string('last_passwd_gen')->nullable();
            //$table->date('stats_date_from')->nullable();
            //$table->date('stats_date_to')->nullable();
            //$table->date('stats_compare_from')->nullable();
            //$table->date('stats_compare_to')->nullable();
            $table->string('passwd', 255);
            $table->string('lastname', 255);
            $table->string('firstname', 255);
            $table->string('email', 255);
            $table->boolean('active')->default(0);
            $table->unsignedInteger('id_profile')->nullable();
            //$table->string('bo_color', 32)->nullable();
            //$table->unsignedInteger('default_tab')->nullable();
            //$table->string('bo_theme', 32)->nullable();
            //$table->string('bo_css', 64)->nullable();
            //$table->unsignedInteger('bo_width')->nullable();
            //$table->boolean('bo_menu')->default(0);
            //$table->unsignedInteger('stats_compare_option')->nullable();
            //$table->string('preselect_date_range', 32)->nullable();
            $table->unsignedInteger('id_last_order')->nullable();
            $table->unsignedInteger('id_last_customer_message')->nullable();
            $table->unsignedInteger('id_last_customer')->nullable();
            //$table->string('reset_password_token', 40)->nullable();
            //$table->date('reset_password_validity')->nullable();
            //$table->boolean('has_enabled_gravatar')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
