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
// use Barryvdh\Debugbar\Facade as Debugbar;

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
                        // dd(app(\App\Services\PrestaShopService::class)
                        //     ->fetchEmployees (
                        //         $record->prestashop_url,
                        //         $record->prestashop_api_key
                        //     ));
                        return app(\App\Services\PrestaShopService::class)
                            ->fetchAvailableEndpoints (
                                $record->prestashop_url,
                                $record->prestashop_api_key
                            );
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('sync')
                    ->label('Sync API')
                    ->action(function (Site $record) {
                        $result = app(\App\Services\PrestaShopService::class)
                            ->fetchAvailableEndpoints ($record->prestashop_url, $record->prestashop_api_key);

                        if (str_starts_with($result, 'Error:') || $result === 'None' || empty(trim($result))) {
                            Notification::make()
                                ->title('API Sync Failed')
                                ->body($result)
                                ->danger()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('API Sync Successful')
                                ->body("Available API endpoints: " . $result)
                                ->success()
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
