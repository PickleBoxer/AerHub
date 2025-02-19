<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SiteResource\Pages;
use App\Filament\Resources\SiteResource\RelationManagers;
use App\Models\Site;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Notifications\Notification;
use Barryvdh\Debugbar\Facade as Debugbar;

class SiteResource extends Resource
{
    protected static ?string $model = Site::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    // Indicates if the resource is scoped to tenant
    protected static bool $isScopedToTenant = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('prestashop_url')
                    ->label('PrestaShop URL')
                    ->required()
                    ->url(),
                TextInput::make('prestashop_api_key')
                    ->label('API Key')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('prestashop_url')->label('PrestaShop URL'),
                TextColumn::make('endpoints')
                    ->label('Endpoints')
                    ->getStateUsing(function (Site $record) {
                        try {
                            $service = app(\App\Services\PrestaShopService::class);
                            $apiData = $service->fetchApiData($record->prestashop_url, $record->prestashop_api_key);

                            Debugbar::info($apiData);

                            if (!empty($apiData) && isset($apiData['api']) && is_array($apiData['api'])) {
                                // Filter out the "@attributes" element.
                                $endpoints = array_filter(
                                    $apiData['api'],
                                    function ($key) {
                                    return $key !== '@attributes';
                                },
                                    ARRAY_FILTER_USE_KEY
                                );
                                // Return a comma-separated list of endpoint names.
                                return implode(', ', array_keys($endpoints));
                            }
                        } catch (\Exception $e) {
                            return 'Error: ' . $e->getMessage();
                        }
                        return 'None';
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('sync')
                    ->label('Sync API')
                    ->action(function (Site $record) {
                        try {
                            $service = app(\App\Services\PrestaShopService::class);
                            $apiData = $service->fetchApiData($record->prestashop_url, $record->prestashop_api_key);

                            if (!empty($apiData) && isset($apiData['api']) && is_array($apiData['api'])) {
                                // Filter out the "@attributes" element.
                                $endpoints = array_filter($apiData['api'], function ($key) {
                                    return $key !== '@attributes';
                                }, ARRAY_FILTER_USE_KEY);

                                $endpointCount = count($endpoints);
                                Notification::make()
                                    ->title('API Sync Successful')
                                    ->body("Found {$endpointCount} available API endpoint" . ($endpointCount !== 1 ? 's' : '') . '.')
                                    ->success()
                                    ->send();
                            } else {
                                Notification::make()
                                    ->title('API Sync Failed')
                                    ->body('No API data retrieved or the response structure is unexpected.')
                                    ->danger()
                                    ->send();
                            }
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('API Sync Exception')
                                ->body('Error: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
                    ->color('primary'),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSites::route('/'),
            'create' => Pages\CreateSite::route('/create'),
            'edit' => Pages\EditSite::route('/{record}/edit'),
        ];
    }
}
