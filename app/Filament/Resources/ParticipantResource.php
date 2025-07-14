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
    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $navigationGroup = 'Manajemen Pendaftaran';
    protected static ?string $navigationLabel = 'Peserta';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Data Diri Peserta')
                    ->schema([
                        Forms\Components\TextInput::make('full_name')->required()->label('Nama Lengkap'),
                        Forms\Components\TextInput::make('email')->email()->required()->label('Email'),
                        Forms\Components\TextInput::make('nisn')->numeric()->required()->label('NISN'),
                        Forms\Components\TextInput::make('phone_number')->tel()->required()->label('No. Telepon'),
                        Forms\Components\TextInput::make('place_of_birth')->required()->label('Tempat Lahir'),
                        Forms\Components\DatePicker::make('date_of_birth')->native(false)->required()->label('Tanggal Lahir'),
                        Forms\Components\Textarea::make('address')->required()->label('Alamat')->columnSpanFull(),
                    ])->columns(2),
                Forms\Components\Section::make('Dokumen Peserta')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('studentproof')
                            ->collection('student-proofs')
                            ->label('Kartu Pelajar/Mahasiswa'),
                        SpatieMediaLibraryFileUpload::make('twibbon_proof')
                            ->collection('twibbon-proofs')
                            ->label('Bukti Twibbon'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('full_name')->label('Nama Lengkap')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('email')->label('Email')->searchable(),
                Tables\Columns\TextColumn::make('nisn')->label('NISN'),
                SpatieMediaLibraryImageColumn::make('student_proofs')->collection('student-proofs')->label('Dokumen Bukti Siswa'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
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