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
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Collection; 

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
                        SpatieMediaLibraryFileUpload::make('student_proof')
                            ->collection('student-proofs')
                            ->label('Kartu Pelajar/Mahasiswa')
                            ->acceptedFileTypes(['image/*', 'application/pdf']),
                        SpatieMediaLibraryFileUpload::make('twibbon_proof')
                            ->collection('twibbon-proofs')
                            ->label('Bukti Twibbon'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Nama Lengkap')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nisn')
                    ->label('NISN')
                    ->searchable(),
                
   
                Tables\Columns\TextColumn::make('teamMemberships.team.name')
                    ->label('Tim Terdaftar')
                    ->formatStateUsing(function ($state) {
                        if ($state instanceof Collection) {
                            return $state->implode(', ');
                        }
                        return $state;
                    })
                    ->limit(50),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('phone_number')
                    ->label('No. Telepon')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('competition')
                    ->label('Filter Berdasarkan Kompetisi')
                    ->relationship('teamMemberships.team.registration.competition', 'name')
                    ->searchable()
                    ->preload(),
            ])

            ->actions([
                Tables\Actions\EditAction::make(),
                
                ActionGroup::make([
                    Action::make('viewStudentProof')
                        ->label('Lihat Bukti Siswa')
                        ->icon('heroicon-o-eye')
                        ->modalContent(fn (Participant $record): \Illuminate\Contracts\View\View => 
                            view('filament.modals.view-media', ['media' => $record->getFirstMedia('student-proofs')]))
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel('Tutup')
                        ->visible(fn (Participant $record): bool => $record->hasMedia('student-proofs')),

                    Action::make('downloadStudentProof')
                        ->label('Unduh Bukti Siswa')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action(function (Participant $record) {
                            $mediaItem = $record->getFirstMedia('student-proofs');
                            return response()->download($mediaItem->getPath(), $mediaItem->file_name);
                        })
                        ->visible(fn (Participant $record): bool => $record->hasMedia('student-proofs')),
           
                    Action::make('viewTwibbonProof')
                        ->label('Lihat Bukti Twibbon')
                        ->icon('heroicon-o-photo')
                        ->modalContent(fn (Participant $record): \Illuminate\Contracts\View\View => 
                            view('filament.modals.view-media', ['media' => $record->getFirstMedia('twibbon-proofs')]))
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel('Tutup')
                        ->visible(fn (Participant $record): bool => $record->hasMedia('twibbon-proofs')),

                    Action::make('downloadTwibbonProof')
                        ->label('Unduh Bukti Twibbon')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action(function (Participant $record) {
                            $mediaItem = $record->getFirstMedia('twibbon-proofs');
                            return response()->download($mediaItem->getPath(), $mediaItem->file_name);
                        })
                        ->visible(fn (Participant $record): bool => $record->hasMedia('twibbon-proofs')),

                ])->label('Dokumen')->icon('heroicon-o-document-text'),
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
