<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';


    protected static array $statuses = [
        'in stock' => 'in stock',
        'sold out' => 'sold out',
        'coming soon' => 'coming soon',
    ];


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->required()->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('price')->required(),
                /*Forms\Components\Select::make('status')
                ->options([
                'in stock' => 'in stock',
                'sold out' => 'sold out',
                'coming soon' => 'coming soon',
                ]),*/

                Forms\Components\Radio::make('status')
                    ->options(self::$statuses),

                Forms\Components\Select::make('category_id')
                    ->relationship('category', 'name'),
                Forms\Components\Select::make('tags')
                    ->relationship('tags', 'name')
                    ->multiple()
                ,
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //Tables\Columns\TextColumn::make('name')->rules(['required', 'min:3']),
                Tables\Columns\TextInputColumn::make('name')->rules(['required', 'min:3']),
                Tables\Columns\TextColumn::make('price')->sortable()
                    ->money('usd')
                    ->getStateUsing(function (Product $record): float {
                        return $record->price / 100;
                    }),
                Tables\Columns\ToggleColumn::make('is_active'),
                /*Tables\Columns\TextColumn::make('status')->badge()
                ->color(fn(string $state): string => match ($state) {
                'in stock' => 'primary',
                'sold out' => 'danger',
                'coming soon' => 'info',
                }),              */
                Tables\Columns\SelectColumn::make('status')
                    ->options(self::$statuses),
                Tables\Columns\TextColumn::make('category.name'),
                Tables\Columns\TextColumn::make('tags.name')->badge(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(self::$statuses),
                Tables\Filters\SelectFilter::make('category')
                    ->relationship('category', 'name'),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from'),
                        Forms\Components\DatePicker::make('created_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\TagsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }


}
