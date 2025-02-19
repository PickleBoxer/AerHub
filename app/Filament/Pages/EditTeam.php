<?php

namespace App\Filament\Pages;

use Filament\Facades\Filament;
use Filament\Pages\Tenancy\EditTenantProfile;

class EditTeam extends EditTenantProfile
{
    protected static string $view = 'filament.pages.edit-team';

    protected static ?int $navigationSort = 2;

    protected static ?string $slug = 'team-settings';

    public static function getLabel(): string
    {
        return __('Team Settings');
    }

    protected function getViewData(): array
    {
        return [
            'team' => Filament::getTenant(),
        ];
    }
}
