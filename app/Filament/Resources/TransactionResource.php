<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Category;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Transaction;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\TransactionResource\Pages;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->required(),
            Forms\Components\Select::make('category_id')
                ->relationship('category', 'name')
                ->searchable()
                ->preload()
                ->required(),
            Forms\Components\DatePicker::make('date')
                ->required(),
            Forms\Components\TextInput::make('amount')
                ->required()
                ->numeric(),
            Forms\Components\TextArea::make('note')
                ->rows(5)
                ->required(),
            Forms\Components\FileUpload::make('image')
                ->image()
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\ImageColumn::make('category.image')
                ->label('Gambar'),
            Tables\Columns\TextColumn::make('category.name')
                ->description(fn(Transaction $record): string => $record->name)
                ->label('Transaksi'),
            Tables\Columns\IconColumn::make('category.is_expense')
                ->boolean()
                ->label('Tipe')
                ->trueicon('heroicon-o-arrow-up-circle')
                ->falseicon('heroicon-o-arrow-down-circle')
                ->trueColor('danger')
                ->falseColor('success'),
            //     ->sortable(),
            Tables\Columns\TextColumn::make('date')
                ->date()
                ->label('Tanggal')
                ->sortable(),
            Tables\Columns\TextColumn::make('amount')
                ->numeric()
                ->money('IDR', locale: 'ID')
                ->sortable(),
            // Tables\Columns\TextColumn::make('note')
            //     ->searchable(),
            Tables\Columns\TextColumn::make('created_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            Tables\Columns\TextColumn::make('updated_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            Tables\Columns\TextColumn::make('deleted_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ])->filters([
            Tables\Filters\TrashedFilter::make(),
        ])->actions([
            Tables\Actions\ViewAction::make(),
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
            Tables\Actions\ForceDeleteAction::make(),
            Tables\Actions\RestoreAction::make(),
        ])->bulkActions([
            Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\ForceDeleteBulkAction::make(),
                Tables\Actions\RestoreBulkAction::make(),
            ]),
        ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }
}
