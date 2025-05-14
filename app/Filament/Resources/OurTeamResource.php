<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OurTeamResource\Pages;
use App\Models\OurTeam;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OurTeamResource extends Resource
{
    protected static ?string $model = OurTeam::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Tim Kami';
    protected static ?string $navigationGroup = 'Landing Page';
    protected static ?string $pluralLabel = 'Tim Kami';
    protected static ?string $slug = 'tim-kami';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make('image')
                    ->image()
                    ->required()
                    ->directory('our-team-images'),
                Forms\Components\TextInput::make('nama')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('deskripsi')
                    ->nullable(),
                Forms\Components\TextInput::make('facebook_url')
                    ->label('Facebook URL')
                    ->url()
                    ->nullable()
                    ->maxLength(255),
                Forms\Components\TextInput::make('instagram_url')
                    ->label('Instagram URL')
                    ->url()
                    ->nullable()
                    ->maxLength(255),
                Forms\Components\TextInput::make('whatsapp_url')
                    ->label('WhatsApp URL')
                    ->url()
                    ->nullable()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')->label('Foto')->circular(),
                Tables\Columns\TextColumn::make('nama')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('deskripsi')->limit(50),
                Tables\Columns\TextColumn::make('facebook_url')->label('Facebook')->limit(30),
                Tables\Columns\TextColumn::make('instagram_url')->label('Instagram')->limit(30),
                Tables\Columns\TextColumn::make('whatsapp_url')->label('WhatsApp')->limit(30),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
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
            'index' => Pages\ListOurTeams::route('/'),
            'create' => Pages\CreateOurTeam::route('/create'),
            'edit' => Pages\EditOurTeam::route('/{record}/edit'),
        ];
    }
}