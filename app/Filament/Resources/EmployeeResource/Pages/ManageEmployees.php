<?php

namespace App\Filament\Resources\EmployeeResource\Pages;

use App\Filament\Resources\EmployeeResource;
use Filament\Resources\Pages\ManageRecords;

class ManageEmployees extends ManageRecords
{
    protected static string $resource = EmployeeResource::class;
}
