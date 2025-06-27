<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ParticipantResource\Pages;
use App\Models\Participant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;

class ParticipantResource extends Resource
{
    protected static ?string $model = Participant::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static ?string $navigationGroup = 'Manajemen Pendaftaran';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Data Diri Peserta')
                    ->schema([
                        // Field-field dari model Participant Anda
                        Forms\Components\TextInput::make('full_name')
                            ->required()->label('Nama Lengkap'),
                        
                        Forms\Components\TextInput::make('nisn')
                            ->required()
                            ->numeric()
                            ->unique(ignoreRecord: true) 
                            ->label('NISN'),
                        
                        Forms\Components\TextInput::make('place_of_birth')
                            ->required()->label('Tempat Lahir'),
                        
                        Forms\Components\DatePicker::make('date_of_birth')
                            ->native(false) 
                            ->required()->label('Tanggal Lahir'),
                        
                        Forms\Components\TextInput::make('phone_number')
                            ->tel()
                            ->unique(ignoreRecord: true)
                            ->required()->label('Nomor Telepon'),
                        
                        Forms\Components\Textarea::make('address')
                            ->required()->label('Alamat')->columnSpanFull(),

                        SpatieMediaLibraryFileUpload::make('identity_card')
                            ->label('Kartu Identitas (Kartu Pelajar / KTP)')
                            ->collection('identity-cards') 
                            ->image() 
                            ->imageEditor() 
                            ->required(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('identity_card')
                    ->label('Kartu Identitas')
                    ->collection('identity-cards'),
                    
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Nama Lengkap')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('nisn')
                    ->label('NISN')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('phone_number')
                    ->label('No. Telepon'),
            ])
            ->filters([
         
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListParticipants::route('/'),
            'create' => Pages\CreateParticipant::route('/create'),
            'edit' => Pages\EditParticipant::route('/{record}/edit'),
        ];
    }    
}
